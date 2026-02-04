<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WatchHistory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail_path',
        'video_path',
        'video_url',
        'duration',
        'views_count',
        'likes_count',
        'dislikes_count',
        'comments_count',
        'status',
        'is_public',
        'is_featured',
        'is_reel',
        'comments_enabled',
        'likes_enabled',
        'comments_require_approval',
        'video_quality',
        'video_format',
        'file_size',
        'tags',
        'language',
        'published_at',
        'moderation_reason',
        'original_file_path',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'is_reel' => 'boolean',
        'comments_enabled' => 'boolean',
        'likes_enabled' => 'boolean',
        'comments_require_approval' => 'boolean',
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'video_url';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relazione con le qualità video disponibili
     */
    public function videoQualities(): HasMany
    {
        return $this->hasMany(VideoQuality::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getUserReaction(?int $userId): ?string
    {
        if (!$userId) return null;

        try {
            $like = $this->likes()->where('user_id', $userId)->first();
            $reaction = $like?->reaction;



            return $reaction;
        } catch (\Exception $e) {


            return null;
        }
    }

    public function toggleLike(int $userId)
    {
        try {
            $like = $this->likes()->where('user_id', $userId)->first();

            if ($like?->reaction === 'like') {
                $like->delete();
                $this->decrement('likes_count');
            } else {
                if ($like?->reaction === 'dislike') {
                    $this->decrement('dislikes_count');
                    $like->update(['reaction' => 'like']);
                } else {
                    $this->likes()->create([
                        'user_id' => $userId,
                        'reaction' => 'like'
                    ]);
                }
                $this->increment('likes_count');
            }

            $this->refresh();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function toggleDislike(int $userId)
    {
        try {
            $like = $this->likes()->where('user_id', $userId)->first();

            if ($like?->reaction === 'dislike') {
                $like->delete();
                $this->decrement('dislikes_count');
            } else {
                if ($like?->reaction === 'like') {
                    $this->decrement('likes_count');
                    $like->update(['reaction' => 'dislike']);
                } else {
                    $this->likes()->create([
                        'user_id' => $userId,
                        'reaction' => 'dislike'
                    ]);
                }
                $this->increment('dislikes_count');
            }

            $this->refresh();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function playlistVideos(): HasMany
    {
        return $this->hasMany(PlaylistVideo::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_videos')->withPivot('position');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('is_public', true)
            ->whereNotNull('published_at');
    }

    public function scopeTrending($query)
    {
        return $query->published()
            ->orderBy('views_count', 'desc')
            ->limit(10);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->published()
            ->where('published_at', '>=', now()->subDays($days))
            ->orderBy('published_at', 'desc');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intval($this->duration / 3600);
        $minutes = intval(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return asset('storage/' . $this->thumbnail_path);
    }

    public function getVideoFileUrlAttribute(): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        return asset($this->video_path);
    }

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

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->where('reaction', 'like')->count();
    }

    public function getDislikesCountAttribute(): int
    {
        return $this->likes()->where('reaction', 'dislike')->count();
    }

    /**
     * Ottiene tutte le qualità video disponibili per questo video
     * Formato compatibile con Artplayer: array di oggetti con default, html, url
     */
    public function getAvailableQualities(): array
    {
        $dbQualities = $this->videoQualities()
            ->available()
            ->byQuality()
            ->get()
            ->filter(function ($quality) {
                return $quality->fileExists();
            });

        if ($dbQualities->isNotEmpty()) {
            $qualities = $dbQualities->map(function ($quality, $index) {
                return [
                    'default' => $index === 0,
                    'html' => $quality->label,
                    'url' => $quality->public_url,
                    'quality' => $quality->quality,
                    'id' => $quality->id,
                    'file_size' => $quality->file_size,
                    'formatted_file_size' => $quality->formatted_file_size,
                    'width' => $quality->width,
                    'height' => $quality->height,
                    'resolution' => $quality->resolution,
                    'bitrate' => $quality->bitrate,
                ];
            })->toArray();

            return $this->ensureOriginalQuality($qualities);
        }

        $qualities = $this->detectQualitiesFromFiles();

        return $this->ensureOriginalQuality($qualities);
    }

    /**
     * Ottiene la qualità video corrente (quella salvata nel campo video_path)
     */
    public function getCurrentQuality(): ?array
    {
        if (!$this->video_path) {
            return null;
        }

        $qualities = $this->getAvailableQualities();

        if (empty($qualities)) {
            return null;
        }

        $currentQuality = $this->extractQuality();

        return $qualities[$currentQuality] ?? reset($qualities) ?: null;
    }

    /**
     * Ottiene la qualità preferita per l'utente corrente
     */
    public function getPreferredQuality(?int $userId = null): ?array
    {
        return $this->getCurrentQuality();
    }

    /**
     * Salva le qualità video nel database dopo il transcoding
     */
    public function saveVideoQualities(array $qualitiesData): void
    {
        foreach ($qualitiesData as $quality => $data) {
            VideoQuality::createForVideo($this, $quality, $data);
        }
    }

    /**
     * Costruisce il path del video per una qualità specifica
     */
    private function buildVideoPath(string $videoId, string $quality): string
    {
        return "video_{$videoId}_{$quality}.mp4";
    }

    /**
     * Ottiene l'ID video in modo sicuro
     */
    private function getVideoId(): string
    {
        $id = (string) $this->id;

        if ($this->video_path) {
            if (preg_match('/^video_(\d+)_.*\.mp4$/', $this->video_path, $matches)) {
                return $matches[1];
            }

            $cleanPath = preg_replace('/^videos\//', '', $this->video_path);
            if (preg_match('/^video_(\d+)_.*\.mp4$/', $cleanPath, $matches)) {
                return $matches[1];
            }
        }

        return $id;
    }

    /**
     * Estrae la qualità video corrente
     */
    private function extractQuality(): string
    {
        if (!$this->video_path) {
            return 'original';
        }

        $path = $this->video_path;

        $path = preg_replace('/^videos\//', '', $path);

        if (preg_match('/^video_\d+_(.+)\.mp4$/', $path, $matches)) {
            return $matches[1];
        }

        return 'original';
    }

    /**
     * Verifica se un file video esiste
     */
    private function videoFileExists(string $videoPath): bool
    {
        $fullPath = public_path('storage/videos/' . $videoPath);
        return file_exists($fullPath);
    }

    /**
     * Ottiene l'URL del video per una qualità specifica
     */
    public function getVideoUrlForQuality(string $quality): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        $videoId = $this->getVideoId();
        $videoPath = $this->buildVideoPath($videoId, $quality);

        if ($this->videoFileExists($videoPath)) {
            return asset('storage/videos/' . $videoPath);
        }

        return null;
    }

    /**
     * Rileva le qualità video dai file fisici presenti nella cartella videos
     * Formato compatibile con Artplayer: array di oggetti con default, html, url
     */
    private function detectQualitiesFromFiles(): array
    {
        $videoId = $this->id;
        $videosDir = public_path('storage/videos/');
        $qualities = [];

        $supportedQualities = [
            'original' => 'Originale',
            '2160p' => '2160p 4K Ultra HD',
            '1440p' => '1440p 2K QHD',
            '1080p' => '1080p Full HD',
            '720p' => '720p HD',
            '480p' => '480p',
            '360p' => '360p'
        ];

        $index = 0;
        foreach ($supportedQualities as $quality => $label) {
            $fileName = "video_{$videoId}_{$quality}.mp4";
            $filePath = $videosDir . $fileName;

            $fileExists = file_exists($filePath);

            if ($fileExists) {
                $fileSize = filesize($filePath);
                $assetUrl = asset('storage/videos/' . $fileName);

                $qualities[] = [
                    'default' => $index === 0,
                    'html' => $label,
                    'url' => $assetUrl,
                    'quality' => $quality,
                    'id' => null,
                    'file_size' => $fileSize,
                    'formatted_file_size' => $this->formatBytes($fileSize),
                    'width' => $this->getQualityWidth($quality),
                    'height' => $this->getQualityHeight($quality),
                    'resolution' => $this->getQualityWidth($quality) . 'x' . $this->getQualityHeight($quality),
                    'bitrate' => null,
                ];
                $index++;
            }
        }

        return $qualities;
    }

    /**
     * Ottiene la larghezza in pixel per una qualità
     */
    private function getQualityWidth(string $quality): ?int
    {
        $widths = [
            '2160p' => 3840,
            '1440p' => 2560,
            '1080p' => 1920,
            '720p' => 1280,
            '480p' => 854,
            '360p' => 640,
        ];

        return $widths[$quality] ?? null;
    }

    /**
     * Ottiene l'altezza in pixel per una qualità
     */
    private function getQualityHeight(string $quality): ?int
    {
        $heights = [
            '2160p' => 2160,
            '1440p' => 1440,
            '1080p' => 1080,
            '720p' => 720,
            '480p' => 480,
            '360p' => 360,
        ];

        return $heights[$quality] ?? null;
    }

    /**
     * Formatta i byte in formato leggibile
     */
    private function formatBytes($bytes): string
    {
        if (!$bytes || $bytes <= 0) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Assicura che ci sia sempre almeno la qualità originale disponibile
     * Formato compatibile con Artplayer: array di oggetti con default, html, url
     */
    private function ensureOriginalQuality(array $qualities): array
    {
        // Se non ci sono qualità disponibili
        if (empty($qualities)) {
            // Verifica se esiste il file video originale
            $originalPath = $this->video_path;

            if ($originalPath) {
                // Costruisci il percorso completo del file
                $fullPath = public_path('storage/' . $originalPath);

                if (file_exists($fullPath)) {
                    $fileSize = filesize($fullPath);

                    // Aggiungi la qualità originale come fallback
                    $qualities[] = [
                        'default' => true, // È l'unica qualità, quindi è di default
                        'html' => 'Originale',
                        'url' => asset('storage/' . $originalPath),
                        'quality' => 'original',
                        'id' => null,
                        'file_size' => $fileSize,
                        'formatted_file_size' => $this->formatBytes($fileSize),
                        'width' => null,
                        'height' => null,
                        'resolution' => null,
                        'bitrate' => null,
                        'is_fallback' => true, // Flag per indicare che è un fallback
                    ];
                }
            }
        }

        return $qualities;
    }

    /**
     * Verifica se i commenti sono abilitati per questo video
     */
    public function areCommentsEnabled(): bool
    {
        // Controlla prima l'impostazione globale
        $globalEnabled = \App\Models\Setting::getValue('enable_comments', true);
        if (!$globalEnabled) {
            return false;
        }

        // Poi controlla l'impostazione specifica del video
        return $this->comments_enabled ?? true;
    }

    /**
     * Verifica se i like sono abilitati per questo video
     */
    public function areLikesEnabled(): bool
    {
        // Controlla prima l'impostazione globale
        $globalEnabled = \App\Models\Setting::getValue('enable_likes', true);
        if (!$globalEnabled) {
            return false;
        }

        // Poi controlla l'impostazione specifica del video
        return $this->likes_enabled ?? true;
    }

    /**
     * Verifica se i commenti richiedono approvazione per questo video
     */
    public function commentsRequireApproval(): bool
    {
        return $this->comments_require_approval ?? false;
    }

    /**
     * Scope per ottenere solo i commenti approvati o pending per l'autore
     */
    public function approvedComments()
    {
        return $this->comments()->where('status', 'approved');
    }

    /**
     * Scope per ottenere tutti i commenti visibili (approvati + pending se è il proprietario)
     */
    public function visibleComments($userId = null)
    {
        $query = $this->comments()->where('status', 'approved');

        // Se è il proprietario del video, può vedere anche i commenti pending
        if ($userId && $this->user_id === $userId) {
            $query->orWhere('status', 'pending');
        }

        return $query;
    }

    /**
     * Relazione con i watch later
     */
    public function watchLater(): HasMany
    {
        return $this->hasMany(WatchLater::class);
    }

    /**
     * Ottiene i sottotitoli disponibili per questo video
     */
    public function getAvailableSubtitles(): array
    {
        $subtitles = [];
        $videoId = $this->id;

        // Cartella dove cercare i sottotitoli
        $subtitleFolder = 'subtitles/';

        // Pattern di lingue supportate
        $supportedLanguages = [
            'it' => 'Italiano',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'pt' => 'Português',
            'ru' => 'Русский',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
        ];

        // Cerca i file di sottotitoli per ogni lingua supportata
        foreach ($supportedLanguages as $langCode => $langName) {
            // Pattern: video_{id}_{lang}.vtt
            $fileName = "video_{$videoId}_{$langCode}.vtt";
            $storagePath = $subtitleFolder . $fileName;

            // Verifica se il file esiste
            if (Storage::disk('public')->exists($storagePath)) {
                $subtitles[] = [
                    'url' => asset('storage/' . $storagePath),
                    'language' => $langCode,
                    'label' => $langName,
                    'type' => 'vtt',
                ];
            }
        }

        // Cerca anche sottotitoli generici (senza codice lingua)
        $genericFileName = "video_{$videoId}.vtt";
        $genericStoragePath = $subtitleFolder . $genericFileName;

        if (Storage::disk('public')->exists($genericStoragePath)) {
            $subtitles[] = [
                'url' => asset('storage/' . $genericStoragePath),
                'language' => 'default',
                'label' => 'Sottotitoli',
                'type' => 'vtt',
            ];
        }

        return $subtitles;
    }
}
