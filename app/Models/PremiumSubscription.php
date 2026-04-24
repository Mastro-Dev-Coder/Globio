<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumSubscription extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_TRIALING = 'trialing';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    public const STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'user_id',
        'provider',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_price_id',
        'stripe_checkout_session_id',
        'plan_code',
        'plan_name',
        'status',
        'billing_interval',
        'amount',
        'currency',
        'cancel_at_period_end',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'ends_at',
        'last_webhook_at',
        'features',
        'meta',
    ];

    protected $casts = [
        'cancel_at_period_end' => 'boolean',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_webhook_at' => 'datetime',
        'features' => 'array',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        if (!in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING], true)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->current_period_end && $this->current_period_end->isPast() && !$this->cancel_at_period_end) {
            return false;
        }

        return true;
    }
};
