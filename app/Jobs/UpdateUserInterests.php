<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\VideoRecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateUserInterests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param int|null $userId ID specifico dell'utente (null per aggiornare tutti)
     */
    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recommendationService = new VideoRecommendationService();

        if ($this->userId) {
            // Aggiorna un utente specifico
            try {
                $recommendationService->updateUserInterests($this->userId);
                Log::info("Interessi aggiornati per utente ID: {$this->userId}");
            } catch (\Exception $e) {
                Log::error("Errore nell'aggiornamento interessi per utente ID {$this->userId}: " . $e->getMessage());
            }
        } else {
            // Aggiorna tutti gli utenti con attività recente
            $activeUsers = User::whereHas('watchHistories', function ($query) {
                $query->where('last_watched_at', '>=', now()->subDays(30));
            })->get();

            foreach ($activeUsers as $user) {
                try {
                    $recommendationService->updateUserInterests($user->id);
                    Log::info("Interessi aggiornati per utente ID: {$user->id}");
                } catch (\Exception $e) {
                    Log::error("Errore nell'aggiornamento interessi per utente ID {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}