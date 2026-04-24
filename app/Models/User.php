<?php

namespace App\Models;

use App\Models\ApiAccessToken;
use App\Models\PremiumSubscription;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'stripe_customer_id',
        'premium_access_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_access_ends_at' => 'datetime',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'channel_id', 'subscriber_id');
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id', 'channel_id');
    }

    public function userPreferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function premiumSubscriptions(): HasMany
    {
        return $this->hasMany(PremiumSubscription::class);
    }

    public function activePremiumSubscription(): ?PremiumSubscription
    {
        return $this->premiumSubscriptions
            ->first(fn (PremiumSubscription $subscription) => $subscription->isActive())
            ?? $this->premiumSubscriptions()
                ->latest('current_period_end')
                ->get()
                ->first(fn (PremiumSubscription $subscription) => $subscription->isActive());
    }

    public function hasActivePremium(): bool
    {
        if ($this->relationLoaded('premiumSubscriptions')) {
            return $this->activePremiumSubscription() !== null;
        }

        return $this->premiumSubscriptions()
            ->get()
            ->contains(fn (PremiumSubscription $subscription) => $subscription->isActive());
    }

    public function premiumCapabilities(): array
    {
        $subscription = $this->activePremiumSubscription();

        if (!$subscription) {
            return [
                'ad_free' => false,
                'background_playback' => false,
                'picture_in_picture' => false,
                'smart_downloads' => false,
                'higher_quality_streaming' => false,
                'reels_enhanced_controls' => false,
                'queue_management' => false,
            ];
        }

        return array_merge([
            'ad_free' => true,
            'background_playback' => true,
            'picture_in_picture' => true,
            'smart_downloads' => true,
            'higher_quality_streaming' => true,
            'reels_enhanced_controls' => true,
            'queue_management' => true,
        ], $subscription->features ?? []);
    }

    // La relazione notifications Ã¨ gestita automaticamente dal trait Notifiable di Laravel
    public function apiAccessTokens(): HasMany
    {
        return $this->hasMany(ApiAccessToken::class);
    }

}
