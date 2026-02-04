<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class CreatorFeedback extends Model
{
    protected $table = 'creator_feedbacks';
    
    protected $fillable = [
        'creator_id',
        'admin_id',
        'report_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipi di feedback
    const TYPE_REPORT_RESOLUTION = 'report_resolution';
    const TYPE_REPORT_DISMISSED = 'report_dismissed';
    const TYPE_REPORT_ESCALATED = 'report_escalated';
    const TYPE_GENERAL_NOTICE = 'general_notice';

    // Relazioni
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    // Scope
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessor per l'etichetta del tipo
    public function getTypeLabelAttribute()
    {
        return [
            self::TYPE_REPORT_RESOLUTION => 'Risoluzione Segnalazione',
            self::TYPE_REPORT_DISMISSED => 'Segnalazione Respinta',
            self::TYPE_REPORT_ESCALATED => 'Segnalazione Escalata',
            self::TYPE_GENERAL_NOTICE => 'Avviso Generale',
        ][$this->type] ?? $this->type;
    }

    // Metodi
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    // Metodi statici per creare feedback
    public static function createForReportResolution($creatorId, $adminId, $reportId, $title, $message)
    {
        return self::create([
            'creator_id' => $creatorId,
            'admin_id' => $adminId,
            'report_id' => $reportId,
            'type' => self::TYPE_REPORT_RESOLUTION,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function createForReportDismissed($creatorId, $adminId, $reportId, $title, $message)
    {
        return self::create([
            'creator_id' => $creatorId,
            'admin_id' => $adminId,
            'report_id' => $reportId,
            'type' => self::TYPE_REPORT_DISMISSED,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function createForReportEscalated($creatorId, $adminId, $reportId, $title, $message)
    {
        return self::create([
            'creator_id' => $creatorId,
            'admin_id' => $adminId,
            'report_id' => $reportId,
            'type' => self::TYPE_REPORT_ESCALATED,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function createGeneralNotice($creatorId, $adminId, $title, $message)
    {
        return self::create([
            'creator_id' => $creatorId,
            'admin_id' => $adminId,
            'type' => self::TYPE_GENERAL_NOTICE,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    // Statistiche
    public static function getStatsForCreator($creatorId)
    {
        return [
            'total' => self::where('creator_id', $creatorId)->count(),
            'unread' => self::where('creator_id', $creatorId)->where('is_read', false)->count(),
            'by_type' => self::where('creator_id', $creatorId)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    // Badge color per il tipo
    public function getTypeColorAttribute()
    {
        return [
            self::TYPE_REPORT_RESOLUTION => 'green',
            self::TYPE_REPORT_DISMISSED => 'gray',
            self::TYPE_REPORT_ESCALATED => 'red',
            self::TYPE_GENERAL_NOTICE => 'blue',
        ][$this->type] ?? 'gray';
    }
}