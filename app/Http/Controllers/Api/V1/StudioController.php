<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChannelAnalytics;
use App\Models\Comment;
use App\Models\CreatorFeedback;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioController extends Controller
{
    public function summary(Request $request)
    {
        $userId = $request->user()->id;
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $stats = ChannelAnalytics::getChannelStats($userId, $startDate, $endDate);

        $videoQuery = Video::where('user_id', $userId);
        $publishedVideoIds = (clone $videoQuery)->where('status', 'published')->pluck('id');

        return response()->json([
            'range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'totals' => [
                'videos' => (clone $videoQuery)->count(),
                'published_videos' => $publishedVideoIds->count(),
                'drafts' => (clone $videoQuery)->where('status', 'draft')->count(),
                'processing' => (clone $videoQuery)->where('status', 'processing')->count(),
                'subscribers' => Subscription::where('channel_id', $userId)->count(),
                'views' => (int) ($stats->total_views ?? 0),
                'likes' => (int) ($stats->total_likes ?? 0),
                'comments' => (int) ($stats->total_comments ?? 0),
                'shares' => (int) ($stats->total_shares ?? 0),
                'watch_time_minutes' => (float) ($stats->total_watch_time ?? 0),
                'average_watch_duration' => (float) ($stats->avg_watch_duration ?? 0),
                'pending_comments' => Comment::whereIn('video_id', $publishedVideoIds)->where('status', Comment::STATUS_PENDING)->count(),
                'open_reports' => Report::where('reported_user_id', $userId)
                    ->whereIn('status', [Report::STATUS_PENDING, Report::STATUS_REVIEWED, Report::STATUS_ESCALATED])
                    ->count(),
                'unread_feedback' => CreatorFeedback::where('creator_id', $userId)->where('is_read', false)->count(),
            ],
            'recent_videos' => Video::with('user.userProfile')
                ->where('user_id', $userId)
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (Video $video) => $this->serializeVideo($video)),
        ]);
    }

    public function analytics(Request $request)
    {
        $userId = $request->user()->id;
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $startDate = $validated['start_date'] ?? now()->subDays(30)->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();
        $limit = (int) ($validated['limit'] ?? 10);

        $daily = ChannelAnalytics::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(views) as views, SUM(likes) as likes, SUM(comments) as comments, SUM(shares) as shares, SUM(watch_time_minutes) as watch_time_minutes')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => optional($row->date)?->toDateString() ?? (string) $row->date,
                'views' => (int) $row->views,
                'likes' => (int) $row->likes,
                'comments' => (int) $row->comments,
                'shares' => (int) $row->shares,
                'watch_time_minutes' => (float) $row->watch_time_minutes,
            ]);

        $stats = ChannelAnalytics::getChannelStats($userId, $startDate, $endDate);
        $topVideos = ChannelAnalytics::getTopVideos($userId, $limit, $startDate, $endDate)
            ->map(fn ($row) => $this->serializeTopVideo($row));
        $demographics = ChannelAnalytics::getDemographics($userId, $startDate, $endDate);

        return response()->json([
            'range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'totals' => [
                'views' => (int) ($stats->total_views ?? 0),
                'likes' => (int) ($stats->total_likes ?? 0),
                'comments' => (int) ($stats->total_comments ?? 0),
                'shares' => (int) ($stats->total_shares ?? 0),
                'watch_time_minutes' => (float) ($stats->total_watch_time ?? 0),
                'average_watch_duration' => (float) ($stats->avg_watch_duration ?? 0),
            ],
            'daily' => $daily,
            'top_videos' => $topVideos,
            'traffic_sources' => ChannelAnalytics::getTrafficSources($userId, $startDate, $endDate),
            'demographics' => [
                'countries' => $demographics['countries'],
                'devices' => $demographics['devices'],
            ],
        ]);
    }

    public function videoAnalytics(Request $request, string $video)
    {
        $video = $this->resolveOwnedVideo($video, $request->user()->id);
        abort_if(!$video, 404, 'Video not found.');

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $startDate = $validated['start_date'] ?? now()->subDays(30)->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();

        return response()->json([
            'video' => $this->serializeVideo($video),
            'range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'daily' => ChannelAnalytics::getVideoStats($video->id, $startDate, $endDate)
                ->map(fn ($row) => [
                    'date' => optional($row->date)?->toDateString() ?? (string) $row->date,
                    'views' => (int) $row->daily_views,
                    'likes' => (int) $row->daily_likes,
                    'comments' => (int) $row->daily_comments,
                    'shares' => (int) $row->daily_shares,
                    'watch_time_minutes' => (float) $row->daily_watch_time,
                ]),
        ]);
    }

    public function community(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:all,approved,pending,rejected,hidden'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Comment::with(['user.userProfile', 'video'])
            ->whereHas('video', fn (Builder $videoQuery) => $videoQuery->where('user_id', Auth::id()));

        if (($validated['status'] ?? 'all') !== 'all') {
            $query->where('status', $validated['status']);
        }

        $paginated = $query->latest()->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'data' => collect($paginated->items())->map(fn (Comment $comment) => $this->serializeComment($comment)),
            'meta' => $this->paginationMeta($paginated),
        ]);
    }

    public function approveComment(Request $request, Comment $comment)
    {
        $this->authorizeCommentOwner($request, $comment);
        $comment->approve();

        return response()->json([
            'message' => 'Comment approved.',
            'comment' => $this->serializeComment($comment->fresh(['user.userProfile', 'video'])),
        ]);
    }

    public function rejectComment(Request $request, Comment $comment)
    {
        $this->authorizeCommentOwner($request, $comment);
        $comment->reject();

        return response()->json([
            'message' => 'Comment rejected.',
            'comment' => $this->serializeComment($comment->fresh(['user.userProfile', 'video'])),
        ]);
    }

    public function hideComment(Request $request, Comment $comment)
    {
        $this->authorizeCommentOwner($request, $comment);
        $comment->hide();

        return response()->json([
            'message' => 'Comment hidden.',
            'comment' => $this->serializeComment($comment->fresh(['user.userProfile', 'video'])),
        ]);
    }

    public function reports(Request $request)
    {
        $validated = $request->validate([
            'view' => ['nullable', 'in:received,submitted'],
            'status' => ['nullable', 'in:all,pending,reviewed,resolved,dismissed,escalated'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Report::with(['reporter.userProfile', 'reportedUser.userProfile', 'video', 'comment.user.userProfile', 'channel.userProfile', 'admin']);

        if (($validated['view'] ?? 'received') === 'submitted') {
            $query->where('reporter_id', Auth::id());
        } else {
            $query->where(function (Builder $builder) {
                $builder->where('reported_user_id', Auth::id())
                    ->orWhere('channel_id', Auth::id());
            });
        }

        if (($validated['status'] ?? 'all') !== 'all') {
            $query->where('status', $validated['status']);
        }

        $paginated = $query->latest()->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'data' => collect($paginated->items())->map(fn (Report $report) => $this->serializeReport($report)),
            'meta' => $this->paginationMeta($paginated),
            'stats' => [
                'received' => Report::where('reported_user_id', Auth::id())->orWhere('channel_id', Auth::id())->count(),
                'submitted' => Report::where('reporter_id', Auth::id())->count(),
                'pending' => Report::where('reported_user_id', Auth::id())->where('status', Report::STATUS_PENDING)->count(),
            ],
        ]);
    }

    public function reportReasons(Request $request)
    {
        $targetType = $request->validate([
            'target_type' => ['nullable', 'in:user,video,comment,channel'],
        ])['target_type'] ?? 'video';

        return response()->json([
            'target_type' => $targetType,
            'data' => Report::getPresetReasons($targetType),
        ]);
    }

    public function createReport(Request $request)
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:user,video,comment,channel'],
            'target_id' => ['required', 'integer'],
            'type' => ['nullable', 'in:spam,harassment,copyright,inappropriate_content,fake_information,other'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $type = $validated['type'] ?? $this->normalizeReportType($validated['reason']);
        $targetType = $validated['target_type'];
        $targetId = (int) $validated['target_id'];
        $reporterId = $request->user()->id;

        $this->guardSelfReport($targetType, $targetId, $reporterId);

        $duplicate = Report::where('reporter_id', $reporterId)
            ->where('type', $type)
            ->where(function (Builder $query) use ($targetType, $targetId) {
                match ($targetType) {
                    'video' => $query->where('video_id', $targetId),
                    'comment' => $query->where('comment_id', $targetId),
                    'channel' => $query->where('channel_id', $targetId),
                    default => $query->where('reported_user_id', $targetId)
                        ->whereNull('video_id')
                        ->whereNull('comment_id')
                        ->whereNull('channel_id'),
                };
            })
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'You already reported this content recently.',
            ], 422);
        }

        $priority = Report::autoClassifyPriority($type, $validated['description'] ?? $validated['reason']);

        $report = match ($targetType) {
            'video' => Report::reportVideo($reporterId, $targetId, $type, $validated['reason'], $validated['description'] ?? null, $priority),
            'comment' => Report::reportComment($reporterId, $targetId, $type, $validated['reason'], $validated['description'] ?? null, $priority),
            'channel' => Report::reportChannel($reporterId, $targetId, $type, $validated['reason'], $validated['description'] ?? null, $priority),
            default => Report::reportUser($reporterId, $targetId, $type, $validated['reason'], $validated['description'] ?? null, $priority),
        };

        return response()->json([
            'message' => 'Report submitted.',
            'report' => $this->serializeReport($report->fresh(['reporter.userProfile', 'reportedUser.userProfile', 'video', 'comment.user.userProfile', 'channel.userProfile'])),
        ], 201);
    }

    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:all,read,unread'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = CreatorFeedback::with(['admin', 'report'])
            ->where('creator_id', Auth::id());

        if (($validated['status'] ?? 'all') === 'read') {
            $query->where('is_read', true);
        } elseif (($validated['status'] ?? 'all') === 'unread') {
            $query->where('is_read', false);
        }

        $paginated = $query->latest()->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'data' => collect($paginated->items())->map(fn (CreatorFeedback $feedback) => $this->serializeFeedback($feedback)),
            'meta' => $this->paginationMeta($paginated),
            'unread_count' => CreatorFeedback::where('creator_id', Auth::id())->where('is_read', false)->count(),
        ]);
    }

    public function markFeedbackAsRead(Request $request, CreatorFeedback $feedback)
    {
        abort_if($feedback->creator_id !== $request->user()->id, 403, 'Unauthorized.');
        $feedback->markAsRead();

        return response()->json([
            'message' => 'Feedback marked as read.',
            'feedback' => $this->serializeFeedback($feedback->fresh(['admin', 'report'])),
        ]);
    }

    private function serializeVideo(Video $video): array
    {
        return [
            'id' => $video->id,
            'slug' => $video->video_url,
            'title' => $video->title,
            'thumbnail_url' => $video->thumbnail_url,
            'video_url' => $video->video_file_url,
            'duration' => (int) $video->duration,
            'views_count' => (int) $video->views_count,
            'likes_count' => (int) $video->likes_count,
            'dislikes_count' => (int) $video->dislikes_count,
            'comments_count' => (int) $video->comments_count,
            'is_reel' => (bool) $video->is_reel,
            'is_public' => (bool) $video->is_public,
            'status' => $video->status,
            'published_at' => optional($video->published_at)?->toIso8601String(),
            'created_at' => optional($video->created_at)?->toIso8601String(),
        ];
    }

    private function resolveOwnedVideo(string $video, int $userId): ?Video
    {
        return Video::where('user_id', $userId)
            ->where(function (Builder $query) use ($video) {
                $query->where('video_url', $video);
                if (is_numeric($video)) {
                    $query->orWhere('id', (int) $video);
                }
            })
            ->first();
    }

    private function serializeTopVideo(object $row): array
    {
        $video = $row->video ?? null;

        return [
            'video' => $video instanceof Video ? $this->serializeVideo($video) : null,
            'views' => (int) ($row->total_views ?? $row->views_count ?? 0),
            'likes' => (int) ($row->total_likes ?? $row->likes_count ?? 0),
            'comments' => (int) ($row->total_comments ?? 0),
            'watch_time_minutes' => (float) ($row->total_watch_time ?? 0),
        ];
    }

    private function serializeComment(Comment $comment): array
    {
        $profile = $comment->user?->userProfile;

        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'status' => $comment->status,
            'likes_count' => (int) ($comment->likes_count ?? 0),
            'created_at' => optional($comment->created_at)?->toIso8601String(),
            'author' => [
                'id' => $comment->user?->id,
                'name' => $profile?->channel_name ?? $comment->user?->name,
                'username' => $profile?->username,
                'avatar_url' => $this->mediaUrl($profile?->avatar_url),
            ],
            'video' => $comment->video ? [
                'id' => $comment->video->id,
                'slug' => $comment->video->video_url,
                'title' => $comment->video->title,
                'thumbnail_url' => $comment->video->thumbnail_url,
            ] : null,
        ];
    }

    private function serializeReport(Report $report): array
    {
        return [
            'id' => $report->id,
            'target_type' => $report->target_type,
            'type' => $report->type,
            'type_label' => $report->type_label,
            'reason' => $report->effective_reason,
            'description' => $report->description,
            'status' => $report->status,
            'status_label' => $report->status_label,
            'priority' => $report->priority,
            'priority_label' => $report->priority_label,
            'resolution_action' => $report->resolution_action,
            'resolution_action_label' => $report->resolution_action ? $report->resolution_action_label : null,
            'admin_notes' => $report->admin_notes,
            'resolved_at' => optional($report->resolved_at)?->toIso8601String(),
            'created_at' => optional($report->created_at)?->toIso8601String(),
            'reporter' => $this->serializeUser($report->reporter),
            'reported_user' => $this->serializeUser($report->reportedUser),
            'target' => $this->serializeReportTarget($report),
        ];
    }

    private function serializeReportTarget(Report $report): ?array
    {
        if ($report->video) {
            return [
                'type' => 'video',
                'id' => $report->video->id,
                'title' => $report->video->title,
                'thumbnail_url' => $report->video->thumbnail_url,
            ];
        }

        if ($report->comment) {
            return [
                'type' => 'comment',
                'id' => $report->comment->id,
                'content' => $report->comment->content,
                'author' => $this->serializeUser($report->comment->user),
            ];
        }

        if ($report->channel) {
            return [
                'type' => 'channel',
                ...$this->serializeUser($report->channel),
            ];
        }

        return $report->reportedUser ? [
            'type' => 'user',
            ...$this->serializeUser($report->reportedUser),
        ] : null;
    }

    private function serializeFeedback(CreatorFeedback $feedback): array
    {
        return [
            'id' => $feedback->id,
            'type' => $feedback->type,
            'type_label' => $feedback->type_label,
            'title' => $feedback->title,
            'message' => $feedback->message,
            'is_read' => (bool) $feedback->is_read,
            'read_at' => optional($feedback->read_at)?->toIso8601String(),
            'created_at' => optional($feedback->created_at)?->toIso8601String(),
            'admin' => $this->serializeUser($feedback->admin),
            'report_id' => $feedback->report_id,
        ];
    }

    private function serializeUser(?User $user): ?array
    {
        if (!$user) {
            return null;
        }

        $profile = $user->userProfile;

        return [
            'id' => $user->id,
            'name' => $profile?->channel_name ?? $user->name,
            'username' => $profile?->username,
            'avatar_url' => $this->mediaUrl($profile?->avatar_url),
        ];
    }

    private function authorizeCommentOwner(Request $request, Comment $comment): void
    {
        $comment->loadMissing('video');
        abort_if(!$comment->video || $comment->video->user_id !== $request->user()->id, 403, 'Unauthorized.');
    }

    private function guardSelfReport(string $targetType, int $targetId, int $reporterId): void
    {
        if ($targetType === 'user' || $targetType === 'channel') {
            abort_if($targetId === $reporterId, 422, 'You cannot report yourself.');
            User::findOrFail($targetId);
            return;
        }

        if ($targetType === 'video') {
            $video = Video::findOrFail($targetId);
            abort_if($video->user_id === $reporterId, 422, 'You cannot report your own video.');
            return;
        }

        $comment = Comment::findOrFail($targetId);
        abort_if($comment->user_id === $reporterId, 422, 'You cannot report your own comment.');
    }

    private function normalizeReportType(string $reason): string
    {
        return match ($reason) {
            'spam' => Report::TYPE_SPAM,
            'harassment', 'hate_speech' => Report::TYPE_HARASSMENT,
            'copyright' => Report::TYPE_COPYRIGHT,
            'fake_information' => Report::TYPE_FAKE_INFORMATION,
            'inappropriate_content', 'violence' => Report::TYPE_INAPPROPRIATE_CONTENT,
            default => Report::TYPE_OTHER,
        };
    }

    private function paginationMeta($paginated): array
    {
        return [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
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
}
