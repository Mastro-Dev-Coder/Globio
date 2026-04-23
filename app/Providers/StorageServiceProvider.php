<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->ensureStorageDirectoriesExist();
    }

    /**
     * Assicura che le directory di storage necessarie esistano
     */
    private function ensureStorageDirectoriesExist(): void
    {
        $directories = [
            public_path('videos'),
            public_path('thumbnails'),
            public_path('avatars'),
            public_path('banners'),
            public_path('logos'),
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                    Log::warning("Impossibile creare la directory: {$directory}");
                    continue;
                }
                Log::info("Directory creata automaticamente: {$directory}");
            }
        }
    }
}