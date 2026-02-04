<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likeable');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('reaction', ['like', 'dislike'])->default('like');
            $table->timestamps();
            $table->unique(['likeable_type', 'likeable_id', 'user_id'], 'unique_user_like');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
