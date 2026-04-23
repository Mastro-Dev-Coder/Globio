<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'watched_duration',
        'total_duration',
        'completed',
        'last_watched_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    // Scope per cronologia recente
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_watched_at', '>=', now()->subDays($days))
                    ->orderBy('last_watched_at', 'desc');
    }

    // Ottieni percentuale completamento
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_duration == 0) {
            return 0;
        }
        
        return round(($this->watched_duration / $this->total_duration) * 100, 1);
    }
}