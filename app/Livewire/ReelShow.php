<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\Like;
use App\Models\WatchLater;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ReelShow extends Component
{
    public Video $currentVideo;
    public array $reels = [];
    public int $currentIndex = 0;
    public ?int $userId = null;
    
    // Video player state
    public bool $isPlaying = false;
    public bool $isMuted = false;
    public bool $isFullscreen = false;
    public float $currentTime = 0;
    public float $duration = 0;
    public float $volume = 1.0;
    
    // UI state
    public bool $showComments = false;
    public ?int $currentCommentVideoId = null;
    public bool $showContextMenu = false;
    public array $contextMenuPosition = ['x' => 0, 'y' => 0];
    public string $contextMenuVideo = '';
    
    protected $listeners = [
        'video-ended' => 'nextVideo',
        'video-time-update' => 'updateTime',
        'reel-navigate' => 'navigateToReel',
        'toggle-fullscreen' => 'toggleFullscreen',
        'reel-toggle-watch-later' => 'toggleWatchLaterForReel',
        'showContextMenu' => 'showContextMenu',
        'hideContextMenu' => 'hideContextMenu',
        'reelChanged' => 'handleReelChanged',
        'videoLiked' => 'handleVideoLikeDislikeUpdate',
        'videoDisliked' => 'handleVideoLikeDislikeUpdate',
        'videoUpdated' => 'handleVideoLikeDislikeUpdate',
        'changeVideoQuality' => 'changeVideoQuality',
    ];

    public function mount(Video $video)
    {
        $this->currentVideo = $video;
        $this->userId = Auth::id();
        
        $this->loadReels();
        $this->findCurrentIndex();
        
        // Incrementa le visualizzazioni
        $this->currentVideo->incrementViews();
    }

    /**
     * Carica tutti i reels disponibili in ordine casuale
     */
    public function loadReels(): void
    {
        try {
            $this->reels = Video::published()
                ->where('is_reel', true)
                ->where('id', '!=', $this->currentVideo->id)
                ->with(['user.userProfile'])
                ->inRandomOrder()
                ->limit(20)
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => $video->description,
                        'thumbnail_path' => $video->thumbnail_path,
                        'video_path' => $video->video_path,
                        'video_url' => $video->video_url,
                        'duration' => $video->duration,
                        'views_count' => $video->views_count,
                        'likes_count' => $video->likes()->where('reaction', 'like')->count(),
                        'dislikes_count' => $video->likes()->where('reaction', 'dislike')->count(),
                        'comments_count' => $video->comments()->count(),
                        'tags' => $video->tags ?? [],
                        'user' => [
                            'id' => $video->user->id,
                            'name' => $video->user->name,
                            'channel_name' => $video->user->userProfile?->channel_name,
                            'avatar_url' => $video->user->userProfile?->avatar_url,
                            'subscribers' => $video->user->subscribers()->count(),
                        ],
                        'created_at' => $video->created_at->toISOString(),
                        'user_reaction' => $this->getUserReaction($video->id),
                        'is_in_watch_later' => $this->isInWatchLater($video->id),
                    ];
                })->toArray();
                
            Log::info('Reels loaded successfully', [
                'total_reels' => count($this->reels),
                'current_video_id' => $this->currentVideo->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reels', [
                'error' => $e->getMessage(),
                'current_video_id' => $this->currentVideo->id
            ]);
            $this->reels = [];
        }
    }

    /**
     * Trova l'indice del video corrente
     */
    public function findCurrentIndex(): void
    {
        $currentVideoInReels = array_search($this->currentVideo->id, array_column($this->reels, 'id'));
        
        if ($currentVideoInReels !== false) {
            $this->currentIndex = $currentVideoInReels;
        } else {
            // Se il video corrente non è nei reels, aggiungilo all'inizio
            array_unshift($this->reels, [
                'id' => $this->currentVideo->id,
                'title' => $this->currentVideo->title,
                'description' => $this->currentVideo->description,
                'thumbnail_path' => $this->currentVideo->thumbnail_path,
                'video_path' => $this->currentVideo->video_path,
                'video_url' => $this->currentVideo->video_url,
                'duration' => $this->currentVideo->duration,
                'views_count' => $this->currentVideo->views_count,
                'likes_count' => $this->currentVideo->likes()->where('reaction', 'like')->count(),
                'dislikes_count' => $this->currentVideo->likes()->where('reaction', 'dislike')->count(),
                'comments_count' => $this->currentVideo->comments()->count(),
                'tags' => $this->currentVideo->tags ?? [],
                'user' => [
                    'id' => $this->currentVideo->user->id,
                    'name' => $this->currentVideo->user->name,
                    'channel_name' => $this->currentVideo->user->userProfile?->channel_name,
                    'avatar_url' => $this->currentVideo->user->userProfile?->avatar_url,
                    'subscribers' => $this->currentVideo->user->subscribers()->count(),
                ],
                'created_at' => $this->currentVideo->created_at->toISOString(),
                'user_reaction' => $this->getUserReaction($this->currentVideo->id),
                'is_in_watch_later' => $this->isInWatchLater($this->currentVideo->id),
            ]);
            $this->currentIndex = 0;
        }
    }

    /**
     * Naviga al reel successivo con precaricamento
     */
    public function nextVideo(): void
    {
        if ($this->currentIndex < count($this->reels) - 1) {
            $this->currentIndex++;
            $this->loadCurrentVideoSmooth();
        }
    }

    /**
     * Naviga al reel precedente con precaricamento
     */
    public function previousVideo(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentVideoSmooth();
        }
    }

    /**
     * Naviga a un reel specifico con transizione fluida
     */
    public function navigateToReel(int $videoId): void
    {
        $index = array_search($videoId, array_column($this->reels, 'id'));
        if ($index !== false) {
            $this->currentIndex = $index;
            $this->loadCurrentVideoSmooth();
        }
    }

    /**
     * Carica il video corrente con ottimizzazioni per transizioni fluide
     */
    public function loadCurrentVideo(): void
    {
        if (isset($this->reels[$this->currentIndex])) {
            $videoData = $this->reels[$this->currentIndex];
            $this->currentVideo = Video::find($videoData['id']);
            
            // Aggiorna i dati del video corrente
            $this->currentVideo->incrementViews();
            
            // Reset player state
            $this->isPlaying = false;
            $this->currentTime = 0;
            $this->duration = $this->currentVideo->duration ?? 0;
            
            // Aggiorna i dati nei reels con query dirette
            $this->reels[$this->currentIndex]['views_count'] = $this->currentVideo->fresh()->views_count;
            $this->reels[$this->currentIndex]['likes_count'] = $this->currentVideo->likes()->where('reaction', 'like')->count();
            $this->reels[$this->currentIndex]['dislikes_count'] = $this->currentVideo->likes()->where('reaction', 'dislike')->count();
            
            $this->dispatch('video-changed', videoId: $this->currentVideo->id);
        }
    }

    /**
     * Carica il video corrente con transizione fluida e precaricamento
     */
    public function loadCurrentVideoSmooth(): void
    {
        if (isset($this->reels[$this->currentIndex])) {
            $videoData = $this->reels[$this->currentIndex];
            
            // Precarica il nuovo video
            $this->dispatch('video-transition-start');
            
            // Aggiorna il video corrente in modo asincrono
            $this->currentVideo = Video::with(['user.userProfile'])->find($videoData['id']);
            
            // Incrementa le visualizzazioni in background
            $this->currentVideo->incrementViews();
            
            // Reset state del player
            $this->isPlaying = false;
            $this->currentTime = 0;
            $this->duration = $this->currentVideo->duration ?? 0;
            
            // Aggiorna i dati locali senza query aggiuntive quando possibile
            if (isset($this->reels[$this->currentIndex])) {
                $this->reels[$this->currentIndex]['views_count'] = $this->currentVideo->views_count;
                $this->reels[$this->currentIndex]['likes_count'] = $this->currentVideo->likes()->where('reaction', 'like')->count();
                $this->reels[$this->currentIndex]['dislikes_count'] = $this->currentVideo->likes()->where('reaction', 'dislike')->count();
            }
            
            // Dispatch eventi per JavaScript
            $this->dispatch('video-changed-smooth', [
                'videoId' => $this->currentVideo->id,
                'index' => $this->currentIndex,
                'total' => count($this->reels)
            ]);

            // Se i commenti sono aperti per il video precedente, chiudili
            if ($this->showComments && $this->currentCommentVideoId !== $this->currentVideo->id) {
                $this->closeComments();
            }
            
            // Precarica i video adiacenti per transizioni ancora più fluide
            $this->preloadAdjacentVideos();
        }
    }

    /**
     * Precarica i video adiacenti per transizioni fluide
     */
    private function preloadAdjacentVideos(): void
    {
        $videosToPreload = [];
        
        // Precarica il video successivo
        if ($this->currentIndex < count($this->reels) - 1 && isset($this->reels[$this->currentIndex + 1])) {
            $videosToPreload[] = $this->reels[$this->currentIndex + 1]['id'];
        }
        
        // Precarica il video precedente
        if ($this->currentIndex > 0 && isset($this->reels[$this->currentIndex - 1])) {
            $videosToPreload[] = $this->reels[$this->currentIndex - 1]['id'];
        }
        
        if (!empty($videosToPreload)) {
            $this->dispatch('preload-videos', ['videoIds' => $videosToPreload]);
        }
    }

    /**
     * Toggle play/pause
     */
    public function togglePlay(): void
    {
        $this->isPlaying = !$this->isPlaying;
        $this->dispatch('player-state-changed', isPlaying: $this->isPlaying);
    }

    /**
     * Toggle mute
     */
    public function toggleMute(): void
    {
        $this->isMuted = !$this->isMuted;
        $this->dispatch('player-mute-changed', isMuted: $this->isMuted);
    }

    /**
     * Toggle fullscreen
     */
    public function toggleFullscreen(): void
    {
        $this->isFullscreen = !$this->isFullscreen;
        $this->dispatch('fullscreen-toggled', isFullscreen: $this->isFullscreen);
    }

    /**
     * Aggiorna il tempo del video
     */
    public function updateTime(float $time): void
    {
        $this->currentTime = $time;
    }


    /**
     * Toggle watch later for the current video
     */
    public function toggleWatchLater(): void
    {
        if (!$this->userId) {
            return;
        }

        try {
            WatchLater::toggleWatchLater($this->userId, $this->currentVideo->id);
            
            // Aggiorna lo stato localmente
            $this->reels[$this->currentIndex]['is_in_watch_later'] =
                WatchLater::isInWatchLater($this->userId, $this->currentVideo->id);
            
            $this->dispatch('watch-later-updated', [
                'isInWatchLater' => $this->reels[$this->currentIndex]['is_in_watch_later']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error toggling watch later', [
                'error' => $e->getMessage(),
                'video_id' => $this->currentVideo->id,
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * Toggle watch later for a specific reel (used from JavaScript)
     */
    public function toggleWatchLaterForReel(int $videoId): void
    {
        if (!$this->userId) {
            return;
        }

        try {
            WatchLater::toggleWatchLater($this->userId, $videoId);
            
            // Trova l'indice del video nell'array reels
            $index = array_search($videoId, array_column($this->reels, 'id'));
            if ($index !== false) {
                // Aggiorna lo stato localmente
                $this->reels[$index]['is_in_watch_later'] =
                    WatchLater::isInWatchLater($this->userId, $videoId);
                
                $this->dispatch('reel-watch-later-updated', [
                    'videoId' => $videoId,
                    'isInWatchLater' => $this->reels[$index]['is_in_watch_later']
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error toggling watch later for reel', [
                'error' => $e->getMessage(),
                'video_id' => $videoId,
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * Mostra/nasconde i commenti per un video specifico
     */
    public function toggleComments(?int $videoId = null): void
    {
        // Se viene passato un video ID diverso da quello corrente, cambia video
        if ($videoId && $videoId !== $this->currentCommentVideoId) {
            $this->currentCommentVideoId = $videoId;
            $this->showComments = true;
        } elseif ($videoId === $this->currentCommentVideoId) {
            // Toggle per lo stesso video
            $this->showComments = !$this->showComments;
            if (!$this->showComments) {
                $this->currentCommentVideoId = null;
            }
        } else {
            // Toggle senza video specifico (usa il video corrente)
            $this->currentCommentVideoId = $this->currentVideo->id;
            $this->showComments = !$this->showComments;
        }
    }

    /**
     * Chiude i commenti
     */
    public function closeComments(): void
    {
        $this->showComments = false;
        $this->currentCommentVideoId = null;
    }

    /**
     * Mostra il menu contestuale
     */
    public function showContextMenu(int $videoId, int $x, int $y): void
    {
        Log::info('showContextMenu called', [
            'videoId' => $videoId,
            'x' => $x,
            'y' => $y,
            'showContextMenu' => $this->showContextMenu,
            'contextMenuVideo' => $this->contextMenuVideo
        ]);
        
        $this->contextMenuVideo = (string) $videoId;
        $this->contextMenuPosition = ['x' => $x, 'y' => $y];
        $this->showContextMenu = true;
        
        Log::info('Context menu state updated', [
            'showContextMenu' => $this->showContextMenu,
            'contextMenuVideo' => $this->contextMenuVideo,
            'contextMenuPosition' => $this->contextMenuPosition
        ]);
    }

    /**
     * Ottiene il video per il menu contestuale
     */
    public function getContextMenuVideo(): ?Video
    {
        if (!$this->contextMenuVideo) {
            return null;
        }
        
        try {
            return Video::find((int) $this->contextMenuVideo);
        } catch (\Exception $e) {
            Log::error('Error getting context menu video', [
                'error' => $e->getMessage(),
                'contextMenuVideo' => $this->contextMenuVideo
            ]);
            return null;
        }
    }

    /**
     * Ottiene le qualità disponibili per il video del menu contestuale
     */
    public function getContextMenuVideoQualities(): array
    {
        $video = $this->getContextMenuVideo();
        return $video ? $video->getAvailableQualities() : [];
    }

    /**
     * Nasconde il menu contestuale
     */
    public function hideContextMenu(): void
    {
        Log::info('hideContextMenu called');
        $this->showContextMenu = false;
        $this->contextMenuVideo = '';
    }

    /**
     * Cambia la qualità del video dal menu contestuale o dall'interfaccia principale
     */
    public function changeVideoQuality(string $quality): void
    {
        try {
            $videoId = $this->contextMenuVideo ? (int) $this->contextMenuVideo : $this->currentVideo->id;
            $video = Video::find($videoId);
            
            if (!$video) {
                Log::error('Video not found for quality change', [
                    'video_id' => $videoId
                ]);
                return;
            }

            $availableQualities = $video->getAvailableQualities();
            
            // Se è "Auto", usa la qualità raccomandata
            $actualQuality = $quality === 'auto' ? ($video->getPreferredQuality()['quality'] ?? '720p') : $quality;
            
            // Verifica che la qualità sia disponibile
            if (!isset($availableQualities[$actualQuality])) {
                Log::warning('Requested quality not available', [
                    'video_id' => $videoId,
                    'requested_quality' => $quality,
                    'actual_quality' => $actualQuality,
                    'available_qualities' => array_keys($availableQualities)
                ]);
                return;
            }

            // Salva la qualità preferita per l'utente (se loggato)
            if ($this->userId) {
                // Salva nel database le preferenze dell'utente
                UserPreference::updateOrCreate(
                    ['user_id' => $this->userId, 'key' => "video_quality_{$videoId}"],
                    ['value' => $quality]
                );
            }

            // Salva anche in sessione come fallback
            session(["preferred_video_quality_{$videoId}" => $quality]);

            // Dispatch evento per il JavaScript con informazioni complete
            $this->dispatch('video-quality-changed', [
                'videoId' => $videoId,
                'quality' => $quality,
                'actualQuality' => $actualQuality,
                'videoUrl' => $availableQualities[$actualQuality]['url'],
                'label' => $availableQualities[$actualQuality]['label'] ?? $actualQuality,
                'fileSize' => $availableQualities[$actualQuality]['formatted_file_size'] ?? null,
                'isReel' => true
            ]);
            
            $this->hideContextMenu();
            
            Log::info('Reel video quality changed', [
                'user_id' => $this->userId,
                'video_id' => $videoId,
                'requested_quality' => $quality,
                'actual_quality' => $actualQuality,
                'is_reel' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error changing reel video quality', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'video_id' => $this->contextMenuVideo ?? $this->currentVideo->id,
                'quality' => $quality
            ]);
        }
    }

    /**
     * Aggiunge il video ai watch later dal menu contestuale
     */
    public function addToWatchLaterFromContext(): void
    {
        if (!$this->userId || !$this->contextMenuVideo) {
            return;
        }

        try {
            $videoId = (int) $this->contextMenuVideo;
            WatchLater::toggleWatchLater($this->userId, $videoId);
            
            $this->dispatch('watch-later-updated-from-context', [
                'videoId' => $videoId,
                'isInWatchLater' => WatchLater::isInWatchLater($this->userId, $videoId)
            ]);
            
            $this->hideContextMenu();
            
            Log::info('Video added to watch later from context menu', [
                'user_id' => $this->userId,
                'video_id' => $videoId
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding to watch later from context menu', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'video_id' => $this->contextMenuVideo
            ]);
        }
    }

    /**
     * Marca il video come "non interessa"
     */
    public function notInterested(): void
    {
        if (!$this->userId || !$this->contextMenuVideo) {
            return;
        }

        try {
            $videoId = (int) $this->contextMenuVideo;
            
            // Qui potresti implementare logica per nascondere il video dall'utente
            // Per ora aggiungiamo solo un log
            Log::info('User marked video as not interested', [
                'user_id' => $this->userId,
                'video_id' => $videoId
            ]);
            
            $this->dispatch('not-interested-selected', ['videoId' => $videoId]);
            $this->hideContextMenu();
            
        } catch (\Exception $e) {
            Log::error('Error marking video as not interested', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'video_id' => $this->contextMenuVideo
            ]);
        }
    }

    /**
     * Condivide il video
     */
    public function shareVideo(): void
    {
        try {
            $videoId = $this->contextMenuVideo ? (int) $this->contextMenuVideo : $this->currentVideo->id;
            $video = Video::find($videoId);
            
            if ($video) {
                $shareUrl = route('reel.show', $video->id);
                
                $this->dispatch('share-video', [
                    'videoId' => $videoId,
                    'url' => $shareUrl,
                    'title' => $video->title
                ]);
                
                if ($this->contextMenuVideo) {
                    $this->hideContextMenu();
                }
                
                Log::info('Video share initiated', [
                    'video_id' => $videoId,
                    'share_url' => $shareUrl
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sharing video', [
                'error' => $e->getMessage(),
                'video_id' => $this->contextMenuVideo ?? $this->currentVideo->id
            ]);
        }
    }

    /**
     * Copia il link del video
     */
    public function copyLink(): void
    {
        if (!$this->contextMenuVideo) {
            return;
        }

        try {
            $videoId = (int) $this->contextMenuVideo;
            $video = Video::find($videoId);
            
            if ($video) {
                $shareUrl = route('reel.show', $video->id);
                
                $this->dispatch('copy-link', [
                    'videoId' => $videoId,
                    'url' => $shareUrl
                ]);
                
                $this->hideContextMenu();
                
                Log::info('Video link copied', [
                    'video_id' => $videoId,
                    'share_url' => $shareUrl
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error copying video link', [
                'error' => $e->getMessage(),
                'video_id' => $this->contextMenuVideo
            ]);
        }
    }

    /**
     * Segnala il video
     */
    public function reportVideo(): void
    {
        if (!$this->userId || !$this->contextMenuVideo) {
            return;
        }

        try {
            $videoId = (int) $this->contextMenuVideo;
            
            // Reindirizza alla pagina di segnalazione o apri modal
            $this->dispatch('report-video', ['videoId' => $videoId]);
            $this->hideContextMenu();
            
            Log::info('Video report initiated', [
                'user_id' => $this->userId,
                'video_id' => $videoId
            ]);
        } catch (\Exception $e) {
            Log::error('Error reporting video', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'video_id' => $this->contextMenuVideo
            ]);
        }
    }

    /**
     * Ottiene la reazione dell'utente per un video
     */
    public function getUserReaction(int $videoId): ?string
    {
        if (!$this->userId) return null;
        
        try {
            $video = Video::find($videoId);
            return $video?->getUserReaction($this->userId);
        } catch (\Exception $e) {
            Log::error('Error getting user reaction', [
                'error' => $e->getMessage(),
                'video_id' => $videoId,
                'user_id' => $this->userId
            ]);
            return null;
        }
    }

    /**
     * Verifica se un video è nei watch later
     */
    public function isInWatchLater(int $videoId): bool
    {
        if (!$this->userId) return false;
        
        try {
            return WatchLater::isInWatchLater($this->userId, $videoId);
        } catch (\Exception $e) {
            Log::error('Error checking watch later', [
                'error' => $e->getMessage(),
                'video_id' => $videoId,
                'user_id' => $this->userId
            ]);
            return false;
        }
    }

    /**
     * Formatta i numeri grandi
     */
    public function formatCount(int $count): string
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }
        return (string) $count;
    }

    /**
     * Ottiene il video corrente per i commenti
     */
    public function getCurrentCommentVideo(): ?Video
    {
        if (!$this->showComments || !$this->currentCommentVideoId) {
            return null;
        }

        return Video::find($this->currentCommentVideoId);
    }

    /**
     * Verifica se i commenti sono aperti per il video corrente
     */
    public function isCommentsOpenForCurrentVideo(): bool
    {
        return $this->showComments && $this->currentCommentVideoId === $this->currentVideo->id;
    }

    /**
     * Gestisce il cambio di reel da JavaScript
     */
    public function handleReelChanged($data = null): void
    {
        if ($data && isset($data['index']) && isset($data['videoId'])) {
            $this->currentIndex = $data['index'];

            // Aggiorna il video corrente
            if (isset($this->reels[$this->currentIndex])) {
                $videoData = $this->reels[$this->currentIndex];
                $this->currentVideo = Video::find($videoData['id']);

                // Aggiorna i dati locali per mantenere la sincronizzazione
                $this->reels[$this->currentIndex]['views_count'] = $this->currentVideo->views_count;
                $this->reels[$this->currentIndex]['likes_count'] = $this->currentVideo->likes()->where('reaction', 'like')->count();
                $this->reels[$this->currentIndex]['dislikes_count'] = $this->currentVideo->likes()->where('reaction', 'dislike')->count();
                $this->reels[$this->currentIndex]['comments_count'] = $this->currentVideo->comments()->count();
            }

            // Forza il refresh della vista per aggiornare il pulsante guarda più tardi
            $this->dispatch('$refresh');

            Log::info('Reel changed via JavaScript', [
                'index' => $this->currentIndex,
                'video_id' => $data['videoId']
            ]);
        }
    }

    /**
     * Gestisce gli aggiornamenti dei like/dislike dal componente ReelLikeDislike
     */
    public function handleVideoLikeDislikeUpdate($data = null): void
    {
        if ($data && isset($data['videoId'])) {
            $videoId = $data['videoId'];
            
            // Trova l'indice del video nell'array reels
            $index = array_search($videoId, array_column($this->reels, 'id'));
            
            if ($index !== false) {
                // Ricarica i dati dal database per garantire accuracy
                $video = Video::find($videoId);
                if ($video) {
                    $likesCount = $video->likes()->where('reaction', 'like')->count();
                    $dislikesCount = $video->likes()->where('reaction', 'dislike')->count();
                    
                    // Aggiorna i dati locali
                    $this->reels[$index]['likes_count'] = $likesCount;
                    $this->reels[$index]['dislikes_count'] = $dislikesCount;
                    
                    // Se è il video corrente, aggiorna anche $currentVideo
                    if ($this->currentVideo->id == $videoId) {
                        $this->currentVideo->likes_count = $likesCount;
                        $this->currentVideo->dislikes_count = $dislikesCount;
                    }
                    
                    Log::info('Like/dislike counts updated from database', [
                        'video_id' => $videoId,
                        'index' => $index,
                        'likes_count' => $likesCount,
                        'dislikes_count' => $dislikesCount
                    ]);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.reel-show', [
            'currentCommentVideo' => $this->getCurrentCommentVideo(),
            'isCommentsOpenForCurrentVideo' => $this->isCommentsOpenForCurrentVideo(),
        ]);
    }
}
