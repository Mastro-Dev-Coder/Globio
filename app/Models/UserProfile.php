<?php

namespace App\Models;

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'channel_name',
        'channel_description',
        'avatar_url',
        'banner_url',
        'subscribers_count',
        'video_count',
        'total_views',
        'is_verified',
        'is_channel_enabled',
        'social_links',
        'country',
        'available_balance',
        'monetization_enabled',
        'ad_preferences',
        'payout_method',
        'bank_details',
        'tax_info',
        'channel_created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'is_verified' => 'boolean',
            'is_channel_enabled' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'user_id');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'user_id');
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'channel_id', 'subscriber_id');
    }

    /**
     * Get the route key name for route model binding.
     */
    public function getRouteKeyName()
    {
        return 'username';
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($userProfile) {
            // Imposta is_channel_enabled a true di default se è null
            if (is_null($userProfile->is_channel_enabled)) {
                $userProfile->is_channel_enabled = true;
            }
        });
    }
}
