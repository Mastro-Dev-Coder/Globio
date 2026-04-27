<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class PublishScheduledVideosCommand extends Command
{
    protected $signature = 'videos:publish-scheduled';
    protected $description = 'Pubblica i video programmati alla data e ora previste';

    public function handle(): int
    {
        $videos = Video::query()
            ->where('visibility', 'scheduled')
            ->where('status', 'draft')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', now())
            ->get();

        foreach ($videos as $video) {
            $video->update([
                'visibility' => 'public',
                'status' => 'published',
                'is_public' => true,
                'published_at' => $video->scheduled_for ?? now(),
            ]);
        }

        $this->info("Pubblicati {$videos->count()} video programmati.");

        return self::SUCCESS;
    }
}
