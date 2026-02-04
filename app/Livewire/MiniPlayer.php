<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MiniPlayer extends Component
{
    public ?Video $currentVideo = null;
    public bool $isPlaying = false;
    public float $currentTime = 0;
    public float $duration = 0;
    public float $volume = 1;
    public float $previousVolume = 1;
    public bool $muted = false;
    public string $playbackRate = '1';
    public bool $isVisible = false;
    public bool $isExpanded = false;
    public string $currentQuality = 'auto';
    public bool $autoplay = true;
    
    protected $listeners = [
        'startMiniPlayer' => 'startVideoSession',
        'pauseMiniPlayer' => 'pauseVideo',
        'resumeMiniPlayer' => 'resumeVideo',
        'stopMiniPlayer' => 'stopVideoSession',
        'updateMiniPlayerTime' => 'updateTime',
        'videoEnded' => 'handleVideoEnded'
    ];

    protected $rules = [
        'currentTime' => 'numeric|min:0',
        'volume' => 'numeric|min:0|max:1',
        'muted' => 'boolean',
        'playbackRate' => 'in:0.5,0.75,1,1.25,1.5,1.75,2',
        'autoplay' => 'boolean'
    ];

    public function mount()
    {
        $this->loadMiniPlayerState();
    }

    /**
     * Carica lo stato del miniplayer dalla sessione
     */
    public function loadMiniPlayerState()
    {
        $miniPlayerState = session()->get('mini_player_state', []);
        
        if (!empty($miniPlayerState)) {
            $videoId = $miniPlayerState['video_id'] ?? null;
            $this->currentTime = $miniPlayerState['current_time'] ?? 0;
            $this->isPlaying = $miniPlayerState['is_playing'] ?? false;
            $this->isVisible = $miniPlayerState['is_visible'] ?? false;
            $this->isExpanded = $miniPlayerState['is_expanded'] ?? false;
            $this->volume = $miniPlayerState['volume'] ?? 1;
            $this->previousVolume = $miniPlayerState['previous_volume'] ?? 1;
            $this->muted = $miniPlayerState['muted'] ?? false;
            $this->playbackRate = $miniPlayerState['playback_rate'] ?? '1';
            $this->autoplay = $miniPlayerState['autoplay'] ?? true;
            $this->currentQuality = $miniPlayerState['quality'] ?? 'auto';

            if ($videoId) {
                $this->currentVideo = Video::find($videoId);
                if ($this->currentVideo) {
                    $this->duration = $this->currentVideo->duration ?? 0;
                }
            }
        }
    }

    /**
     * Avvia una sessione video nel miniplayer
     */
    public function startVideoSession($videoData)
    {
        try {
            $videoId = $videoData['video_id'] ?? null;
            $startTime = (float) ($videoData['current_time'] ?? 0);

            if (!$videoId || !is_numeric($videoId)) {
                $this->dispatch('showToast', [
                    'message' => 'ID video non valido',
                    'type' => 'error'
                ]);
                return;
            }

            $video = Video::with('user.userProfile')->find($videoId);

            if (!$video) {
                $this->dispatch('showToast', [
                    'message' => 'Video non trovato',
                    'type' => 'error'
                ]);
                return;
            }

            if (!$video->is_public || $video->status !== 'published') {
                $this->dispatch('showToast', [
                    'message' => 'Video non disponibile',
                    'type' => 'error'
                ]);
                return;
            }

            // Salva la cronologia di visione
            if (Auth::check()) {
                WatchHistory::updateOrCreate(
                    ['user_id' => Auth::id(), 'video_id' => $video->id],
                    [
                        'watched_duration' => $startTime,
                        'total_duration' => $video->duration ?? 0,
                        'last_watched_at' => now(),
                    ]
                );
            }

            $this->currentVideo = $video;
            $this->currentTime = $startTime;
            $this->duration = $video->duration ?? 0;
            $this->isPlaying = true;
            $this->isVisible = true;
            $this->isExpanded = false;

            // Carica le preferenze utente
            $this->loadUserPreferences();

            $this->saveMiniPlayerState();

            $this->dispatch('showToast', [
                'message' => 'Mini player avviato',
                'type' => 'success'
            ]);

            $this->dispatch('miniPlayerStarted', [
                'video' => $this->formatVideoData(),
                'startTime' => $startTime
            ]);

        } catch (\Exception $e) {
            Log::error('Errore avvio mini player:', [
                'error' => $e->getMessage(),
                'video_data' => $videoData,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('showToast', [
                'message' => 'Errore durante l\'avvio del mini player',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Carica le preferenze utente per il miniplayer
     */
    protected function loadUserPreferences()
    {
        if (!Auth::check()) return;

        $user = Auth::user();
        
        // Carica preferenze volume
        $this->volume = (float) ($user->userProfile?->miniplayer_volume ?? 1);
        $this->muted = $user->userProfile?->miniplayer_muted ?? false;
        
        // Carica preferenze qualità
        $this->currentQuality = $user->userProfile?->miniplayer_quality ?? 'auto';
        
        // Carica preferenze velocità
        $this->playbackRate = $user->userProfile?->miniplayer_playback_rate ?? '1';
        
        // Carica preferenze autoplay
        $this->autoplay = $user->userProfile?->miniplayer_autoplay ?? true;
    }

    /**
     * Salva lo stato del miniplayer
     */
    public function saveMiniPlayerState()
    {
        $state = [
            'video_id' => $this->currentVideo?->id,
            'current_time' => $this->currentTime,
            'is_playing' => $this->isPlaying,
            'is_visible' => $this->isVisible,
            'is_expanded' => $this->isExpanded,
            'volume' => $this->volume,
            'previous_volume' => $this->previousVolume,
            'muted' => $this->muted,
            'playback_rate' => $this->playbackRate,
            'autoplay' => $this->autoplay,
            'quality' => $this->currentQuality,
            'last_watched_at' => now()->toISOString()
        ];

        session()->put('mini_player_state', $state);
    }

    /**
     * Formatta i dati video per il frontend
     */
    protected function formatVideoData()
    {
        if (!$this->currentVideo) return null;

        return [
            'id' => $this->currentVideo->id,
            'title' => $this->currentVideo->title ?? 'Video senza titolo',
            'thumbnail_url' => $this->currentVideo->thumbnail_url ? asset('storage/' . $this->currentVideo->thumbnail_url) : null,
            'video_url' => $this->currentVideo->video_file_url ? asset('storage/' . $this->currentVideo->video_file_url) : null,
            'duration' => $this->currentVideo->duration ?? 0,
            'formatted_duration' => $this->formatTime($this->currentVideo->duration ?? 0),
            'channel_name' => $this->currentVideo->user->userProfile?->channel_name ?: $this->currentVideo->user->name ?? 'Canale sconosciuto',
            'channel_avatar' => $this->currentVideo->user->userProfile?->avatar_url ?
                asset('storage/' . $this->currentVideo->user->userProfile->avatar_url) : null,
            'views_count' => $this->currentVideo->views_count ?? 0,
            'likes_count' => $this->currentVideo->likes_count ?? 0,
            'comments_count' => $this->currentVideo->comments_count ?? 0,
            'created_at' => $this->currentVideo->created_at?->diffForHumans() ?? 'Data sconosciuta',
            'user_id' => $this->currentVideo->user_id,
            'is_reel' => $this->currentVideo->is_reel ?? false,
            'tags' => $this->currentVideo->tags ?? []
        ];
    }

    /**
     * Aggiorna il tempo corrente
     */
    public function updateTime($time)
    {
        $this->currentTime = $time;
        $this->saveMiniPlayerState();
        
        // Aggiorna la cronologia di visione
        if (Auth::check() && $this->currentVideo) {
            WatchHistory::updateOrCreate(
                ['user_id' => Auth::id(), 'video_id' => $this->currentVideo->id],
                [
                    'watched_duration' => $time,
                    'total_duration' => $this->duration,
                    'last_watched_at' => now(),
                ]
            );
        }
    }


    /**
     * Cambia la velocità di riproduzione
     */
    public function changePlaybackRate($rate)
    {
        $this->playbackRate = $rate;
        $this->saveMiniPlayerState();
        
        // Salva preferenza utente
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->userProfile) {
                $user->userProfile->miniplayer_playback_rate = $rate;
                $user->userProfile->save();
            }
        }

        $this->dispatch('playbackRateChanged', ['rate' => $rate]);
    }

    /**
     * Cambia il volume
     */
    public function changeVolume($volume)
    {
        $this->volume = $volume;
        $this->muted = $volume == 0;
        $this->saveMiniPlayerState();
        
        // Salva preferenza utente
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->userProfile) {
                $user->userProfile->miniplayer_volume = $volume;
                $user->userProfile->miniplayer_muted = $this->muted;
                $user->userProfile->save();
            }
        }

        $this->dispatch('volumeChanged', [
            'volume' => $volume,
            'muted' => $this->muted
        ]);
    }

    /**
     * Attiva/disattiva il mute
     */
    public function toggleMute()
    {
        $this->muted = !$this->muted;
        if ($this->muted) {
            $this->previousVolume = $this->volume;
            $this->volume = 0;
        } else {
            $this->volume = $this->previousVolume > 0 ? $this->previousVolume : 0.5;
        }
        $this->saveMiniPlayerState();

        // Salva preferenza utente
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->userProfile) {
                $user->userProfile->miniplayer_muted = $this->muted;
                $user->userProfile->miniplayer_volume = $this->volume;
                $user->userProfile->save();
            }
        }

        $this->dispatch('volumeChanged', [
            'volume' => $this->volume,
            'muted' => $this->muted
        ]);
    }

    /**
     * Attiva/disattiva l'autoplay
     */
    public function toggleAutoplay()
    {
        $this->autoplay = !$this->autoplay;
        $this->saveMiniPlayerState();

        // Salva preferenza utente
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->userProfile) {
                $user->userProfile->miniplayer_autoplay = $this->autoplay;
                $user->userProfile->save();
            }
        }

        $this->dispatch('autoplayChanged', ['enabled' => $this->autoplay]);
    }

    /**
     * Cambia la qualità del video
     */
    public function changeQuality($quality)
    {
        $validQualities = ['auto', '144p', '240p', '360p', '480p', '720p', '1080p', '1440p', '2160p'];
        if (!in_array($quality, $validQualities)) {
            $quality = 'auto';
        }

        $this->currentQuality = $quality;
        $this->saveMiniPlayerState();

        // Salva preferenza utente
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->userProfile) {
                $user->userProfile->miniplayer_quality = $quality;
                $user->userProfile->save();
            }
        }

        // Per ora dispatchiamo l'evento, ma la logica di cambio qualità
        // dovrebbe essere implementata nel frontend se ci sono multiple qualità
        $this->dispatch('qualityChanged', [
            'quality' => $quality,
            'videoUrl' => $this->currentVideo ? $this->formatVideoData()['video_url'] : null
        ]);
    }

    /**
     * Salta avanti/indietro
     */
    public function skipTime($seconds)
    {
        $newTime = max(0, min($this->duration, $this->currentTime + $seconds));
        $this->currentTime = $newTime;
        $this->saveMiniPlayerState();

        $this->dispatch('timeSkipped', [
            'newTime' => $newTime,
            'seconds' => $seconds
        ]);
    }

    /**
     * Vai a un tempo specifico
     */
    public function seekTo($time)
    {
        $this->currentTime = max(0, min($this->duration, $time));
        $this->saveMiniPlayerState();

        $this->dispatch('timeSeeked', ['time' => $this->currentTime]);
    }

    /**
     * Mette in pausa il video
     */
    public function pauseVideo()
    {
        $this->isPlaying = false;
        $this->saveMiniPlayerState();
        $this->dispatch('videoPaused');
    }

    /**
     * Riprende il video
     */
    public function resumeVideo()
    {
        $this->isPlaying = true;
        $this->saveMiniPlayerState();
        $this->dispatch('videoResumed');
    }

    /**
     * Ferma il video e chiude il miniplayer
     */
    public function stopVideoSession()
    {
        $this->isPlaying = false;
        $this->isVisible = false;
        $this->isExpanded = false;
        $this->currentVideo = null;
        $this->currentTime = 0;
        $this->duration = 0;
        
        session()->forget('mini_player_state');
        
        $this->dispatch('miniPlayerStopped');
        $this->dispatch('showToast', [
            'message' => 'Mini player chiuso',
            'type' => 'info'
        ]);
    }

    /**
     * Espande/riduce il miniplayer
     */
    public function toggleExpand()
    {
        $this->isExpanded = !$this->isExpanded;
        $this->saveMiniPlayerState();
        
        $this->dispatch('miniPlayerToggled', [
            'expanded' => $this->isExpanded
        ]);
    }

    /**
     * Chiude il miniplayer
     */
    public function closeMiniPlayer()
    {
        $this->stopVideoSession();
    }

    /**
     * Gestisce la fine del video
     */
    public function handleVideoEnded()
    {
        if ($this->autoplay) {
            // Cerca video correlati
            $this->playNextVideo();
        } else {
            $this->isPlaying = false;
            $this->saveMiniPlayerState();
        }
    }

    /**
     * Riproduce il video successivo
     */
    protected function playNextVideo()
    {
        if (!$this->currentVideo) return;

        try {
            // Cerca video correlati
            $query = Video::published()
                ->where('id', '!=', $this->currentVideo->id)
                ->where('is_reel', false);

            // Priorità: stesso canale
            $nextVideo = $query->where('user_id', $this->currentVideo->user_id)
                ->inRandomOrder()
                ->first();

            // Se non trovato, cerca per tags
            if (!$nextVideo && $this->currentVideo->tags) {
                $tags = is_array($this->currentVideo->tags) ? $this->currentVideo->tags : json_decode($this->currentVideo->tags, true) ?? [];
                if (!empty($tags)) {
                    $nextVideo = $query->where(function ($q) use ($tags) {
                        foreach ($tags as $tag) {
                            $q->orWhereJsonContains('tags', $tag);
                        }
                    })->inRandomOrder()->first();
                }
            }

            // Se ancora non trovato, qualsiasi video pubblicato
            if (!$nextVideo) {
                $nextVideo = Video::published()
                    ->where('id', '!=', $this->currentVideo->id)
                    ->where('is_reel', false)
                    ->inRandomOrder()
                    ->first();
            }

            if ($nextVideo) {
                $this->startVideoSession([
                    'video_id' => $nextVideo->id,
                    'current_time' => 0
                ]);
            } else {
                $this->isPlaying = false;
                $this->saveMiniPlayerState();
                $this->dispatch('showToast', [
                    'message' => 'Nessun video disponibile per l\'autoplay',
                    'type' => 'info'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Errore playNextVideo:', [
                'error' => $e->getMessage(),
                'current_video_id' => $this->currentVideo->id
            ]);

            $this->isPlaying = false;
            $this->saveMiniPlayerState();
            $this->dispatch('showToast', [
                'message' => 'Errore durante la ricerca del video successivo',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Formatta il tempo in formato mm:ss
     */
    public function formatTime($seconds)
    {
        if (!$seconds || $seconds <= 0) return '0:00';

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Calcola la percentuale di completamento
     */
    public function getProgressPercentage()
    {
        if (!$this->duration || $this->duration <= 0) return 0;
        return min(100, ($this->currentTime / $this->duration) * 100);
    }

    /**
     * Ottiene l'icona volume appropriata
     */
    public function getVolumeIcon()
    {
        if ($this->muted || $this->volume == 0) {
            return 'fa-volume-mute';
        } elseif ($this->volume < 0.5) {
            return 'fa-volume-down';
        } else {
            return 'fa-volume-up';
        }
    }

    /**
     * Ottiene l'icona play/pause appropriata
     */
    public function getPlayIcon()
    {
        return $this->isPlaying ? 'fa-pause' : 'fa-play';
    }


    public function render()
    {
        return view('livewire.mini-player');
    }
}