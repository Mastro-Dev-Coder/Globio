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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['banner', 'adsense', 'video']);
            $table->enum('position', ['header', 'sidebar', 'footer', 'between_videos', 'video_overlay']);
            $table->text('content')->nullable();
            $table->longText('code')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('views')->default(0);
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['position', 'is_active']);
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};