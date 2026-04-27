<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadSession extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'original_file_name',
        'mime_type',
        'total_size',
        'chunk_size',
        'total_chunks',
        'uploaded_chunks',
        'uploaded_bytes',
        'status',
        'disk',
        'temp_dir',
        'assembled_path',
        'expires_at',
        'last_activity_at',
        'video_id',
    ];

    protected $casts = [
        'uploaded_chunks' => 'array',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
