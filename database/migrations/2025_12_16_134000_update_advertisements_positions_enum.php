<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Aggiorna l'enum per le posizioni delle pubblicità
        DB::statement("ALTER TABLE advertisements MODIFY COLUMN position ENUM('footer', 'between_videos', 'home_video', 'video_overlay') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ripristina l'enum originale
        DB::statement("ALTER TABLE advertisements MODIFY COLUMN position ENUM('header', 'sidebar', 'footer', 'between_videos', 'video_overlay') NOT NULL");
    }
};