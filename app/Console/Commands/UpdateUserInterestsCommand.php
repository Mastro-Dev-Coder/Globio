<?php

namespace App\Console\Commands;

use App\Jobs\UpdateUserInterests;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserInterestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:update-interests {user_id? : ID dell\'utente specifico da aggiornare}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggiorna gli interessi degli utenti per le raccomandazioni video';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            // Verifica che l'utente esista
            $user = User::find($userId);
            if (!$user) {
                $this->error("Utente con ID {$userId} non trovato.");
                return 1;
            }

            $this->info("Aggiornamento interessi per utente ID: {$userId}");
            UpdateUserInterests::dispatch($userId);
        } else {
            $this->info('Aggiornamento interessi per tutti gli utenti attivi...');

            // Conta utenti attivi
            $activeUsersCount = User::whereHas('watchHistories', function ($query) {
                $query->where('last_watched_at', '>=', now()->subDays(30));
            })->count();

            $this->info("Trovati {$activeUsersCount} utenti attivi da aggiornare.");

            // Puoi scegliere di dispatchare il job senza user_id per aggiornare tutti
            UpdateUserInterests::dispatch();
        }

        $this->info('Job di aggiornamento interessi inviato alla coda.');
        return 0;
    }
}