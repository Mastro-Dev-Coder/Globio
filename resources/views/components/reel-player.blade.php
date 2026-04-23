@props(['video' => null, 'index' => 0, 'isCurrent' => false])

@php
    use App\Models\Video;
    
    $reel = null;
    if ($video instanceof Video) {
        $reelData = [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'thumbnail_path' => $video->thumbnail_path,
            'video_path' => $video->video_path,
            'video_url' => $video->video_url,
            'duration' => $video->duration,
            'views_count' => $video->views_count,
            'likes_count' => $video->likes_count,
            'comments_count' => $video->comments_count,
            'tags' => $video->tags ?? [],
            'created_at' => $video->created_at->toISOString(),
        ];
    } elseif (is_array($video)) {
        $reelData = $video;
    } else {
        $reelData = [];
    }
    
    $videoId = $reelData['id'] ?? 0;
    $videoUrl = $reelData['video_path'] ?? '';
    $posterUrl = $reelData['thumbnail_path'] ?? '';
    $title = $reelData['title'] ?? '';
    
    // Get available qualities for this video
    $qualities = [];
    if ($videoId && $video instanceof Video) {
        $qualities = $video->getAvailableQualities();
    }
    
    // Convert to array format for JS
    $qualitiesArray = [];
    foreach ($qualities as $quality => $qualityData) {
        $qualitiesArray[] = [
            'quality' => $quality,
            'url' => $qualityData['url'] ?? '',
            'html' => $qualityData['label'] ?? $quality,
            'default' => ($qualityData['is_default'] ?? false) || $quality === 'original'
        ];
    }
    
    $qualitiesJson = json_encode($qualitiesArray, JSON_THROW_ON_ERROR);
    
    // Determine if this is the current video being viewed
    $isCurrentVideo = $isCurrent;
    
    // Autoplay settings
    $autoplay = $isCurrentVideo;
    $muted = false;
    $loop = true;
@endphp

<!-- Reel Player Container with ArtPlayer -->
<div class="relative w-full aspect-[9/16] bg-black rounded-2xl overflow-hidden shadow-2xl cursor-pointer group mx-auto"
    id="reelPlayerContainer{{ $indexel-index="{{ $index }}"
    data }}"
    data-re-video-id="{{ $videoId }}"
    onclick="toggleReelPlayPause({{ $index }})">
    
    <!-- ArtPlayer Container -->
    <div id="reelPlayer{{ $index }}" 
        class="w-full h-full"
        data-video-url="{{ asset('storage/' . $videoUrl) }}"
        data-poster="{{ $posterUrl ? asset('storage/' . $posterUrl) : '' }}"
        data-video-id="{{ $videoId }}"
        data-video-title="{{ $title }}"
        data-qualities="{{ $qualitiesJson }}"
        data-autoplay="{{ $autoplay ? 'true' : 'false' }}"
        data-muted="{{ $muted ? 'true' : 'false' }}"
        data-loop="{{ $loop ? 'true' : 'false' }}">
    </div>
    
    <!-- Custom Overlay for Reel Interactions (outside player) -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Play/Pause Overlay (visible when paused) -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-auto" 
            id="reelPlayOverlay{{ $index }}"
            style="opacity: {{ $autoplay ? '0' : '1' }}; transition: opacity 0.3s;"
            onclick="event.stopPropagation(); toggleReelPlayPause({{ $index }})">
            <div class="w-20 h-20 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-black/60 transition-all duration-200 hover:scale-110">
                <i class="fas fa-play text-white text-2xl ml-1" id="reelPlayIcon{{ $index }}"></i>
            </div>
        </div>
        
        <!-- Loading Indicator -->
        <div id="reelLoading{{ $index }}" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 z-40">
            <div class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
        </div>
        
        <!-- Error Message -->
        <div id="reelError{{ $index }}" class="absolute bottom-20 left-4 right-4 bg-red-600/90 text-white text-sm px-4 py-2 rounded-lg opacity-0 pointer-events-none transition-opacity duration-300 z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="reelErrorText{{ $index }}">Errore nel caricamento del video</span>
        </div>
    </div>
    
    <!-- Right Side Actions (Like, Comment, Share) -->
    <div class="absolute right-2 bottom-20 flex flex-col items-center gap-4 pointer-events-auto z-30">
        <!-- Like Button -->
        <button wire:click="likeVideo({{ $videoId }})" 
            class="flex flex-col items-center gap-1"
            onclick="event.stopPropagation()">
            <div class="w-12 h-12 bg-black/40 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10">
                <i class="fas fa-heart text-white text-lg" id="reelLikeIcon{{ $index }}"></i>
            </div>
            <span class="text-white text-xs font-medium" id="reelLikeCount{{ $index }}">
                {{ number_format($reelData['likes_count'] ?? 0) }}
            </span>
        </button>
        
        <!-- Comment Button -->
        <button wire:click="toggleComments({{ $videoId }})" 
            class="flex flex-col items-center gap-1"
            onclick="event.stopPropagation()">
            <div class="w-12 h-12 bg-black/40 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10">
                <i class="fas fa-comment text-white text-lg"></i>
            </div>
            <span class="text-white text-xs font-medium">
                {{ number_format($reelData['comments_count'] ?? 0) }}
            </span>
        </button>
        
        <!-- Share Button -->
        <button wire:click="shareVideo" 
            class="flex flex-col items-center gap-1"
            onclick="event.stopPropagation()">
            <div class="w-12 h-12 bg-black/40 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10">
                <i class="fas fa-share text-white text-lg"></i>
            </div>
            <span class="text-white text-xs font-medium">Condividi</span>
        </button>
        
        <!-- Save/Watch Later Button -->
        <button wire:click="toggleWatchLater" 
            class="flex flex-col items-center gap-1"
            onclick="event.stopPropagation()">
            <div class="w-12 h-12 bg-black/40 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10">
                <i class="fas fa-bookmark text-white text-lg" id="reelSaveIcon{{ $index }}"></i>
            </div>
            <span class="text-white text-xs font-medium">Salva</span>
        </button>
    </div>
    
    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-auto z-20">
        <!-- Channel Info -->
        <div class="flex items-center gap-3 mb-2">
            @if (isset($reelData['user']['avatar_url']) && $reelData['user']['avatar_url'])
                <img src="{{ asset('storage/' . $reelData['user']['avatar_url']) }}" 
                    alt="{{ $reelData['user']['channel_name'] ?? $reelData['user']['name'] }}"
                    class="w-8 h-8 rounded-full object-cover border-2 border-white/50">
            @else
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center border-2 border-white/50">
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(substr($reelData['user']['name'] ?? 'U', 0, 1)) }}
                    </span>
                </div>
            @endif
            <span class="text-white font-medium text-sm">
                {{ $reelData['user']['channel_name'] ?? $reelData['user']['name'] ?? 'Utente' }}
            </span>
            <button class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-full text-white text-xs font-medium transition-colors">
                Segui
            </button>
        </div>
        
        <!-- Title -->
        <h3 class="text-white text-sm font-medium mb-1 line-clamp-2">
            {{ $title }}
        </h3>
        
        <!-- Audio Info -->
        <div class="flex items-center gap-2 text-white/70 text-xs">
            <i class="fas fa-music"></i>
            <span class="truncate">Audio originale - {{ $reelData['user']['name'] ?? 'Creator' }}</span>
        </div>
    </div>
</div>

<script type="module">
    import { 
        initReelPlayer, 
        updateReelPlayer, 
        destroyReelPlayer 
    } from '{{ Vite::asset('resources/js/artplayer-reel-init.js') }}';

    // Initialize reel player when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeReelPlayer({{ $index }});
    });

    // Also initialize when Livewire is ready
    function initializeReelPlayer(index) {
        const container = document.getElementById(`reelPlayer${index}`);
        const containerWrapper = document.getElementById(`reelPlayerContainer${index}`);
        
        if (!container || !containerWrapper) {
            console.error('Reel player container not found for index:', index);
            return;
        }

        // Check if this reel is currently visible
        const isVisible = containerWrapper.offsetParent !== null;
        if (!isVisible) {
            // Defer initialization until visible
            return;
        }

        // Get reel data from data attributes
        const reelData = {
            videoUrl: container.dataset.videoUrl,
            poster: container.dataset.poster,
            videoId: parseInt(container.dataset.videoId),
            title: container.dataset.videoTitle,
            qualities: JSON.parse(container.dataset.qualities || '[]'),
            autoplay: container.dataset.autoplay === 'true',
            muted: container.dataset.muted === 'true',
            loop: container.dataset.loop === 'true',
            onEnded: function() {
                // Handle video ended - auto advance to next reel
                console.log('Reel ended, advancing to next...');
                if (typeof window.navigateToNextReel === 'function') {
                    window.navigateToNextReel();
                }
            }
        };

        // Initialize the player
        try {
            const art = initReelPlayer(`reelPlayer${index}`, reelData);
            
            if (art) {
                console.log('Reel player initialized for index:', index);
                
                // Store reference globally
                window.reelPlayers = window.reelPlayers || {};
                window.reelPlayers[index] = art;
            }
        } catch (error) {
            console.error('Error initializing reel player:', error);
        }
    }

    // Global function to toggle play/pause for a specific reel
    window.toggleReelPlayPause = function(index) {
        const art = window.reelPlayers?.[index];
        if (art) {
            if (art.playing) {
                art.pause();
            } else {
                art.play();
            }
        }
    };

    // Global function to get current reel player
    window.getCurrentReelPlayer = function() {
        return window.currentReelIndex !== undefined ? window.reelPlayers?.[window.currentReelIndex] : null;
    };
</script>
