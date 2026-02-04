<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VideoQuality extends Model
{
    protected $fillable = [
        'video_id',
        'quality',
        'label',
        'file_path',
        'file_url',
        'file_size',
        'width',
        'height',
        'bitrate',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'bitrate' => 'integer',
    ];

    /**
     * Relazione con il video
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Ottiene l'URL pubblico del file video
     */
    public function getPublicUrlAttribute(): string
    {
        if (!$this->file_path) {
            return '';
        }

        // Usa Storage::url() per ottenere l'URL corretto con il prefisso /storage/
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Ottiene le dimensioni del file formattate
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Ottiene le informazioni sulla risoluzione
     */
    public function getResolutionAttribute(): string
    {
        if ($this->width && $this->height) {
            return $this->width . 'x' . $this->height;
        }

        return 'N/A';
    }

    /**
     * Verifica se il file esiste fisicamente
     */
    public function fileExists(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Scope per ottenere solo le qualità disponibili
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope per ordinare per qualità (dal più alto al più basso)
     */
    public function scopeByQuality($query)
    {
        $qualityOrder = [
            'original' => 0,
            '2160p' => 1,
            '1440p' => 2,
            '1080p' => 3,
            '720p' => 4,
            '480p' => 5,
            '360p' => 6,
        ];

        return $query->orderByRaw("FIELD(quality, 'original', '2160p', '1440p', '1080p', '720p', '480p', '360p') ASC");
    }

    /**
     * Factory method per creare una qualità video
     */
    public static function createForVideo(Video $video, string $quality, array $data): self
    {
        $qualityLabels = [
            'original' => 'Originale',
            '2160p' => '2160p 4K Ultra HD',
            '1440p' => '1440p 2K QHD',
            '1080p' => '1080p Full HD',
            '720p' => '720p HD',
            '480p' => '480p',
            '360p' => '360p',
        ];

        return self::create([
            'video_id' => $video->id,
            'quality' => $quality,
            'label' => $qualityLabels[$quality] ?? $quality,
            'file_path' => $data['file_path'] ?? null,
            'file_url' => $data['file_url'] ?? null,
            'file_size' => $data['file_size'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'bitrate' => $data['bitrate'] ?? null,
            'is_available' => $data['is_available'] ?? true,
        ]);
    }
}