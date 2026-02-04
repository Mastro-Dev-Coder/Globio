<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'position',
        'content',
        'code',
        'image_url',
        'link_url',
        'is_active',
        'start_date',
        'end_date',
        'clicks',
        'views',
        'priority',
        'language'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'clicks' => 'integer',
        'views' => 'integer',
        'priority' => 'integer',
    ];

    // Tipi di pubblicità
    const TYPE_BANNER = 'banner';
    const TYPE_ADSENSE = 'adsense';
    const TYPE_VIDEO = 'video';

    // Posizioni disponibili
    const POSITION_FOOTER = 'footer';
    const POSITION_BETWEEN_VIDEOS = 'between_videos';
    const POSITION_HOME_VIDEO = 'home_video';
    const POSITION_VIDEO_OVERLAY = 'video_overlay';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeCurrentlyActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where(function ($q) use ($language) {
            $q->where('language', $language)
              ->orWhereNull('language');
        });
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementClicks()
    {
        $this->increment('clicks');
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_BANNER => 'Banner',
            self::TYPE_ADSENSE => 'AdSense',
            self::TYPE_VIDEO => 'Video',
            default => 'Sconosciuto'
        };
    }

    public function getPositionLabelAttribute()
    {
        return match($this->position) {
            self::POSITION_FOOTER => 'Footer',
            self::POSITION_BETWEEN_VIDEOS => 'Tra i Video',
            self::POSITION_HOME_VIDEO => 'Video Home',
            self::POSITION_VIDEO_OVERLAY => 'Overlay Video',
            default => 'Sconosciuta'
        };
    }

    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }
}