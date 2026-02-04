<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    protected $fillable = ['video_id', 'user_id', 'content', 'parent_id', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_HIDDEN = 'hidden';

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function dislikes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable')->where('type', 'dislike');
    }

    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isDislikedBy($userId): bool
    {
        return $this->dislikes()->where('user_id', $userId)->exists();
    }

    /**
     * Verifica se il commento è approvato e visibile pubblicamente
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Verifica se il commento è in attesa di approvazione
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verifica se il commento è rifiutato
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Verifica se il commento è nascosto
     */
    public function isHidden(): bool
    {
        return $this->status === self::STATUS_HIDDEN;
    }

    /**
     * Verifica se il commento è visibile per un utente
     */
    public function isVisibleTo($userId = null): bool
    {
        // I commenti approvati sono sempre visibili
        if ($this->isApproved()) {
            return true;
        }

        // I commenti pending sono visibili solo al proprietario del video
        if ($this->isPending() && $userId) {
            return $this->video->user_id === $userId;
        }

        // Commenti rifiuti e nascosti non sono mai visibili
        return false;
    }

    /**
     * Approva il commento
     */
    public function approve(): bool
    {
        return $this->update(['status' => self::STATUS_APPROVED]);
    }

    /**
     * Rifiuta il commento
     */
    public function reject(): bool
    {
        return $this->update(['status' => self::STATUS_REJECTED]);
    }

    /**
     * Nasconde il commento
     */
    public function hide(): bool
    {
        return $this->update(['status' => self::STATUS_HIDDEN]);
    }

    /**
     * Imposta il commento come pending
     */
    public function setPending(): bool
    {
        return $this->update(['status' => self::STATUS_PENDING]);
    }

    /**
     * Scope per ottenere solo i commenti approvati
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope per ottenere solo i commenti in attesa
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope per ottenere solo i commenti rifiutati
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope per ottenere solo i commenti nascosti
     */
    public function scopeHidden($query)
    {
        return $query->where('status', self::STATUS_HIDDEN);
    }

    /**
     * Scope per ottenere i commenti visibili per un utente
     */
    public function scopeVisibleTo($query, $userId = null)
    {
        $query = $query->where('status', self::STATUS_APPROVED);
        
        // Se è specificato un utente e è il proprietario del video, includi anche i pending
        if ($userId) {
            $videoOwnerIds = Video::where('user_id', $userId)->pluck('id');
            if ($videoOwnerIds->isNotEmpty()) {
                $query->orWhereIn('video_id', $videoOwnerIds)
                      ->where('status', self::STATUS_PENDING);
            }
        }
        
        return $query;
    }

    /**
     * Boot method per impostare automaticamente lo status dei nuovi commenti
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            // Se non è specificato uno status, determina in base alle impostazioni del video
            if (!$comment->status) {
                $video = $comment->video;
                if ($video && $video->commentsRequireApproval()) {
                    $comment->status = self::STATUS_PENDING;
                } else {
                    $comment->status = self::STATUS_APPROVED;
                }
            }
        });

        static::created(function ($comment) {
            // Aggiorna il contatore dei commenti del video se il commento è approvato
            if ($comment->isApproved()) {
                $comment->video->increment('comments_count');
            }
        });

        static::deleted(function ($comment) {
            // Aggiorna il contatore dei commenti del video se il commento era approvato
            if ($comment->isApproved()) {
                $comment->video->decrement('comments_count');
            }
        });

        static::updated(function ($comment) {
            // Gestisci i cambiamenti di status per aggiornare i contatori
            if ($comment->wasChanged('status')) {
                $originalStatus = $comment->getOriginal('status');
                $newStatus = $comment->status;

                // Da non approvato a approvato
                if ($originalStatus !== self::STATUS_APPROVED && $newStatus === self::STATUS_APPROVED) {
                    $comment->video->increment('comments_count');
                }
                
                // Da approvato a non approvato
                if ($originalStatus === self::STATUS_APPROVED && $newStatus !== self::STATUS_APPROVED) {
                    $comment->video->decrement('comments_count');
                }
            }
        });
    }
}
