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
            $table->enum('status', ['processing', 'uploaded', 'transcoding', 'published', 'rejected', 'pending_approval'])->default('processing')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->enum('status', ['processing', 'uploaded', 'transcoding', 'published', 'rejected'])->default('processing')->change();
        });
    }
};