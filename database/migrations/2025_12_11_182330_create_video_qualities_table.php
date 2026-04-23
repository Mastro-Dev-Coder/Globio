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
        Schema::create('video_qualities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->string('quality'); // 'original', '1080p', '720p', '480p', '360p'
            $table->string('label'); // 'Originale', '1080p Full HD', '720p HD', etc.
            $table->string('file_path'); // path to the video file
            $table->string('file_url'); // public URL to the video file
            $table->integer('file_size')->nullable(); // file size in bytes
            $table->integer('width')->nullable(); // video width
            $table->integer('height')->nullable(); // video height
            $table->integer('bitrate')->nullable(); // video bitrate in kbps
            $table->boolean('is_available')->default(true); // whether this quality is available
            $table->timestamps();
            
            $table->unique(['video_id', 'quality']);
            $table->index(['video_id', 'is_available']);
            $table->index('quality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_qualities');
    }
};