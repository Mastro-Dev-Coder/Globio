<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\WatchLater;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user()->load(['userProfile', 'premiumSubscriptions']);
        $profile = $user->userProfile;
        $premiumSubscription = $user->activePremiumSubscription();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'premium' => [
                'active' => $user->hasActivePremium(),
                'premium_access_ends_at' => optional($user->premium_access_ends_at)?->toIso8601String(),
                'plan' => $premiumSubscription ? [
                    'id' => $premiumSubscription->id,
                    'plan_code' => $premiumSubscription->plan_code,
                    'plan_name' => $premiumSubscription->plan_name,
                    'status' => $premiumSubscription->status,
                    'billing_interval' => $premiumSubscription->billing_interval,
                    'amount' => (int) $premiumSubscription->amount,
                    'currency' => $premiumSubscription->currency,
                    'current_period_end' => optional($premiumSubscription->current_period_end)?->toIso8601String(),
                    'cancel_at_period_end' => (bool) $premiumSubscription->cancel_at_period_end,
                ] : null,
                'features' => $user->premiumCapabilities(),
            ],
            'profile' => [
                'username' => $profile?->username,
                'channel_name' => $profile?->channel_name ?? $user->name,
                'channel_description' => $profile?->channel_description,
                'avatar_url' => $this->mediaUrl($profile?->avatar_url),
                'banner_url' => $this->mediaUrl($profile?->banner_url),
                'country' => $profile?->country,
                'subscriber_count' => (int) ($profile?->subscriber_count ?? 0),
                'video_count' => (int) ($profile?->video_count ?? 0),
                'total_views' => (int) ($profile?->total_views ?? 0),
            ],
        ]);
    }

    public function updateMe(Request $request)
    {
        $user = $request->user()->load('userProfile');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'channel_name' => ['nullable', 'string', 'max:120'],
            'channel_description' => ['nullable', 'string', 'max:2000'],
            'country' => ['nullable', 'string', 'max:10'],
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        if (!$user->userProfile) {
            $user->userProfile()->create([
                'username' => $this->generateUsername($validated['name'] ?? $user->name),
                'channel_name' => $validated['channel_name'] ?? $user->name,
                'channel_description' => $validated['channel_description'] ?? '',
                'is_channel_enabled' => true,
            ]);
            $user->refresh();
            $user->load('userProfile');
        }

        $profileData = [];
        foreach (['channel_name', 'channel_description', 'country'] as $field) {
            if (array_key_exists($field, $validated)) {
                $profileData[$field] = $validated[$field];
            }
        }

        if (!empty($profileData)) {
            $user->userProfile->update($profileData);
        }

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $this->me($request)->getData(true),
        ]);
    }

    public function watchLater()
    {
        $userId = Auth::id();

        $items = WatchLater::with(['video.user.userProfile'])
            ->where('user_id', $userId)
            ->latest('added_at')
            ->get()
            ->map(function (WatchLater $item) {
                return $this->serializeVideo($item->video);
            })
            ->filter()
            ->values();

        return response()->json(['data' => $items]);
    }

    public function addToWatchLater(string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        WatchLater::updateOrCreate([
            'user_id' => Auth::id(),
            'video_id' => $videoModel->id,
        ], [
            'added_at' => now(),
        ]);

        return response()->json([
            'message' => 'Video added to watch later.',
        ]);
    }

    public function removeFromWatchLater(string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        WatchLater::where('user_id', Auth::id())
            ->where('video_id', $videoModel->id)
            ->delete();

        return response()->json([
            'message' => 'Video removed from watch later.',
        ]);
    }

    public function watchHistory()
    {
        $items = WatchHistory::with(['video.user.userProfile'])
            ->where('user_id', Auth::id())
            ->orderByDesc('last_watched_at')
            ->limit(100)
            ->get()
            ->map(function (WatchHistory $history) {
                $video = $this->serializeVideo($history->video);
                if (!$video) {
                    return null;
                }

                $video['progress'] = [
                    'watched_duration' => (int) $history->watched_duration,
                    'total_duration' => (int) $history->total_duration,
                    'completion_percentage' => (float) $history->completion_percentage,
                    'completed' => (bool) $history->completed,
                    'last_watched_at' => optional($history->last_watched_at)?->toIso8601String(),
                ];

                return $video;
            })
            ->filter()
            ->values();

        return response()->json(['data' => $items]);
    }

    public function upsertWatchHistory(Request $request, string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        $validated = $request->validate([
            'watched_duration' => ['required', 'integer', 'min:0'],
            'total_duration' => ['nullable', 'integer', 'min:0'],
            'completed' => ['nullable', 'boolean'],
        ]);

        $totalDuration = (int) ($validated['total_duration'] ?? $videoModel->duration ?? 0);
        $completed = array_key_exists('completed', $validated)
            ? (bool) $validated['completed']
            : ($totalDuration > 0 && (int) $validated['watched_duration'] >= $totalDuration);

        WatchHistory::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'video_id' => $videoModel->id,
            ],
            [
                'watched_duration' => (int) $validated['watched_duration'],
                'total_duration' => $totalDuration,
                'completed' => $completed,
                'last_watched_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Watch history updated.',
        ]);
    }

    public function playlists()
    {
        $playlists = Playlist::withCount('videos')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function (Playlist $playlist) {
                return [
                    'id' => $playlist->id,
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'thumbnail_url' => $playlist->dynamic_thumbnail_url,
                    'is_public' => (bool) $playlist->is_public,
                    'video_count' => (int) $playlist->videos_count,
                    'views_count' => (int) $playlist->views_count,
                    'created_at' => optional($playlist->created_at)?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $playlists]);
    }

    public function notifications()
    {
        $user = Auth::user();

        $databaseNotifications = $user->notifications()
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->serializeDatabaseNotification($notification));

        $appNotifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Notification $notification) => $this->serializeAppNotification($notification));

        $notifications = $databaseNotifications
            ->concat($appNotifications)
            ->sortByDesc('created_at')
            ->take(100)
            ->values();

        return response()->json([
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count()
                + Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    }

    public function readNotification(string $id)
    {
        $user = Auth::user();

        if (str_starts_with($id, 'app:')) {
            $id = substr($id, 4);
        }

        if (str_starts_with($id, 'db:')) {
            $id = substr($id, 3);
        }

        $databaseNotification = $user->notifications()->where('id', $id)->first();
        if ($databaseNotification) {
            $databaseNotification->markAsRead();

            return response()->json([
                'message' => 'Notification marked as read.',
            ]);
        }

        $appNotification = Notification::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $appNotification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
        ]);
    }

    public function readAllNotifications()
    {
        $user = Auth::user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read.',
        ]);
    }

    private function serializeDatabaseNotification(DatabaseNotification $notification): array
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? class_basename($notification->type);
        $title = $data['title'] ?? $data['post_title'] ?? $this->notificationTitle($type);
        $message = $data['message'] ?? $data['excerpt'] ?? null;
        $actionUrl = $notification->url ?? $data['url'] ?? $data['action_url'] ?? $data['__action_url'] ?? null;

        return [
            'id' => (string) $notification->id,
            'source' => 'database',
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'data' => $data,
            'read_at' => optional($notification->read_at)?->toIso8601String(),
            'created_at' => optional($notification->created_at)?->toIso8601String(),
        ];
    }

    private function serializeAppNotification(Notification $notification): array
    {
        return [
            'id' => (string) $notification->id,
            'source' => 'app',
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'action_url' => $notification->action_url,
            'data' => [],
            'read_at' => optional($notification->read_at)?->toIso8601String(),
            'created_at' => optional($notification->created_at)?->toIso8601String(),
        ];
    }

    private function notificationTitle(string $type): string
    {
        return match ($type) {
            'new_comment' => 'New comment',
            'new_like' => 'New like',
            'new_subscriber' => 'New subscriber',
            'new_video_share' => 'New share',
            'report_assigned' => 'Report assigned',
            'creator_feedback' => 'Creator feedback',
            default => Str::headline($type),
        };
    }

    private function resolveVideo(string $video): ?Video
    {
        return Video::with(['user.userProfile'])
            ->where('status', 'published')
            ->where('is_public', true)
            ->where(function ($query) use ($video) {
                $query->where('video_url', $video);
                if (is_numeric($video)) {
                    $query->orWhere('id', (int) $video);
                }
            })
            ->first();
    }

    private function serializeVideo(?Video $video): ?array
    {
        if (!$video) {
            return null;
        }

        $profile = $video->user?->userProfile;

        return [
            'id' => $video->id,
            'slug' => $video->video_url,
            'title' => $video->title,
            'thumbnail_url' => $video->thumbnail_url,
            'video_url' => $video->video_file_url,
            'duration' => (int) $video->duration,
            'views_count' => (int) $video->views_count,
            'likes_count' => (int) $video->likes_count,
            'comments_count' => (int) $video->comments_count,
            'is_reel' => (bool) $video->is_reel,
            'published_at' => optional($video->published_at)?->toIso8601String(),
            'creator' => [
                'id' => $video->user?->id,
                'name' => $profile?->channel_name ?? $video->user?->name,
                'username' => $profile?->username,
                'avatar_url' => $this->mediaUrl($profile?->avatar_url),
            ],
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }

    private function generateUsername(string $name): string
    {
        $base = Str::slug($name, '-');
        $base = $base !== '' ? $base : 'user';
        $candidate = $base;
        $i = 1;

        while (\App\Models\UserProfile::where('username', $candidate)->exists()) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}
