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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('video_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->enum('type', [
                'spam',
                'harassment', 
                'copyright',
                'inappropriate_content',
                'fake_information',
                'other'
            ]);
            
            $table->string('reason');
            $table->text('description')->nullable();
            
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed', 'escalated'])->default('pending');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->enum('resolution_action', [
                'content_removed',
                'user_warned',
                'user_suspended', 
                'user_banned',
                'false_report',
                'no_action'
            ])->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->json('evidence_files')->nullable();
            
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index(['type', 'status']);
            $table->index(['admin_id', 'status']);
            $table->index(['created_at']);
            $table->index(['reported_user_id']);
            $table->index(['video_id']);
            $table->index(['comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};