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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('channel_name')->nullable();
            $table->text('channel_description')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->bigInteger('subscriber_count')->default(0);
            $table->bigInteger('video_count')->default(0);
            $table->bigInteger('total_views')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_channel_enabled')->default(true);
            $table->json('social_links')->nullable(); // per i link social
            $table->string('country', 10)->nullable();
            $table->timestamp('channel_created_at')->nullable();
            $table->timestamps();
            
            $table->index('is_verified');
            $table->index('subscriber_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
