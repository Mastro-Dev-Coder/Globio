<?php

namespace App\Services;

use App\Jobs\CompleteVideoJob;
use App\Jobs\ProcessVideoJob;
use App\Models\Setting;
use App\Models\Video;
use App\Notifications\VideoProcessingCompletedNotification;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    private FFMpeg $ffmpeg;
    private FFProbe $ffprobe;

    private string $tempPath;
    private string $videoPath;
    private string $thumbnailPath;

    public function __construct()
    {
        $this->tempPath = 'temp/videos';
        $this->videoPath = 'videos';
        $this->thumbnailPath = 'thumbnails';
        
        $this->ensureStorageDirectoryExists('videos');
        $this->ensureStorageDirectoryExists('thumbnails');
    }

    /**
     * Assicura che una directory esista nello storage public
     */
    private function ensureStorageDirectoryExists(string $directory): void
    {
        $fullPath = "{$directory}";
        if (!Storage::disk('public')->exists($fullPath)) {
            Storage::disk('public')->makeDirectory($fullPath);
            Log::info("Directory creata nello storage: {$fullPath}");
        }
    }

    /**
     * Assicura che una directory esista, creandola se necessario
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \Exception("Impossibile creare la directory: {$directory}");
            }
            Log::info("Directory creata: {$directory}");
        }
    }

    /**
     * Upload video iniziale (senza processamento)
     */
    public function uploadVideo(UploadedFile $file, array $data, int $userId): Video
    {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $tempFilePath = $file->storeAs($this->tempPath, $fileName, 'local');

        try {
            $fileSize = $file->getSize();
            $videoFormat = strtolower($file->getClientOriginalExtension());

            // Prendi il valore is_reel specificato dall'utente, ma sarà sovrascritto durante il processamento
            $userSpecifiedIsReel = $data['is_reel'] ?? false;

            $video = Video::create([
                'user_id'        => $userId,
                'title'          => $data['title'],
                'description'    => $data['description'] ?? null,
                'status'         => 'processing',
                'is_public'      => $data['is_public'] ?? true,
                'language'       => $data['language'] ?? 'it',
                'tags'           => isset($data['tags']) ? explode(',', $data['tags']) : null,
                'video_path'     => $this->tempPath . '/' . $fileName,
                'video_url'      => Str::upper(Str::random(11)),
                'file_size'      => $fileSize,
                'video_format'   => $videoFormat,
                'duration'       => 0,
                'views_count'    => 0,
                'likes_count'    => 0,
                'dislikes_count' => 0,
                'comments_count' => 0,
                'is_featured'    => false,
                'is_reel'        => $userSpecifiedIsReel, // Inizialmente impostato dall'utente, sarà sovrascritto automaticamente
                'video_quality'  => null,
                'published_at'   => null,
                'moderation_reason' => null,
            ]);

            dispatch(new ProcessVideoJob($video->id, $tempFilePath));

            return $video;
        } catch (\Exception $e) {
            Storage::disk('local')->delete($tempFilePath);
            throw $e;
        }
    }

    /**
     * Inizializza FFMpeg e FFProbe (UNICA volta)
     */
    public function initializeFFmpeg(): void
    {
        // Recupera i percorsi dai settings
        $ffmpegPath  = Setting::getValue('ffmpeg_path');
        $ffprobePath = Setting::getValue('ffprobe_path');

        // Su Windows, mantieni i backslash originali per file_exists()
        // ma normalizza per la configurazione FFmpeg
        $ffmpegPathOriginal  = $ffmpegPath;
        $ffprobePathOriginal = $ffprobePath;

        // Normalizza i separatori solo per la configurazione FFmpeg
        $ffmpegPathNormalized  = str_replace('\\', '/', $ffmpegPath);
        $ffprobePathNormalized = str_replace('\\', '/', $ffprobePath);

        // Verifica che i file esistano (usa percorso originale per Windows)
        if (!file_exists($ffmpegPathOriginal)) {
            throw new \Exception("Percorso FFmpeg non trovato: {$ffmpegPathOriginal}");
        }

        if (!file_exists($ffprobePathOriginal)) {
            throw new \Exception("Percorso FFProbe non trovato: {$ffprobePathOriginal}");
        }

        // Su Windows, verifica che siano file .exe
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            if (!preg_match('/\.exe$/i', $ffmpegPathOriginal)) {
                throw new \Exception("Su Windows, FFmpeg deve essere un file .exe: {$ffmpegPathOriginal}");
            }
            if (!preg_match('/\.exe$/i', $ffprobePathOriginal)) {
                throw new \Exception("Su Windows, FFProbe deve essere un file .exe: {$ffprobePathOriginal}");
            }
        }

        Log::info('Inizializzazione FFmpeg con binari custom', [
            'ffmpeg'  => $ffmpegPathOriginal,
            'ffprobe' => $ffprobePathOriginal,
            'os' => PHP_OS
        ]);

        // Configurazione comune per FFmpeg e FFProbe
        // Usa i percorsi normalizzati per la libreria FFmpeg
        $config = [
            'ffmpeg.binaries'  => $ffmpegPathNormalized,
            'ffprobe.binaries' => $ffprobePathNormalized,
            'timeout'          => Setting::getValue('ffmpeg_timeout', 3600),
            'ffmpeg.threads'   => Setting::getValue('ffmpeg_threads', 12),
        ];

        // Crea l'istanza FFmpeg
        $this->ffmpeg = FFMpeg::create($config);

        // Crea l'istanza FFProbe **usando la stessa configurazione**
        $this->ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => $ffmpegPathNormalized,
            'ffprobe.binaries' => $ffprobePathNormalized,
        ]);
    }

    /**
     * Processamento del video (Queue Job)
     */
    public function processVideo(int $videoId, string $tempFilePath): void
    {
        $video = Video::findOrFail($videoId);

        // risolvo percorso assoluto da usare con FFmpeg/FFProbe
        $absoluteTempPath = $this->resolveInputPath($tempFilePath);

        Log::info('Percorsi risolti per processamento', [
            'video_id' => $videoId,
            'temp_file_path_relativo' => $tempFilePath,
            'temp_file_path_assoluto' => $absoluteTempPath,
            'file_exists' => file_exists($absoluteTempPath) ? 'SI' : 'NO'
        ]);

        $shouldDeleteTempFile = false;

        try {
            // verifica che il file esista davvero
            if (!file_exists($absoluteTempPath)) {
                Log::error('File temporaneo non trovato per processamento', [
                    'video_id' => $videoId,
                    'expected_path' => $absoluteTempPath,
                    'original' => $tempFilePath,
                ]);

                $video->update([
                    'status' => 'rejected',
                    'moderation_reason' => "File temporaneo non trovato: {$absoluteTempPath}",
                ]);

                return;
            }

            $this->initializeFFmpeg();

            // PASSARE SEMPRE il percorso assoluto alle funzioni che leggono il file
            $videoInfo = $this->getVideoInfo($absoluteTempPath);
            $thumbnailPath = $this->generateThumbnail($absoluteTempPath, $video->id);
            $processedPaths = $this->transcodeVideo($absoluteTempPath, $video->id, $videoInfo);

            $defaultQuality = Setting::getValue('default_video_quality', '720p');
            $videoPath = $processedPaths[$defaultQuality] ?? $processedPaths['720p'] ?? $processedPaths['original'];

            Log::info('Dati per aggiornamento database', [
                'video_id' => $videoId,
                'video_path_da_salvare' => $videoPath,
                'thumbnail_path_da_salvare' => $thumbnailPath,
                'default_quality' => $defaultQuality,
                'processed_paths' => $processedPaths,
                'video_path_inizia_con_public' => str_starts_with($videoPath, 'public/'),
                'thumbnail_path_inizia_con_public' => str_starts_with($thumbnailPath, 'public/')
            ]);

            // Salva il file originale per poter riprocessare in caso di errore
            $originalFilePath = $this->saveOriginalFile($absoluteTempPath, $video->id);
            
            // Rimuovi il prefisso 'public/' se presente per garantire percorsi puliti
            $cleanVideoPath = str_starts_with($videoPath, 'public/') ? substr($videoPath, 7) : $videoPath;
            $cleanThumbnailPath = str_starts_with($thumbnailPath, 'public/') ? substr($thumbnailPath, 7) : $thumbnailPath;
            $cleanOriginalPath = str_starts_with($originalFilePath, 'public/') ? substr($originalFilePath, 7) : $originalFilePath;

            Log::info('Controllo e pulizia percorsi prima del salvataggio DB', [
                'video_path_originale' => $videoPath,
                'video_path_pulito' => $cleanVideoPath,
                'thumbnail_path_originale' => $thumbnailPath,
                'thumbnail_path_pulito' => $cleanThumbnailPath,
                'original_path_originale' => $originalFilePath,
                'original_path_pulito' => $cleanOriginalPath,
                'pulizia_applicata' => ($cleanVideoPath !== $videoPath) || ($cleanThumbnailPath !== $thumbnailPath) || ($cleanOriginalPath !== $originalFilePath)
            ]);

            // Determina automaticamente se il video è un reel basato sulla risoluzione
            $isReel = $this->isReelByResolution($videoInfo['width'], $videoInfo['height']);

            Log::info('Determinazione automatica tipo video', [
                'video_id' => $videoId,
                'width' => $videoInfo['width'],
                'height' => $videoInfo['height'],
                'is_reel_determinato' => $isReel,
                'rapporto_aspetto' => round($videoInfo['width'] / $videoInfo['height'], 3)
            ]);

            // Salva le informazioni tecniche e i percorsi nel database
            $videoUpdated = $video->update([
                'video_quality'  => $defaultQuality,
                'video_format'   => 'mp4',
                'file_size'      => filesize($absoluteTempPath),
                'duration'       => (int) $videoInfo['duration'],
                'status'         => 'transcoding',
                'video_path'     => $cleanVideoPath,
                'thumbnail_path' => $cleanThumbnailPath,
                'original_file_path' => $cleanOriginalPath,
                'is_reel'        => $isReel, // Impostazione automatica basata sulla risoluzione
            ]);

            // Forza il refresh e verifica che i dati siano stati salvati
            $video->refresh();

            // Salva le qualità video nel database
            $this->saveVideoQualitiesToDatabase($video, $processedPaths, $videoInfo);

            Log::info('Video processato e percorsi salvati nel database', [
                'video_id' => $videoId,
                'update_success' => $videoUpdated,
                'video_path_nel_db' => $video->video_path,
                'thumbnail_path_nel_db' => $video->thumbnail_path,
                'original_file_path_nel_db' => $video->original_file_path,
                'status_nel_db' => $video->status,
                'video_path_contiene_public' => str_contains($video->video_path, 'public/'),
                'thumbnail_path_contiene_public' => str_contains($video->thumbnail_path, 'public/'),
                'original_path_contiene_public' => str_contains($video->original_file_path, 'public/')
            ]);

            // Verifica che i percorsi siano stati effettivamente salvati
            if (!$video->video_path || !$video->thumbnail_path) {
                Log::error('Errore: percorsi video non salvati correttamente nel database', [
                    'video_id' => $videoId,
                    'video_path' => $video->video_path,
                    'thumbnail_path' => $video->thumbnail_path,
                    'percorsi_previsti' => [
                        'video' => $videoPath,
                        'thumbnail' => $thumbnailPath
                    ]
                ]);
                throw new \Exception("Percorsi video non salvati correttamente nel database");
            }

            // Invia notifica di processamento completato
            $user = $video->user;
            if ($user) {
                $user->notify(new VideoProcessingCompletedNotification($video));
            }

            $shouldDeleteTempFile = true;

            Log::info('Invio CompleteVideoJob', ['video_id' => $videoId]);
            dispatch(new CompleteVideoJob($video->id));
        } catch (\Exception $e) {
            Log::error('Errore durante processamento video', [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
                'file_path' => $absoluteTempPath
            ]);

            $video->update([
                'status' => 'rejected',
                'moderation_reason' => 'Errore transcoding FFmpeg: ' . $e->getMessage(),
            ]);

            throw $e;
        } finally {
            if ($shouldDeleteTempFile) {
                try {
                    if (file_exists($absoluteTempPath)) {
                        @unlink($absoluteTempPath);
                        Log::info('File temporaneo cancellato con successo', [
                            'path' => $absoluteTempPath
                        ]);
                    }
                } catch (\Throwable $ex) {
                    Log::warning('Impossibile cancellare file temporaneo', [
                        'path' => $absoluteTempPath,
                        'error' => $ex->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Risolve un percorso passato (relativo come "temp/videos/xxx.mp4" o già assoluto)
     * e restituisce un percorso assoluto valido per il filesystem.
     */
    private function resolveInputPath(string $path): string
    {
        // Normalizza i separatori
        $path = str_replace('\\', '/', $path);

        if (preg_match('#^[A-Za-z]:/#', $path) || Str::startsWith($path, ['/'])) {
            return $path;
        }

        // Se il path inizia con 'temp/', usa Storage per il file temporaneo
        if (Str::startsWith($path, 'temp/')) {
            return Storage::disk('local')->path($path);
        }

        // Per altri percorsi, usa il path relativo
        return base_path($path);
    }

    /**
     * Info video (usa SEMPRE la stessa istanza)
     */
    private function getVideoInfo(string $filePath): array
    {
        Log::info('Tentativo di leggere info video', [
            'file_path' => $filePath,
            'file_exists' => file_exists($filePath) ? 'SI' : 'NO'
        ]);

        $videoStream = $this->ffprobe->streams($filePath)->videos()->first();
        $audioStream = $this->ffprobe->streams($filePath)->audios()->first();

        return [
            'duration' => (float) $this->ffprobe->format($filePath)->get('duration'),
            'width'    => $videoStream ? (int) $videoStream->get('width') : 1920,
            'height'   => $videoStream ? (int) $videoStream->get('height') : 1080,
            'codec'    => $videoStream ? $videoStream->get('codec_name') : 'unknown',
            'bitrate'  => (int) $this->ffprobe->format($filePath)->get('bit_rate'),
            'fps'      => $videoStream ? eval('return ' . $videoStream->get('r_frame_rate') . ';') : 30,
        ];
    }

    /**
     * Determina se un video è un reel basato sulla risoluzione
     * I reel sono tipicamente video verticali (formato 9:16 o simile)
     */
    private function isReelByResolution(int $width, int $height): bool
    {
        // Se l'altezza è maggiore della larghezza, è un video verticale (reel)
        if ($height > $width) {
            return true;
        }

        // Controlla anche il rapporto di aspetto per video più quadrati ma ancora verticali
        // Reel tipici: 9:16 (0.5625), 3:4 (0.75), 1:1 (1.0) - ma noi vogliamo solo quelli verticali
        $aspectRatio = $width / $height;
        
        // Se il rapporto è <= 0.8, è probabilmente un reel (più alto che largo)
        // Esempi: 720x1280 (0.56), 1080x1920 (0.56), 480x854 (0.56)
        return $aspectRatio <= 0.8;
    }

    /**
     * Genera thumbnail
     */
    private function generateThumbnail(string $tempFilePath, int $videoId): ?string
    {
        try {
            $thumbnailFileName = "video_{$videoId}_thumb.jpg";
            $thumbnailPath = "thumbnails/{$thumbnailFileName}";

            // Assicurati che la directory thumbnails esista nello storage
            $this->ensureStorageDirectoryExists('thumbnails');

            // Percorso assoluto per FFMpeg
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            $this->ffmpeg
                ->open($tempFilePath)
                ->frame(TimeCode::fromSeconds(10))
                ->save($thumbnailFullPath);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning('Errore generazione thumbnail', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Transcoding multiplo basato sulle impostazioni admin
     */
    private function transcodeVideo(string $tempFilePath, int $videoId, array $videoInfo): array
    {
        $paths = [];

        $enableTranscoding = Setting::getValue('enable_transcoding', true);

        if (!$enableTranscoding) {
            Log::info('Transcoding disabilitato, salvo solo originale');
            $paths['original'] = $this->saveOriginalFile($tempFilePath, $videoId);
            return $paths;
        }

        $qualitiesString = Setting::getValue('video_qualities', '720p,1080p,original');
        $qualities = array_map('trim', explode(',', $qualitiesString));

        Log::info('Inizio transcoding video', [
            'video_id' => $videoId,
            'qualities_richieste' => $qualities,
            'risoluzione_originale' => $videoInfo['width'] . 'x' . $videoInfo['height']
        ]);

        // Mappa delle risoluzioni
        $resolutionMap = [
            '2160p' => ['width' => 3840, 'height' => 2160, 'bitrate' => 8000],
            '1440p' => ['width' => 2560, 'height' => 1440, 'bitrate' => 6000],
            '1080p' => ['width' => 1920, 'height' => 1080, 'bitrate' => 4000],
            '720p'  => ['width' => 1280, 'height' => 720,  'bitrate' => 2500],
            '480p'  => ['width' => 854,  'height' => 480,  'bitrate' => 1500],
            '360p'  => ['width' => 640,  'height' => 360,  'bitrate' => 800],
        ];

        try {
            foreach ($qualities as $quality) {
                $quality = trim($quality);

                // Gestisci il caso "original"
                if ($quality === 'original') {
                    $paths['original'] = $this->saveOriginalFile($tempFilePath, $videoId);
                    Log::info("Salvato video originale");
                    continue;
                }

                if (!isset($resolutionMap[$quality])) {
                    Log::warning("Qualità non supportata: {$quality}");
                    continue;
                }

                $resolution = $resolutionMap[$quality];

                if ($videoInfo['height'] >= $resolution['height']) {
                    $paths[$quality] = $this->transcodeToQuality(
                        $tempFilePath,
                        $videoId,
                        $quality,
                        $resolution['width'],
                        $resolution['height'],
                        $resolution['bitrate']
                    );
                    Log::info("Transcodificato video in {$quality}");
                } else {
                    Log::info("Saltata qualità {$quality} (risoluzione originale troppo bassa)");
                }
            }

            if (empty($paths)) {
                $paths['original'] = $this->saveOriginalFile($tempFilePath, $videoId);
            }

            return $paths;
        } catch (\Exception $e) {
            Log::error('Errore durante transcoding', [
                'error' => $e->getMessage()
            ]);

            // In caso di errore, salva almeno l'originale
            return [
                'original' => $this->saveOriginalFile($tempFilePath, $videoId)
            ];
        }
    }

    /**
     * Transcode a qualità specifica
     */
    private function transcodeToQuality(string $tempFilePath, int $videoId, string $quality, int $width, int $height, int $bitrate = 2500): string
    {
        $videoFileName = "video_{$videoId}_{$quality}.mp4";
        $videoPath = "videos/{$videoFileName}";

        // Assicurati che la directory videos esista nello storage
        $this->ensureStorageDirectoryExists('videos');

        // Percorso assoluto per FFMpeg
        $videoFullPath = Storage::disk('public')->path($videoPath);

        $format = (new X264())
            ->setKiloBitrate($bitrate)
            ->setAudioCodec('aac')
            ->setVideoCodec('libx264');

        $this->ffmpeg
            ->open($tempFilePath)
            ->save($format, $videoFullPath);

        return $videoPath;
    }

    /**
     * Salva file originale
     */
    private function saveOriginalFile(string $tempFilePath, int $videoId): string
    {
        $originalFileName = "video_{$videoId}_original.mp4";
        $originalPath = "videos/{$originalFileName}";

        // Assicurati che la directory videos esista nello storage
        $this->ensureStorageDirectoryExists('videos');

        // Percorso assoluto per il salvataggio
        $originalFullPath = Storage::disk('public')->path($originalPath);

        copy($tempFilePath, $originalFullPath);

        return $originalPath;
    }

    /**
     * Delete video
     */
    public function deleteVideo(Video $video): bool
    {
        try {
            // Elimina video file
            if ($video->video_path) {
                if (Storage::disk('public')->exists($video->video_path)) {
                    Storage::disk('public')->delete($video->video_path);
                }
            }

            // Elimina thumbnail
            if ($video->thumbnail_path) {
                if (Storage::disk('public')->exists($video->thumbnail_path)) {
                    Storage::disk('public')->delete($video->thumbnail_path);
                }
            }

            // Elimina file originale se esiste
            if ($video->original_file_path) {
                if (Storage::disk('public')->exists($video->original_file_path)) {
                    Storage::disk('public')->delete($video->original_file_path);
                }
            }

            return $video->delete();
        } catch (\Exception $e) {
            Log::error('Errore eliminazione video', [
                'video_id' => $video->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    /**
     * Riprocessa un video rifiutato
     */
    public function reprocessRejectedVideo(int $videoId): bool
    {
        $video = Video::findOrFail($videoId);
        
        if ($video->status !== 'rejected') {
            throw new \Exception('Solo i video rifiutati possono essere riprocessati');
        }
        
        if (!$video->original_file_path) {
            throw new \Exception('File originale non trovato per questo video');
        }
        
        // Verifica che il file originale esista
        $absoluteOriginalPath = $this->resolveInputPath($video->original_file_path);
        if (!file_exists($absoluteOriginalPath)) {
            throw new \Exception('File originale non trovato sul filesystem: ' . $absoluteOriginalPath);
        }
        
        try {
            // Resetta lo stato del video
            $video->update([
                'status' => 'processing',
                'moderation_reason' => null,
            ]);
            
            // Crea un percorso temporaneo relativo per il Job
            $tempFileName = 'reprocess_' . time() . '_' . Str::uuid() . '.mp4';
            $tempRelativePath = $this->tempPath . '/' . $tempFileName;
            
            // Copia il file originale nel percorso temporaneo
            $tempAbsolutePath = $this->resolveInputPath($tempRelativePath);
            $this->ensureDirectoryExists(dirname($tempAbsolutePath));
            copy($absoluteOriginalPath, $tempAbsolutePath);
            
            // Avvia un nuovo job di processazione con il percorso temporaneo
            dispatch(new ProcessVideoJob($video->id, $tempRelativePath));
            
            Log::info('Video riprocessamento avviato', [
                'video_id' => $videoId,
                'original_file_path' => $video->original_file_path,
                'temp_relative_path' => $tempRelativePath
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Errore durante riprocessamento video', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            
            // Ripristina lo stato rejected in caso di errore
            $video->update([
                'status' => 'rejected',
                'moderation_reason' => 'Errore durante riprocessamento: ' . $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Salva le qualità video nel database
     */
    private function saveVideoQualitiesToDatabase(Video $video, array $processedPaths, array $videoInfo): void
    {
        try {
            // Rimuovi le qualità esistenti se presenti
            $video->videoQualities()->delete();

            $qualitiesData = [];
            $resolutionMap = [
                '2160p' => ['width' => 3840, 'height' => 2160],
                '1440p' => ['width' => 2560, 'height' => 1440],
                '1080p' => ['width' => 1920, 'height' => 1080],
                '720p'  => ['width' => 1280, 'height' => 720],
                '480p'  => ['width' => 854, 'height' => 480],
                '360p'  => ['width' => 640, 'height' => 360],
            ];

            foreach ($processedPaths as $quality => $filePath) {
                $qualityData = [
                    'file_path' => $filePath,
                    'file_url' => asset($filePath),
                    'width' => $resolutionMap[$quality]['width'] ?? null,
                    'height' => $resolutionMap[$quality]['height'] ?? null,
                    'bitrate' => $this->getBitrateForQuality($quality),
                    'is_available' => true,
                ];

                // Se è il file originale, usa le dimensioni reali del video
                if ($quality === 'original') {
                    $qualityData['width'] = $videoInfo['width'];
                    $qualityData['height'] = $videoInfo['height'];
                }

                $qualitiesData[$quality] = $qualityData;
            }

            // Salva tutte le qualità nel database
            foreach ($qualitiesData as $quality => $data) {
                \App\Models\VideoQuality::createForVideo($video, $quality, $data);
            }

            Log::info('Qualità video salvate nel database', [
                'video_id' => $video->id,
                'qualities_saved' => array_keys($qualitiesData)
            ]);
        } catch (\Exception $e) {
            Log::error('Errore nel salvataggio delle qualità video', [
                'video_id' => $video->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ottiene il bitrate per una qualità specifica
     */
    private function getBitrateForQuality(string $quality): int
    {
        $bitrateMap = [
            'original' => 0, // Usare il bitrate originale
            '2160p' => 8000,
            '1440p' => 6000,
            '1080p' => 4000,
            '720p' => 2500,
            '480p' => 1500,
            '360p' => 800,
        ];

        return $bitrateMap[$quality] ?? 2500;
    }

    /**
     * URL streaming
     */
    public function getStreamingUrl(Video $video): ?string
    {
        if (!$video->video_path) {
            return null;
        }

        return asset($video->video_path);
    }
}
