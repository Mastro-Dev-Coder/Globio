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
        Schema::create('channel_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date');

            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);

            $table->decimal('watch_time_minutes', 10, 2)->default(0);
            $table->decimal('average_watch_duration', 8, 2)->default(0);

            $table->decimal('click_through_rate', 5, 3)->default(0);

            $table->string('traffic_source')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('device_type')->nullable();
            $table->string('referrer')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['video_id', 'date']);
            $table->index(['traffic_source', 'date']);
            $table->index(['country', 'date']);
            $table->index(['device_type', 'date']);

            $table->unique(['user_id', 'video_id', 'date', 'traffic_source', 'country', 'device_type'], 'analytics_unique_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_analytics');
    }
};
