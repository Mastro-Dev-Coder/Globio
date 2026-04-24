<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('stripe');
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('plan_code')->default('globio-premium');
            $table->string('plan_name')->default('Globio Premium');
            $table->string('status')->default('pending');
            $table->string('billing_interval')->default('month');
            $table->unsignedInteger('amount')->default(0);
            $table->string('currency', 3)->default('eur');
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('last_webhook_at')->nullable();
            $table->json('features')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['provider', 'stripe_customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_subscriptions');
    }
};
