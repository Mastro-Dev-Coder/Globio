<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->integer('watched_duration')->default(0); // in seconds
            $table->integer('total_duration')->default(0); // in seconds
            $table->boolean('completed')->default(false);
            $table->timestamp('last_watched_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'video_id']);
            $table->index(['user_id', 'last_watched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};