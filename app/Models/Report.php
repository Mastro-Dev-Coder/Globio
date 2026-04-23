<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'video_id',
        'comment_id',
        'channel_id', // Aggiunto per segnalazioni canale
        'type', // 'spam', 'harassment', 'copyright', 'inappropriate_content', 'fake_information', 'other', 'custom'
        'reason',
        'custom_reason', // Aggiunto per ragioni personalizzate
        'description',
        'status', // 'pending', 'reviewed', 'resolved', 'dismissed', 'escalated'
        'admin_id',
        'admin_notes',
        'resolution_action',
        'resolved_at',
        'priority', // 'low', 'medium', 'high', 'urgent'
        'evidence_files',
    ];

    protected $casts = [
        'evidence_files' => 'array',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relazioni
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    // Relazione per segnalazione canale
    public function channel()
    {
        return $this->belongsTo(User::class, 'channel_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scope per tipo
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope per status
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope per priorità
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Scope per admin (se assegnato)
    public function scopeAssignedTo($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    // Scope per tipo di target
    public function scopeWithTargetType($query, $targetType)
    {
        return match ($targetType) {
            'channel' => $query->whereNotNull('channel_id'),
            'video' => $query->whereNotNull('video_id'),
            'comment' => $query->whereNotNull('comment_id'),
            'user' => $query->whereNull('channel_id')->whereNull('video_id')->whereNull('comment_id'),
            default => $query,
        };
    }

    // Scope per segnalazioni personalizzate
    public function scopeCustom($query)
    {
        return $query->where('type', self::TYPE_CUSTOM);
    }

    // Scope per data
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Tipi di segnalazione
    const TYPE_SPAM = 'spam';
    const TYPE_HARASSMENT = 'harassment';
    const TYPE_COPYRIGHT = 'copyright';
    const TYPE_INAPPROPRIATE_CONTENT = 'inappropriate_content';
    const TYPE_FAKE_INFORMATION = 'fake_information';
    const TYPE_OTHER = 'other';
    const TYPE_CUSTOM = 'custom'; // Aggiunto per segnalazioni personalizzate

    // Target types
    const TARGET_USER = 'user';
    const TARGET_VIDEO = 'video';
    const TARGET_COMMENT = 'comment';
    const TARGET_CHANNEL = 'channel';

    // Stati
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';
    const STATUS_ESCALATED = 'escalated';

    // Priorità
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Azioni di risoluzione
    const ACTION_CONTENT_REMOVED = 'content_removed';
    const ACTION_USER_WARNED = 'user_warned';
    const ACTION_USER_SUSPENDED = 'user_suspended';
    const ACTION_USER_BANNED = 'user_banned';
    const ACTION_FALSE_REPORT = 'false_report';
    const ACTION_CHANNEL_WARNED = 'channel_warned';
    const ACTION_CHANNEL_HIDDEN = 'channel_hidden';
    const ACTION_NO_ACTION = 'no_action';

    // Metodi di utilità
    public function getTypeLabelAttribute()
    {
        $labels = [
            self::TYPE_SPAM => 'Spam',
            self::TYPE_HARASSMENT => 'Molestie',
            self::TYPE_COPYRIGHT => 'Violazione Copyright',
            self::TYPE_INAPPROPRIATE_CONTENT => 'Contenuto Inappropriato',
            self::TYPE_FAKE_INFORMATION => 'Informazioni False',
            self::TYPE_OTHER => 'Altro',
            self::TYPE_CUSTOM => 'Personalizzato',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => 'In attesa',
            self::STATUS_REVIEWED => 'In revisione',
            self::STATUS_RESOLVED => 'Risolto',
            self::STATUS_DISMISSED => 'Respinto',
            self::STATUS_ESCALATED => 'Escalato',
        ][$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute()
    {
        return [
            self::PRIORITY_LOW => 'Bassa',
            self::PRIORITY_MEDIUM => 'Media',
            self::PRIORITY_HIGH => 'Alta',
            self::PRIORITY_URGENT => 'Urgente',
        ][$this->priority] ?? $this->priority;
    }

    public function getResolutionActionLabelAttribute()
    {
        return [
            self::ACTION_CONTENT_REMOVED => 'Contenuto rimosso',
            self::ACTION_USER_WARNED => 'Utente ammonito',
            self::ACTION_USER_SUSPENDED => 'Utente sospeso',
            self::ACTION_USER_BANNED => 'Utente bannato',
            self::ACTION_CHANNEL_WARNED => 'Canale ammonito',
            self::ACTION_CHANNEL_HIDDEN => 'Canale nascosto',
            self::ACTION_FALSE_REPORT => 'Segnalazione falsa',
            self::ACTION_NO_ACTION => 'Nessuna azione',
        ][$this->resolution_action] ?? $this->resolution_action;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REVIEWED => 'blue',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_DISMISSED => 'gray',
            self::STATUS_ESCALATED => 'red',
        ][$this->status] ?? 'gray';
    }

    public function getPriorityColorAttribute()
    {
        return [
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_MEDIUM => 'yellow',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
        ][$this->priority] ?? 'gray';
    }

    // Ottieni il target type (user, video, comment, channel)
    public function getTargetTypeAttribute()
    {
        if ($this->channel_id) return 'channel';
        if ($this->video_id) return 'video';
        if ($this->comment_id) return 'comment';
        return 'user';
    }

    // Ottieni il contenuto segnalato (restituisce il modello appropriato)
    public function getReportedContentAttribute()
    {
        if ($this->channel_id) return $this->channel;
        if ($this->video_id) return $this->video;
        if ($this->comment_id) return $this->comment;
        return $this->reportedUser;
    }

    // Ottieni il motivo effettivo (se custom, usa custom_reason)
    public function getEffectiveReasonAttribute()
    {
        if ($this->type === self::TYPE_CUSTOM && $this->custom_reason) {
            return $this->custom_reason;
        }
        return $this->reason;
    }

    // Metodi per creare segnalazioni
    public static function reportUser($reporterId, $reportedUserId, $type, $reason, $description = null, $priority = self::PRIORITY_MEDIUM)
    {
        return self::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $reportedUserId,
            'type' => $type,
            'reason' => $reason,
            'description' => $description,
            'status' => self::STATUS_PENDING,
            'priority' => $priority,
        ]);
    }

    public static function reportVideo($reporterId, $videoId, $type, $reason, $description = null, $priority = self::PRIORITY_MEDIUM)
    {
        $video = Video::findOrFail($videoId);
        
        return self::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $video->user_id,
            'video_id' => $videoId,
            'type' => $type,
            'reason' => $reason,
            'description' => $description,
            'status' => self::STATUS_PENDING,
            'priority' => $priority,
        ]);
    }

    public static function reportComment($reporterId, $commentId, $type, $reason, $description = null, $priority = self::PRIORITY_MEDIUM)
    {
        $comment = Comment::findOrFail($commentId);
        
        return self::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $comment->user_id,
            'comment_id' => $commentId,
            'type' => $type,
            'reason' => $reason,
            'description' => $description,
            'status' => self::STATUS_PENDING,
            'priority' => $priority,
        ]);
    }

    // Metodo per segnalare un canale
    public static function reportChannel($reporterId, $channelId, $type, $reason, $description = null, $priority = self::PRIORITY_MEDIUM)
    {
        $channel = User::findOrFail($channelId);
        
        return self::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $channelId,
            'channel_id' => $channelId,
            'type' => $type,
            'reason' => $reason,
            'description' => $description,
            'status' => self::STATUS_PENDING,
            'priority' => $priority,
        ]);
    }

    // Metodo per creare segnalazione personalizzata
    public static function reportCustom($reporterId, $targetType, $targetId, $customReason, $description = null, $priority = self::PRIORITY_MEDIUM)
    {
        return match ($targetType) {
            'user' => self::reportUser($reporterId, $targetId, self::TYPE_CUSTOM, $customReason, $description, $priority),
            'video' => self::reportVideo($reporterId, $targetId, self::TYPE_CUSTOM, $customReason, $description, $priority),
            'comment' => self::reportComment($reporterId, $targetId, self::TYPE_CUSTOM, $customReason, $description, $priority),
            'channel' => self::reportChannel($reporterId, $targetId, self::TYPE_CUSTOM, $customReason, $description, $priority),
            default => null,
        };
    }

    // Metodi per gestire le segnalazioni
    public function assignTo($adminId)
    {
        $this->update([
            'admin_id' => $adminId,
            'status' => self::STATUS_REVIEWED,
        ]);
    }

    public function markAsResolved($action, $adminNotes = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution_action' => $action,
            'admin_notes' => $adminNotes,
            'resolved_at' => now(),
        ]);
    }

    public function dismiss($adminNotes = null)
    {
        $this->update([
            'status' => self::STATUS_DISMISSED,
            'admin_notes' => $adminNotes,
            'resolved_at' => now(),
        ]);
    }

    public function escalate($adminNotes = null)
    {
        $this->update([
            'status' => self::STATUS_ESCALATED,
            'admin_notes' => $adminNotes,
        ]);
    }

    // Report predefiniti per i diversi target
    public static function getPresetReasons($targetType)
    {
        $commonReasons = [
            ['value' => 'spam', 'label' => 'Spam', 'icon' => 'fa-trash'],
            ['value' => 'harassment', 'label' => 'Molestie/Bullismo', 'icon' => 'fa-bullhorn'],
            ['value' => 'inappropriate_content', 'label' => 'Contenuto inappropriato', 'icon' => 'fa-exclamation-triangle'],
        ];

        $videoReasons = [
            ...$commonReasons,
            ['value' => 'copyright', 'label' => 'Violazione copyright', 'icon' => 'fa-copyright'],
            ['value' => 'fake_information', 'label' => 'Informazioni false', 'icon' => 'fa-false'],
            ['value' => 'misleading_title', 'label' => 'Titolo fuorviante', 'icon' => 'fa-tag'],
            ['value' => 'violence', 'label' => 'Violenza', 'icon' => 'fa-exclamation-circle'],
        ];

        $channelReasons = [
            ...$commonReasons,
            ['value' => 'impersonation', 'label' => 'Impersonificazione', 'icon' => 'fa-user-secret'],
            ['value' => 'misleading_bio', 'label' => 'Bio fuorviante', 'icon' => 'fa-info-circle'],
            ['value' => 'scam', 'label' => 'Truffa/Frode', 'icon' => 'fa-exclamation-triangle'],
        ];

        $commentReasons = [
            ...$commonReasons,
            ['value' => 'hate_speech', 'label' => 'Discorso d\'odio', 'icon' => 'fa-frown'],
            ['value' => 'harassment', 'label' => 'Molestie', 'icon' => 'fa-bullhorn'],
        ];

        return match ($targetType) {
            'video' => $videoReasons,
            'channel' => $channelReasons,
            'comment' => $commentReasons,
            default => $commonReasons,
        };
    }

    // Statistiche
    public static function getStats($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', self::STATUS_PENDING)->count(),
            'resolved' => $query->clone()->where('status', self::STATUS_RESOLVED)->count(),
            'dismissed' => $query->clone()->where('status', self::STATUS_DISMISSED)->count(),
            'by_type' => $query->clone()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => $query->clone()
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->get()
                ->pluck('count', 'priority')
                ->toArray(),
            'by_target' => $query->clone()
                ->selectRaw('
                    CASE 
                        WHEN channel_id IS NOT NULL THEN "channel"
                        WHEN video_id IS NOT NULL THEN "video"
                        WHEN comment_id IS NOT NULL THEN "comment"
                        ELSE "user"
                    END as target_type, COUNT(*) as count
                ')
                ->groupBy('target_type')
                ->get()
                ->pluck('count', 'target_type')
                ->toArray(),
        ];
    }

    public static function getUserReportHistory($userId, $type = 'reporter')
    {
        $query = $type === 'reporter' 
            ? self::where('reporter_id', $userId)
            : self::where('reported_user_id', $userId);
            
        return $query->with(['reporter', 'reportedUser', 'video', 'comment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public static function getPendingReports($limit = null)
    {
        $query = self::where('status', self::STATUS_PENDING)
            ->with(['reporter', 'reportedUser', 'video', 'comment'])
            ->orderByRaw('CASE priority 
                WHEN "urgent" THEN 1 
                WHEN "high" THEN 2 
                WHEN "medium" THEN 3 
                WHEN "low" THEN 4 
                END')
            ->orderBy('created_at', 'asc');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    public static function autoClassifyPriority($type, $content)
    {
        // Classificazione automatica della priorità basata sul tipo e contenuto
        $urgentTypes = [self::TYPE_HARASSMENT, self::TYPE_COPYRIGHT];
        $highTypes = [self::TYPE_INAPPROPRIATE_CONTENT, self::TYPE_FAKE_INFORMATION];
        
        if (in_array($type, $urgentTypes)) {
            return self::PRIORITY_URGENT;
        }
        
        if (in_array($type, $highTypes)) {
            return self::PRIORITY_HIGH;
        }
        
        // Analisi del contenuto per segnali di urgenza
        if (is_string($content)) {
            $urgentKeywords = ['bombe', 'terrorismo', 'pedofilia', 'omicidio', 'violenza'];
            $highKeywords = ['minaccia', 'odio', 'razzismo', 'bullismo'];
            
            $contentLower = strtolower($content);
            
            foreach ($urgentKeywords as $keyword) {
                if (strpos($contentLower, $keyword) !== false) {
                    return self::PRIORITY_URGENT;
                }
            }
            
            foreach ($highKeywords as $keyword) {
                if (strpos($contentLower, $keyword) !== false) {
                    return self::PRIORITY_HIGH;
                }
            }
        }
        
        return self::PRIORITY_MEDIUM;
    }
}
