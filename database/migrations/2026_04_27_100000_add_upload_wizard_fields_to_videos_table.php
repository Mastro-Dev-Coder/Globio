<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('visibility', 20)->default('public')->after('status');
            $table->timestamp('scheduled_for')->nullable()->after('published_at');
            $table->json('suggested_video_ids')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['visibility', 'scheduled_for', 'suggested_video_ids']);
        });
    }
};
