<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class WatchLater extends Model
{
    use HasFactory;

    protected $table = 'watch_later';

    protected $fillable = [
        'user_id',
        'video_id',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    /**
     * Relazione con l'utente
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione con il video
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope per ottenere i watch later di un utente specifico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope per ottenere i watch later di un video specifico
     */
    public function scopeForVideo($query, $videoId)
    {
        return $query->where('video_id', $videoId);
    }

    /**
     * Verifica se un video è nella watch later list di un utente
     */
    public static function isInWatchLater($userId, $videoId): bool
    {
        return static::where('user_id', $userId)
            ->where('video_id', $videoId)
            ->exists();
    }

    /**
     * Aggiunge un video alla watch later list di un utente
     */
    public static function addToWatchLater($userId, $videoId): bool
    {
        try {
            static::create([
                'user_id' => $userId,
                'video_id' => $videoId,
                'added_at' => now(),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error adding to watch later', [
                'user_id' => $userId,
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Rimuove un video dalla watch later list di un utente
     */
    public static function removeFromWatchLater($userId, $videoId): bool
    {
        try {
            return static::where('user_id', $userId)
                ->where('video_id', $videoId)
                ->delete() > 0;
        } catch (\Exception $e) {
            Log::error('Error removing from watch later', [
                'user_id' => $userId,
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Toggle - aggiunge o rimuove dalla watch later list
     */
    public static function toggleWatchLater($userId, $videoId): bool
    {
        if (static::isInWatchLater($userId, $videoId)) {
            return static::removeFromWatchLater($userId, $videoId);
        } else {
            return static::addToWatchLater($userId, $videoId);
        }
    }
}