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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('video_path');
            $table->string('video_url')->nullable();
            $table->integer('duration')->default(0);
            $table->bigInteger('views_count')->default(0);
            $table->bigInteger('likes_count')->default(0);
            $table->bigInteger('dislikes_count')->default(0);
            $table->bigInteger('comments_count')->default(0);
            $table->enum('status', ['processing', 'uploaded', 'transcoding', 'published', 'rejected'])->default('processing');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('video_quality')->nullable();
            $table->string('video_format')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->json('tags')->nullable();
            $table->string('language', 10)->default('it');
            $table->timestamp('published_at')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'is_public']);
            $table->index(['user_id', 'status']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
