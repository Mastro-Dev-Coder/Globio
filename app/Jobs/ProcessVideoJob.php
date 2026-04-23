<?php

namespace App\Jobs;

use App\Models\Video;
use App\Notifications\VideoProcessingStartedNotification;
use App\Services\VideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $videoId;
    protected string $tempFilePath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $videoId, string $tempFilePath)
    {
        $this->videoId      = $videoId;
        $this->tempFilePath = $tempFilePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $video = Video::findOrFail((int) $this->videoId);
        $user = $video->user;

        if ($user) {
            $user->notify(new VideoProcessingStartedNotification($video));
        }

        Log::info('Avvio processamento video con FFmpeg', [
            'video_id' => $this->videoId,
            'temp_file_path' => $this->tempFilePath
        ]);

        $videoService = new VideoService();

        try {
            $videoService->processVideo((int) $this->videoId, $this->tempFilePath);

            Log::info('Processing completato con successo', [
                'video_id' => $this->videoId
            ]);
        } catch (\Exception $e) {
            Log::error('Errore processamento video', [
                'video_id' => $this->videoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Video::where('id', $this->videoId)->update([
            'status' => 'rejected',
            'moderation_reason' => 'Errore durante il processamento automatico: ' . $exception->getMessage(),
        ]);
    }
}
