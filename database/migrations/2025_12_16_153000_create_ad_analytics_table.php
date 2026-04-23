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
        Schema::create('ad_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->unsignedBigInteger('video_id');
            $table->string('session_id');
            $table->unsignedBigInteger('timestamp');
            $table->string('ad_id')->nullable();
            $table->string('ad_type')->nullable();
            $table->string('position')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('load_time')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->timestamps();

            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['video_id', 'event_type']);
            $table->index(['session_id']);
            $table->index(['created_at']);
            $table->index(['ad_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_analytics');
    }
};