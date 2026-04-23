<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('user_id');
            $table->index('username');
        });

        DB::table('user_profiles')
            ->whereNull('is_channel_enabled')
            ->update(['is_channel_enabled' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropColumn('username');
        });
    }
};
