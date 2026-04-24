<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('remember_token');
            $table->timestamp('premium_access_ends_at')->nullable()->after('stripe_customer_id');

            $table->index('stripe_customer_id');
            $table->index('premium_access_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['stripe_customer_id']);
            $table->dropIndex(['premium_access_ends_at']);
            $table->dropColumn(['stripe_customer_id', 'premium_access_ends_at']);
        });
    }
};
