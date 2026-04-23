<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AutomatedPlaylistService;
use Illuminate\Console\Command;

class GenerateAutoPlaylistsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlists:generate-auto {--user= : Specific user ID to generate playlists for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera playlist automatiche per gli utenti basato sulle loro preferenze';

    /**
     * The automated playlist service.
     *
     * @var AutomatedPlaylistService
     */
    protected AutomatedPlaylistService $playlistService;

    /**
     * Create a new command instance.
     */
    public function __construct(AutomatedPlaylistService $playlistService)
    {
        parent::__construct();
        $this->playlistService = $playlistService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user');

        if ($userId) {
            // Genera playlist per un utente specifico
            return $this->generateForUser($userId);
        }

        // Genera playlist per tutti gli utenti attivi
        $users = User::where('is_active', true)
            ->orWhereNotNull('last_login_at')
            ->get();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $created = 0;
        $errors = 0;

        foreach ($users as $user) {
            try {
                $playlists = $this->playlistService->generateAutomaticPlaylists($user->id);
                $created += $playlists->count();
                
                // Pulisci playlist vecchie
                $this->playlistService->cleanupOldAutoPlaylists($user->id);
            } catch (\Exception $e) {
                $errors++;
                $this->error("Errore per utente {$user->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Playlist automatiche generate: {$created}");
        
        if ($errors > 0) {
            $this->warn("Errori: {$errors}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Genera playlist per un utente specifico.
     */
    protected function generateForUser(int $userId): int
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("Utente {$userId} non trovato");
            return Command::FAILURE;
        }

        try {
            $this->info("Generando playlist automatiche per: {$user->name}");

            $playlists = $this->playlistService->generateAutomaticPlaylists($userId);
            
            foreach ($playlists as $playlist) {
                $this->info("- {$playlist->title} ({$playlist->video_count} video)");
            }

            $this->info("Totale playlist create: {$playlists->count()}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Errore: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
