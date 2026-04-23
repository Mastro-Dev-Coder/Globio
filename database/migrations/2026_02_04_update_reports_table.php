<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Aggiungi colonna per segnalazioni canale
            $table->unsignedBigInteger('channel_id')->nullable()->after('comment_id');
            
            // Aggiungi colonna per segnalazioni personalizzate
            $table->string('custom_reason', 255)->nullable()->after('reason');
            
            // Aggiungi indici per migliorare le performance
            $table->index('channel_id');
            $table->index('type');
            $table->index('status');
            $table->index(['reported_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('channel_id');
            $table->dropColumn('custom_reason');
            
            $table->dropIndex(['channel_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['reported_user_id', 'status']);
        });
    }
};
