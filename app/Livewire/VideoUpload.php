<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\VideoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class VideoUpload extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public ?string $tags = '';
    public string $language = 'it';
    public bool $isPublic = true;
    public bool $isReel = false;
    public ?UploadedFile $videoFile = null;
    public ?UploadedFile $thumbnail = null;

    public float $uploadProgress = 0;
    public string $uploadStatus = 'idle';
    public string $errorMessage = '';
    public ?int $uploadedVideoId = null;

    public ?string $videoUrl = null;
    public ?string $thumbnailUrl = null;

    // Proprietà pubbliche per le opzioni
    public array $supportedLanguages = [];
    public array $tagSuggestions = [];
    public array $supportedFormats = [];

    // Proprietà per le informazioni del file
    public string $estimatedFileSize = 'N/A';
    public string $estimatedProcessingTime = 'N/A';

    protected function rules()
    {
        $maxVideoUploadMb = Setting::getValue('max_video_upload_mb', 500);
        $maxVideoUploadBytes = $maxVideoUploadMb * 1024 * 1024;
        $maxThumbnailSize = 5 * 1024 * 1024;

        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string',
            'language' => 'required|string|size:2|in:it,en,es,fr,de',
            'isPublic' => 'boolean',
            'videoFile' => "required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:{$maxVideoUploadBytes}",
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:' . ($maxThumbnailSize / 1024),
        ];
    }

    protected function messages()
    {
        $maxVideoUploadMb = Setting::getValue('max_video_upload_mb', 500);

        return [
            'title.required' => 'Il titolo è obbligatorio',
            'title.min' => 'Il titolo deve essere di almeno 3 caratteri',
            'title.max' => 'Il titolo non può superare i 255 caratteri',
            'videoFile.required' => 'Devi selezionare un file video',
            'videoFile.mimes' => 'Formato video non supportato',
            'videoFile.max' => "Il file video non può superare i {$maxVideoUploadMb}MB",
            'thumbnail.image' => 'Il thumbnail deve essere un\'immagine',
            'thumbnail.max' => 'Il thumbnail non può superare i 5MB',
        ];
    }

    public function mount()
    {
        $this->supportedLanguages = [
            'it' => 'Italiano',
            'en' => 'Inglese',
            'es' => 'Spagnolo',
            'fr' => 'Francese',
            'de' => 'Tedesco',
        ];

        $this->tagSuggestions = [
            'tecnologia',
            'musica',
            'gaming',
            'sport',
            'notizie',
            'intrattenimento',
            'educativo',
            'tutorial',
            'review',
            'vlog',
            'commedia',
            'drama',
            'documentario',
            'live',
            'shorts'
        ];

        $this->supportedFormats = [
            'mp4' => 'MP4 (Consigliato)',
            'avi' => 'AVI',
            'mov' => 'MOV',
            'wmv' => 'WMV',
            'flv' => 'FLV',
            'webm' => 'WebM',
        ];
    }

    public function updatedVideoFile($value)
    {
        $this->validateOnly('videoFile');

        if ($value instanceof UploadedFile) {
            $this->uploadStatus = 'file_selected';

            $this->videoUrl = $value->temporaryUrl();

            // Calcola dimensione file
            $this->calculateFileSize($value);

            // Calcola tempo processamento
            $this->calculateProcessingTime($value);

            // Rileva automaticamente se il video è un reel basato sulla risoluzione
            $this->detectVideoType($value);

            if (empty($this->title)) {
                $this->title = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }
    }

    public function updatedThumbnail($value)
    {
        $this->validateOnly('thumbnail');

        if ($value instanceof UploadedFile) {
            $this->thumbnailUrl = $value->temporaryUrl();
        }
    }

    /**
     * Calcola dimensione stimata del file
     */
    private function calculateFileSize(UploadedFile $file): void
    {
        $sizeInBytes = $file->getSize();
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $sizeInBytes > 1024 && $i < count($units) - 1; $i++) {
            $sizeInBytes /= 1024;
        }

        $this->estimatedFileSize = round($sizeInBytes, 2) . ' ' . $units[$i];
    }

    /**
     * Calcola durata stimata del processamento
     */
    private function calculateProcessingTime(UploadedFile $file): void
    {
        $sizeInMB = $file->getSize() / (1024 * 1024);

        // Stima: 1 minuto di processamento per ogni 50MB di video
        $estimatedMinutes = ceil($sizeInMB / 50);

        if ($estimatedMinutes < 60) {
            $this->estimatedProcessingTime = $estimatedMinutes . ' minuti';
        } else {
            $hours = floor($estimatedMinutes / 60);
            $minutes = $estimatedMinutes % 60;
            $this->estimatedProcessingTime = $hours . 'h ' . $minutes . 'm';
        }
    }

    /**
     * Rileva automaticamente il tipo di video (reel o normale) basato sulla risoluzione
     */
    private function detectVideoType(UploadedFile $file): void
    {
        try {
            // Crea un file temporaneo per l'analisi
            $tempPath = tempnam(sys_get_temp_dir(), 'video_detect_');
            copy($file->getPathname(), $tempPath);

            // Usa ffprobe per ottenere le informazioni video
            $ffprobePath = Setting::getValue('ffprobe_path');
            if (!$ffprobePath || !file_exists($ffprobePath)) {
                Log::warning('FFProbe non trovato, impossibile rilevare automaticamente il tipo di video');
                return;
            }

            // Esegui ffprobe per ottenere width e height
            $command = escapeshellarg($ffprobePath) . ' -v quiet -print_format json -show_streams ' . escapeshellarg($tempPath);
            $output = shell_exec($command);

            if ($output) {
                $data = json_decode($output, true);
                
                // Trova il stream video
                $videoStream = null;
                foreach ($data['streams'] ?? [] as $stream) {
                    if ($stream['codec_type'] === 'video') {
                        $videoStream = $stream;
                        break;
                    }
                }

                if ($videoStream && isset($videoStream['width'], $videoStream['height'])) {
                    $width = (int) $videoStream['width'];
                    $height = (int) $videoStream['height'];
                    
                    // Determina se è un reel: se l'altezza è maggiore della larghezza
                    // o se il rapporto di aspetto è <= 0.8 (formato verticale)
                    $this->isReel = $this->isReelByResolution($width, $height);
                    
                    Log::info('Tipo video rilevato automaticamente', [
                        'width' => $width,
                        'height' => $height,
                        'is_reel' => $this->isReel,
                        'aspect_ratio' => round($width / $height, 3)
                    ]);
                }
            }

            // Pulisci il file temporaneo
            @unlink($tempPath);
        } catch (\Exception $e) {
            Log::warning('Errore nel rilevamento automatico del tipo video: ' . $e->getMessage());
            // In caso di errore, mantieni il valore di default (false)
        }
    }

    /**
     * Determina se un video è un reel basato sulla risoluzione
     */
    private function isReelByResolution(int $width, int $height): bool
    {
        // Se l'altezza è maggiore della larghezza, è un video verticale (reel)
        if ($height > $width) {
            return true;
        }

        // Controlla anche il rapporto di aspetto per video più quadrati ma ancora verticali
        $aspectRatio = $width / $height;
        
        // Se il rapporto è <= 0.8, è probabilmente un reel (più alto che largo)
        return $aspectRatio <= 0.8;
    }

    public function upload()
    {
        $this->validate();

        try {
            $this->uploadStatus = 'uploading';
            $this->uploadProgress = 0;

            // Simula progresso iniziale
            $this->emit('uploadProgress', 10);

            $uploadData = [
                'title' => $this->title,
                'description' => $this->description,
                'is_public' => $this->isPublic,
                'language' => $this->language,
                'tags' => $this->tags,
                'is_reel' => $this->isReel, // L'utente può specificare manualmente, ma sarà sovrascritto automaticamente se necessario
            ];

            // Upload del video tramite servizio
            $videoService = app(VideoService::class);

            // Simula progresso upload
            $this->emit('uploadProgress', 30);

            $video = $videoService->uploadVideo(
                $this->videoFile,
                $uploadData,
                Auth::id()
            );

            // Simula progresso processamento
            $this->emit('uploadProgress', 60);

            // Upload thumbnail separato se presente
            if ($this->thumbnail instanceof UploadedFile) {
                // Assicurati che la cartella thumbnails esista
                if (!file_exists(public_path('thumbnails'))) {
                    mkdir(public_path('thumbnails'), 0755, true);
                }
                $thumbnailPath = $this->thumbnail->store('thumbnails', 'public');
                $video->update(['thumbnail_path' => $thumbnailPath]);
            }

            // Simula completamento
            $this->emit('uploadProgress', 100);

            $this->uploadedVideoId = $video->id;
            $this->uploadStatus = 'completed';
            $this->uploadProgress = 100;

            // Reset form
            $this->resetForm();

            // Determina l'URL di redirect in base al tipo di video
            $redirectRoute = $this->isReel ? 'reels.show' : 'videos.show';
            $redirectMessage = $this->isReel ? 'Reel caricato con successo! Verrà pubblicato quando il processamento sarà completato.' : 'Video caricato con successo! Verrà pubblicato quando il processamento sarà completato.';

            session()->flash('success', $redirectMessage);

            return redirect()->route($redirectRoute, $video);
        } catch (\Exception $e) {
            $this->uploadStatus = 'error';
            $this->errorMessage = 'Errore durante l\'upload: ' . $e->getMessage();
            $this->uploadProgress = 0;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'description',
            'tags',
            'language',
            'isPublic',
            'isReel',
            'videoFile',
            'thumbnail',
            'uploadProgress',
            'uploadStatus',
            'errorMessage',
            'uploadedVideoId',
            'videoUrl',
            'thumbnailUrl',
            'estimatedFileSize',
            'estimatedProcessingTime'
        ]);
        $this->language = 'it';
        $this->isPublic = true;
        $this->isReel = false; // Reset del tipo video
    }

    protected function emit($event, ...$params)
    {
        // Simulazione evento per il progresso
        if ($event === 'uploadProgress') {
            $this->uploadProgress = $params[0];
        }
    }

    public function render()
    {
        return view('livewire.video-upload');
    }
}
