<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Video;
use App\Models\WatchLater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function appConfig()
    {
        $billing = app(\App\Services\StripeBillingService::class);
        $premiumPlan = $billing->premiumPlan();

        return response()->json([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'api_version' => 'v1',
            'features' => [
                'auth' => true,
                'videos' => true,
                'reels' => true,
                'comments' => true,
                'likes' => true,
                'watch_later' => true,
                'subscriptions' => true,
                'premium' => true,
                'playlist_management' => true,
            ],
            'premium' => [
                'plan' => $premiumPlan,
                'customer_portal_enabled' => $billing->isConfigured(),
            ],
        ]);
    }

    public function home(Request $request)
    {
        $limit = (int) $request->integer('limit', 12);
        $limit = min(max($limit, 4), 24);

        $baseQuery = $this->publishedVideosQuery();

        $featured = (clone $baseQuery)
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit($limit)
            ->get();

        $trending = (clone $baseQuery)
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();

        $latest = (clone $baseQuery)
            ->latest('published_at')
            ->limit($limit)
            ->get();

        $reels = (clone $baseQuery)
            ->where('is_reel', true)
            ->latest('published_at')
            ->limit($limit)
            ->get();

        $creators = User::with('userProfile')
            ->whereHas('videos', function (Builder $query) {
                $query->where('status', 'published')->where('is_public', true);
            })
            ->withCount([
                'videos as published_videos_count' => function (Builder $query) {
                    $query->where('status', 'published')->where('is_public', true);
                },
                'subscribers',
            ])
            ->orderByDesc('subscribers_count')
            ->limit(12)
            ->get();

        return response()->json([
            'featured' => $featured->map(fn (Video $video) => $this->serializeVideo($video)),
            'trending' => $trending->map(fn (Video $video) => $this->serializeVideo($video)),
            'latest' => $latest->map(fn (Video $video) => $this->serializeVideo($video)),
            'reels' => $reels->map(fn (Video $video) => $this->serializeVideo($video)),
            'creators' => $creators->map(fn (User $creator) => $this->serializeCreator($creator)),
        ]);
    }

    public function videos(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:all,video,reel'],
            'sort' => ['nullable', 'in:latest,trending,popular'],
            'q' => ['nullable', 'string', 'max:200'],
            'creator' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = $this->publishedVideosQuery();

        $type = $validated['type'] ?? 'all';
        if ($type === 'video') {
            $query->where('is_reel', false);
        } elseif ($type === 'reel') {
            $query->where('is_reel', true);
        }

        if (!empty($validated['q'])) {
            $term = '%' . $validated['q'] . '%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('tags', 'like', $term);
            });
        }

        if (!empty($validated['creator'])) {
            $creator = $this->resolveCreator($validated['creator']);
            if ($creator) {
                $query->where('user_id', $creator->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $sort = $validated['sort'] ?? 'latest';
        if ($sort === 'trending' || $sort === 'popular') {
            $query->orderByDesc('views_count')->orderByDesc('likes_count');
        } else {
            $query->latest('published_at');
        }

        $perPage = (int) ($validated['per_page'] ?? 20);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => collect($paginated->items())->map(fn (Video $video) => $this->serializeVideo($video)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function showVideo(string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        $related = $this->publishedVideosQuery()
            ->where('id', '!=', $videoModel->id)
            ->where('is_reel', $videoModel->is_reel)
            ->orderByDesc('views_count')
            ->limit(12)
            ->get();

        return response()->json([
            'video' => $this->serializeVideo($videoModel, true),
            'related' => $related->map(fn (Video $item) => $this->serializeVideo($item)),
        ]);
    }

    public function videoComments(string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        $comments = Comment::with(['user.userProfile', 'replies.user.userProfile'])
            ->where('video_id', $videoModel->id)
            ->whereNull('parent_id')
            ->where('status', Comment::STATUS_APPROVED)
            ->latest()
            ->get();

        return response()->json([
            'data' => $comments->map(function (Comment $comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => optional($comment->created_at)?->toIso8601String(),
                    'author' => $this->serializeCreator($comment->user),
                    'replies' => $comment->replies
                        ->where('status', Comment::STATUS_APPROVED)
                        ->map(function (Comment $reply) {
                            return [
                                'id' => $reply->id,
                                'content' => $reply->content,
                                'created_at' => optional($reply->created_at)?->toIso8601String(),
                                'author' => $this->serializeCreator($reply->user),
                            ];
                        })->values(),
                ];
            }),
        ]);
    }

    public function creators(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = User::with('userProfile')
            ->whereHas('userProfile');

        if (!empty($validated['q'])) {
            $term = '%' . $validated['q'] . '%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('name', 'like', $term)
                    ->orWhereHas('userProfile', function (Builder $profileQuery) use ($term) {
                        $profileQuery->where('channel_name', 'like', $term)
                            ->orWhere('username', 'like', $term);
                    });
            });
        }

        $query->withCount([
            'videos as published_videos_count' => function (Builder $videoQuery) {
                $videoQuery->where('status', 'published')->where('is_public', true);
            },
            'subscribers',
        ])->orderByDesc('subscribers_count');

        $paginated = $query->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'data' => collect($paginated->items())->map(fn (User $creator) => $this->serializeCreator($creator)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function showCreator(string $creator)
    {
        $creatorModel = $this->resolveCreator($creator);
        abort_if(!$creatorModel, 404, 'Creator not found.');

        $latestVideos = $this->publishedVideosQuery()
            ->where('user_id', $creatorModel->id)
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()->json([
            'creator' => $this->serializeCreator($creatorModel, true),
            'videos' => $latestVideos->map(fn (Video $video) => $this->serializeVideo($video)),
        ]);
    }

    public function creatorVideos(string $creator, Request $request)
    {
        $creatorModel = $this->resolveCreator($creator);
        abort_if(!$creatorModel, 404, 'Creator not found.');

        $validated = $request->validate([
            'type' => ['nullable', 'in:all,video,reel'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = $this->publishedVideosQuery()->where('user_id', $creatorModel->id);

        $type = $validated['type'] ?? 'all';
        if ($type === 'video') {
            $query->where('is_reel', false);
        } elseif ($type === 'reel') {
            $query->where('is_reel', true);
        }

        $paginated = $query->latest('published_at')->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'creator' => $this->serializeCreator($creatorModel),
            'data' => collect($paginated->items())->map(fn (Video $video) => $this->serializeVideo($video)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);
        $term = '%' . $validated['q'] . '%';

        $videos = $this->publishedVideosQuery()
            ->where(function (Builder $query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('tags', 'like', $term);
            })
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();

        $creators = User::with('userProfile')
            ->whereHas('userProfile', function (Builder $query) use ($term) {
                $query->where('channel_name', 'like', $term)
                    ->orWhere('username', 'like', $term);
            })
            ->withCount('subscribers')
            ->orderByDesc('subscribers_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'videos' => $videos->map(fn (Video $video) => $this->serializeVideo($video)),
            'creators' => $creators->map(fn (User $creator) => $this->serializeCreator($creator)),
            'total_results' => $videos->count() + $creators->count(),
        ]);
    }

    public function addComment(Request $request, string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $comment = Comment::create([
            'video_id' => $videoModel->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Comment created.',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'status' => $comment->status,
                'created_at' => optional($comment->created_at)?->toIso8601String(),
                'author' => $this->serializeCreator($request->user()),
            ],
        ], 201);
    }

    public function reactToVideo(Request $request, string $video)
    {
        $videoModel = $this->resolveVideo($video);
        abort_if(!$videoModel, 404, 'Video not found.');

        $validated = $request->validate([
            'reaction' => ['required', 'in:like,dislike,none'],
        ]);

        $userId = Auth::id();
        $existing = Like::where('likeable_type', Video::class)
            ->where('likeable_id', $videoModel->id)
            ->where('user_id', $userId)
            ->first();

        $reaction = $validated['reaction'];
        if ($reaction === 'none') {
            if ($existing) {
                $existing->delete();
            }
        } elseif ($existing) {
            $existing->update([
                'reaction' => $reaction,
                'type' => $reaction,
            ]);
        } else {
            Like::create([
                'likeable_type' => Video::class,
                'likeable_id' => $videoModel->id,
                'user_id' => $userId,
                'reaction' => $reaction,
                'type' => $reaction,
            ]);
        }

        $videoModel->refresh();
        $likesCount = $videoModel->likes()->where('reaction', 'like')->count();
        $dislikesCount = $videoModel->likes()->where('reaction', 'dislike')->count();
        $videoModel->update([
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
        ]);

        return response()->json([
            'message' => 'Reaction updated.',
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'user_reaction' => $reaction === 'none' ? null : $reaction,
        ]);
    }

    public function toggleCreatorSubscription(string $creator)
    {
        $creatorModel = $this->resolveCreator($creator);
        abort_if(!$creatorModel, 404, 'Creator not found.');

        $currentUser = request()->user();

        if ($currentUser->id === $creatorModel->id) {
            return response()->json([
                'message' => 'You cannot subscribe to yourself.',
            ], 422);
        }

        $subscription = Subscription::where('subscriber_id', $currentUser->id)
            ->where('channel_id', $creatorModel->id)
            ->first();

        if ($subscription) {
            $subscription->delete();
            $isSubscribed = false;
        } else {
            Subscription::create([
                'subscriber_id' => $currentUser->id,
                'channel_id' => $creatorModel->id,
            ]);
            $isSubscribed = true;
        }

        $count = Subscription::where('channel_id', $creatorModel->id)->count();
        if ($creatorModel->userProfile) {
            $creatorModel->userProfile->update(['subscriber_count' => $count]);
        }

        return response()->json([
            'message' => $isSubscribed ? 'Subscribed.' : 'Unsubscribed.',
            'is_subscribed' => $isSubscribed,
            'subscriber_count' => $count,
        ]);
    }

    private function publishedVideosQuery(): Builder
    {
        return Video::with(['user.userProfile'])
            ->where('status', 'published')
            ->where('is_public', true);
    }

    private function resolveVideo(string $video): ?Video
    {
        return $this->publishedVideosQuery()
            ->where(function (Builder $query) use ($video) {
                $query->where('video_url', $video);
                if (is_numeric($video)) {
                    $query->orWhere('id', (int) $video);
                }
            })
            ->first();
    }

    private function resolveCreator(string $creator): ?User
    {
        $query = User::with('userProfile');

        if (is_numeric($creator)) {
            return $query->where('id', (int) $creator)->first();
        }

        return $query->whereHas('userProfile', function (Builder $profileQuery) use ($creator) {
            $profileQuery->where('username', $creator)
                ->orWhere('channel_name', $creator)
                ->orWhereRaw("LOWER(REPLACE(channel_name, ' ', '-')) = ?", [Str::lower($creator)]);
        })->first();
    }

    private function serializeVideo(Video $video, bool $includeDescription = false): array
    {
        $user = $video->user;
        $profile = $user?->userProfile;
        $currentUserId = Auth::id();

        $isInWatchLater = false;
        $userReaction = null;
        if ($currentUserId) {
            $isInWatchLater = WatchLater::where('user_id', $currentUserId)->where('video_id', $video->id)->exists();
            $userReaction = Like::where('likeable_type', Video::class)
                ->where('likeable_id', $video->id)
                ->where('user_id', $currentUserId)
                ->value('reaction');
        }

        $payload = [
            'id' => $video->id,
            'slug' => $video->video_url,
            'title' => $video->title,
            'thumbnail_url' => $video->thumbnail_url,
            'video_url' => $video->video_file_url,
            'duration' => (int) $video->duration,
            'formatted_duration' => $video->formatted_duration,
            'views_count' => (int) $video->views_count,
            'likes_count' => (int) $video->likes_count,
            'dislikes_count' => (int) $video->dislikes_count,
            'comments_count' => (int) $video->comments_count,
            'is_reel' => (bool) $video->is_reel,
            'published_at' => optional($video->published_at)?->toIso8601String(),
            'creator' => [
                'id' => $user?->id,
                'name' => $profile?->channel_name ?? $user?->name,
                'username' => $profile?->username,
                'avatar_url' => $this->mediaUrl($profile?->avatar_url),
                'is_verified' => (bool) ($profile?->is_verified ?? false),
            ],
            'user_state' => [
                'is_in_watch_later' => $isInWatchLater,
                'reaction' => $userReaction,
            ],
            'premium_features' => [
                'ad_free_supported' => true,
                'background_playback_supported' => true,
                'picture_in_picture_supported' => true,
                'higher_quality_streaming_supported' => !$video->is_reel,
                'enhanced_reels_controls_supported' => (bool) $video->is_reel,
            ],
        ];

        if ($includeDescription) {
            $payload['description'] = $video->description;
            $payload['tags'] = $video->tags ?? [];
        }

        return $payload;
    }

    private function serializeCreator(User $creator, bool $includeStats = false): array
    {
        $profile = $creator->userProfile;

        $subscriberCount = $profile?->subscriber_count;
        if ($subscriberCount === null) {
            $subscriberCount = Subscription::where('channel_id', $creator->id)->count();
        }

        $publishedVideosCount = $profile?->video_count;
        if ($publishedVideosCount === null) {
            $publishedVideosCount = $creator->videos()
                ->where('status', 'published')
                ->where('is_public', true)
                ->count();
        }

        $currentUserId = Auth::id();
        $isSubscribed = false;
        if ($currentUserId) {
            $isSubscribed = Subscription::where('subscriber_id', $currentUserId)
                ->where('channel_id', $creator->id)
                ->exists();
        }

        $payload = [
            'id' => $creator->id,
            'name' => $profile?->channel_name ?? $creator->name,
            'username' => $profile?->username,
            'avatar_url' => $this->mediaUrl($profile?->avatar_url),
            'banner_url' => $this->mediaUrl($profile?->banner_url),
            'is_verified' => (bool) ($profile?->is_verified ?? false),
            'subscriber_count' => (int) $subscriberCount,
            'video_count' => (int) $publishedVideosCount,
            'is_subscribed' => $isSubscribed,
        ];

        if ($includeStats) {
            $payload['bio'] = $profile?->channel_description;
            $payload['total_views'] = (int) ($profile?->total_views ?? 0);
            $payload['social_links'] = $profile?->social_links ?? [];
            $payload['country'] = $profile?->country;
        }

        return $payload;
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
}
