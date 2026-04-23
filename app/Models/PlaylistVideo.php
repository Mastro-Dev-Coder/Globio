<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistVideo extends Model
{
    protected $fillable = [
        'playlist_id',
        'video_id',
        'position',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}