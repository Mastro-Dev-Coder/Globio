<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\Video;
use App\Notifications\VideoProcessedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CompleteVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tries = 3;
    protected $timeout = 300;

    public function __construct(
        public int $videoId
    ) {}

    public function handle(): void
    {
        try {
            Log::info('Avvio completamento video', [
                'video_id' => $this->videoId,
            ]);

            $video = Video::findOrFail($this->videoId);
            $user = $video->user;

            if ($user && $user->userProfile) {
                $user->userProfile->increment('video_count');

                $totalViews = $user->videos()->sum('views_count');
                $user->userProfile->update(['total_views' => $totalViews]);
            }

            $video->refresh();
            
            $finalVideoPath = $video->video_path;
            $finalThumbnailPath = $video->thumbnail_path;

            Log::info('Stato video prima del completamento', [
                'video_id' => $this->videoId,
                'status_prima' => $video->status,
                'video_path_prima' => $finalVideoPath,
                'thumbnail_path_prima' => $finalThumbnailPath,
                'video_path_contiene_temp' => strpos($finalVideoPath ?? '', 'temp/') !== false,
            ]);

            $requireApproval = Setting::getValue('require_approval', false);
            $finalStatus = $requireApproval ? 'pending_approval' : 'published';
            $publishedAt = $requireApproval ? null : now();

            Log::info('Determinazione stato finale video', [
                'video_id' => $this->videoId,
                'require_approval' => $requireApproval,
                'final_status' => $finalStatus,
                'published_at' => $publishedAt
            ]);

            $video->update([
                'status' => $finalStatus,
                'published_at' => $publishedAt,
                'is_featured' => false,
                'views_count' => $video->views_count ?? 0,
                'likes_count' => $video->likes_count ?? 0,
                'dislikes_count' => $video->dislikes_count ?? 0,
                'comments_count' => $video->comments_count ?? 0,
                'video_quality' => $video->video_quality ?? '720p',
                'video_format' => $video->video_format ?? 'mp4',
                'moderation_reason' => $requireApproval ? 'In attesa di approvazione amministrativa' : null,
            ]);

            if ($user) {
                $user->notify(new VideoProcessedNotification($video));
            }

            Log::info('Completamento video riuscito', [
                'video_id' => $this->videoId,
                'final_video_path' => $video->fresh()->video_path,
                'final_thumbnail_path' => $video->fresh()->thumbnail_path,
            ]);
        } catch (\Exception $e) {
            Log::error('Errore completamento video', [
                'video_id' => $this->videoId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
