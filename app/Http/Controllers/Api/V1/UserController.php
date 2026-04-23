<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\Subscription;
use App\Models\UserProfile;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\WatchLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function myChannel(Request $request)
    {
        $user = $request->user()->load('userProfile');
        $profile = $user->userProfile;

        if (!$profile) {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'username' => $this->generateUsername($user->name),
                'channel_name' => $user->name,
                'is_channel_enabled' => true,
            ]);
            $user->load('userProfile');
        }

        $subscriberCount = Subscription::where('channel_id', $user->id)->count();
        $videoCount = Video::where('user_id', $user->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->count();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => optional($user->created_at)?->toIso8601String(),
            'profile' => [
                'username' => $profile->username,
                'channel_name' => $profile->channel_name ?? $user->name,
                'channel_description' => $profile->channel_description,
                'avatar_url' => $this->mediaUrl($profile->avatar_url),
                'banner_url' => $this->mediaUrl($profile->banner_url),
                'country' => $profile->country,
                'social_links' => $profile->social_links ?? [],
                'is_verified' => (bool) $profile->is_verified,
                'subscriber_count' => (int) ($profile->subscriber_count ?? $subscriberCount),
                'video_count' => (int) ($profile->video_count ?? $videoCount),
                'total_views' => (int) ($profile->total_views ?? 0),
            ],
        ]);
    }

    public function updateChannel(Request $request)
    {
        $user = $request->user();
        $profile = $user->userProfile;

        $validated = $request->validate([
            'channel_name' => ['nullable', 'string', 'max:120'],
            'channel_description' => ['nullable', 'string', 'max:2000'],
            'country' => ['nullable', 'string', 'max:10'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url'],
        ]);

        if (!$profile) {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'username' => $this->generateUsername($validated['channel_name'] ?? $user->name),
                'channel_name' => $validated['channel_name'] ?? $user->name,
                'channel_description' => $validated['channel_description'] ?? '',
                'is_channel_enabled' => true,
            ]);
        } else {
            $updateData = [];
            if (isset($validated['channel_name'])) {
                $updateData['channel_name'] = $validated['channel_name'];
            }
            if (isset($validated['channel_description'])) {
                $updateData['channel_description'] = $validated['channel_description'];
            }
            if (isset($validated['country'])) {
                $updateData['country'] = $validated['country'];
            }
            if (isset($validated['social_links'])) {
                $updateData['social_links'] = $validated['social_links'];
            }

            if (!empty($updateData)) {
                $profile->update($updateData);
            }
        }

        return response()->json([
            'message' => 'Channel updated successfully.',
            'profile' => [
                'username' => $profile->username,
                'channel_name' => $profile->channel_name,
                'channel_description' => $profile->channel_description,
                'country' => $profile->country,
                'social_links' => $profile->social_links ?? [],
            ],
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $profile = $user->userProfile;

        if (!$profile) {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'username' => $this->generateUsername($user->name),
                'channel_name' => $user->name,
                'is_channel_enabled' => true,
            ]);
        }

        if ($profile->avatar_url && Storage::disk('public')->exists($profile->avatar_url)) {
            Storage::disk('public')->delete($profile->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $profile->update(['avatar_url' => $path]);

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'avatar_url' => $this->mediaUrl($path),
        ]);
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $user = $request->user();
        $profile = $user->userProfile;

        if (!$profile) {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'username' => $this->generateUsername($user->name),
                'channel_name' => $user->name,
                'is_channel_enabled' => true,
            ]);
        }

        if ($profile->banner_url && Storage::disk('public')->exists($profile->banner_url)) {
            Storage::disk('public')->delete($profile->banner_url);
        }

        $path = $request->file('banner')->store('banners', 'public');
        $profile->update(['banner_url' => $path]);

        return response()->json([
            'message' => 'Banner updated successfully.',
            'banner_url' => $this->mediaUrl($path),
        ]);
    }

    public function mySubscriptions(Request $request)
    {
        $subscriptions = Subscription::with(['channel.userProfile'])
            ->where('subscriber_id', Auth::id())
            ->latest('created_at')
            ->get()
            ->map(function ($sub) {
                $channel = $sub->channel;
                $profile = $channel?->userProfile;
                return [
                    'id' => $channel->id,
                    'name' => $profile?->channel_name ?? $channel->name,
                    'username' => $profile?->username,
                    'avatar_url' => $this->mediaUrl($profile?->avatar_url),
                    'is_verified' => (bool) ($profile?->is_verified ?? false),
                    'subscribed_at' => optional($sub->created_at)?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $subscriptions]);
    }

    public function mySubscribers(Request $request)
    {
        $subscribers = Subscription::with(['subscriber.userProfile'])
            ->where('channel_id', Auth::id())
            ->latest('created_at')
            ->get()
            ->map(function ($sub) {
                $user = $sub->subscriber;
                $profile = $user?->userProfile;
                return [
                    'id' => $user->id,
                    'name' => $profile?->channel_name ?? $user->name,
                    'username' => $profile?->username,
                    'avatar_url' => $this->mediaUrl($profile?->avatar_url),
                    'subscribed_at' => optional($sub->created_at)?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $subscribers]);
    }

    public function myVideos(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:all,video,reel'],
            'status' => ['nullable', 'in:all,published,draft,processing,pending_approval'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Video::with(['user.userProfile'])
            ->where('user_id', Auth::id());

        $type = $validated['type'] ?? 'all';
        if ($type === 'video') {
            $query->where('is_reel', false);
        } elseif ($type === 'reel') {
            $query->where('is_reel', true);
        }

        $status = $validated['status'] ?? 'all';
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $paginated = $query->latest('created_at')->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'data' => collect($paginated->items())->map(function ($video) {
                return $this->serializeVideo($video);
            }),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function myLikedVideos(Request $request)
    {
        $likes = Like::with(['likeable.user.userProfile'])
            ->where('user_id', Auth::id())
            ->where('likeable_type', Video::class)
            ->where('reaction', 'like')
            ->latest('created_at')
            ->get()
            ->map(function ($like) {
                $video = $like->likeable;
                if (!$video) return null;
                return $this->serializeVideo($video);
            })
            ->filter()
            ->values();

        return response()->json(['data' => $likes]);
    }

    public function deleteVideo(Request $request, Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $video->delete();
            return response()->json(['message' => 'Video deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting video: ' . $e->getMessage()], 500);
        }
    }

    public function settings(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'email_notifications' => true,
            'push_notifications' => true,
            'comment_notifications' => true,
            'like_notifications' => true,
            'new_subscriber_notifications' => true,
            'marketing_emails' => false,
            'private_profile' => (bool) ($user->userProfile?->is_private ?? false),
            'country' => $user->userProfile?->country,
            'language' => 'en',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => ['nullable', 'boolean'],
            'push_notifications' => ['nullable', 'boolean'],
            'comment_notifications' => ['nullable', 'boolean'],
            'like_notifications' => ['nullable', 'boolean'],
            'new_subscriber_notifications' => ['nullable', 'boolean'],
            'marketing_emails' => ['nullable', 'boolean'],
            'private_profile' => ['nullable', 'boolean'],
            'country' => ['nullable', 'string', 'max:10'],
            'language' => ['nullable', 'string', 'size:2'],
        ]);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'settings' => $validated,
        ]);
    }

    public function likeComment(Request $request, Comment $comment)
    {
        $existing = Like::where('likeable_type', Comment::class)
            ->where('likeable_id', $comment->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
            $comment->decrement('likes_count');
            $liked = false;
        } else {
            Like::create([
                'likeable_type' => Comment::class,
                'likeable_id' => $comment->id,
                'user_id' => Auth::id(),
                'type' => 'like',
                'reaction' => 'like',
            ]);
            $comment->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $comment->fresh()->likes_count,
        ]);
    }

    public function deleteComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    public function myComments(Request $request)
    {
        $comments = Comment::with(['video.user.userProfile'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'status' => $comment->status,
                    'created_at' => optional($comment->created_at)?->toIso8601String(),
                    'video' => [
                        'id' => $comment->video?->id,
                        'title' => $comment->video?->title,
                        'thumbnail_url' => $comment->video?->thumbnail_url,
                    ],
                ];
            });

        return response()->json(['data' => $comments]);
    }

    private function serializeVideo(Video $video): array
    {
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
            'is_public' => (bool) $video->is_public,
            'status' => $video->status,
            'published_at' => optional($video->published_at)?->toIso8601String(),
            'created_at' => optional($video->created_at)?->toIso8601String(),
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
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/')) return asset($cleanPath);
        return asset('storage/' . $cleanPath);
    }

    private function generateUsername(string $name): string
    {
        $base = Str::slug($name, '-');
        $base = $base !== '' ? $base : 'user';
        $candidate = $base;
        $i = 1;

        while (UserProfile::where('username', $candidate)->exists()) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}