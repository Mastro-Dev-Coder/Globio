<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->string('original_file_name');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('total_size');
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            $table->json('uploaded_chunks')->nullable();
            $table->unsignedBigInteger('uploaded_bytes')->default(0);
            $table->string('status', 30)->default('pending');
            $table->string('disk', 30)->default('local');
            $table->string('temp_dir');
            $table->string('assembled_path')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->foreignId('video_id')->nullable()->constrained('videos')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_sessions');
    }
};
