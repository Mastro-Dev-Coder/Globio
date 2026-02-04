<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail_path',
        'is_public',
        'video_count',
        'views_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'playlist_videos')->withPivot('position');
    }

    // Scope per playlist pubbliche
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Ottieni thumbnail URL
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        
        return asset($this->thumbnail_path);
    }

    // Incrementa conteggio visualizzazioni
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    // Ottieni il primo video della playlist ordinato per posizione
    public function getFirstVideoAttribute()
    {
        return $this->videos()->orderBy('playlist_videos.position', 'asc')->first();
    }

    // Ottieni miniature dinamica basata sui video della playlist
    public function getDynamicThumbnailUrlAttribute()
    {
        $firstVideo = $this->first_video;
        
        if ($firstVideo && $firstVideo->thumbnail_url) {
            return $firstVideo->thumbnail_url;
        }
        
        // Fallback: thumbnail personalizzata della playlist se presente
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }
        
        // Fallback finale: icona di default
        return null;
    }
}