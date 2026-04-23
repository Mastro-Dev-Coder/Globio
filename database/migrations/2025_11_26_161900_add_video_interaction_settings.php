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
        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('comments_enabled')->default(true)->after('is_featured');
            $table->boolean('likes_enabled')->default(true)->after('comments_enabled');
            $table->boolean('comments_require_approval')->default(false)->after('likes_enabled');
            
            $table->index(['comments_enabled', 'status']);
            $table->index(['likes_enabled', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['comments_enabled', 'status']);
            $table->dropIndex(['likes_enabled', 'status']);
            $table->dropColumn(['comments_enabled', 'likes_enabled', 'comments_require_approval']);
        });
    }
};