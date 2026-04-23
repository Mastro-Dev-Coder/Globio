<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $fillable = [
        'likeable_type',
        'likeable_id',
        'user_id',
        'reaction',
        'type',
    ];

    protected $casts = [
        'reaction' => 'string',
        'type' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * Get the video associated with the like.
     */
    public function video()
    {
        return $this->belongsTo(Video::class, 'likeable_id')->where('likeable_type', Video::class);
    }
}
