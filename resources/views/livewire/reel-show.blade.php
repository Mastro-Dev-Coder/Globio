@php
    use App\Models\WatchLater;
    use App\Models\Video;
@endphp

<div class="min-h-screen">
    <!-- Layout Principale -->
    <div class="flex h-screen max-h-screen">
        <!-- Sidebar Sinistra: Info Reel -->
        <div class="hidden lg:flex w-80 flex-col border-r border-gray-800/30 backdrop-blur-xl">
            <!-- Header -->
            <div class="p-4 border-b border-gray-800/30">
                <div class="flex items-center justify-between">
                    <button onclick="window.history.back()"
                        class="p-2 rounded-full bg-gray-800/50 hover:bg-gray-700/50 transition-colors">
                        <i class="fas fa-arrow-left text-gray-300 text-sm"></i>
                    </button>
                    <div class="w-8"></div>
                </div>
            </div>

            <!-- Content Scrollabile -->
            <div class="flex-1 overflow-y-auto p-4">
                @foreach ($reels as $index => $reel)
                    <div class="reel-info-panel {{ $index === $currentIndex ? 'block' : 'hidden' }}"
                        data-reel-index="{{ $index }}" data-video-id="{{ $reel['id'] }}">

                        <!-- Author Info -->
                        <div class="flex items-center gap-3 mb-4">
                            @if ($reel['user']['avatar_url'])
                                <a href="{{ route('channel.show', $reel['user']['channel_name']) }}" class="block">
                                    <img src="{{ asset('storage/' . $reel['user']['avatar_url']) }}"
                                        alt="{{ $reel['user']['channel_name'] }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-700/50">
                                </a>
                            @else
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center border-2 border-gray-700/50">
                                    <span class="text-white font-bold text-sm">
                                        {{ strtoupper(substr($reel['user']['name'], 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('channel.show', $reel['user']['channel_name']) }}">
                                    <h3 class="text-white font-medium text-sm truncate">
                                        {{ $reel['user']['channel_name'] ?: $reel['user']['name'] }}
                                    </h3>
                                </a>
                                <p class="text-gray-400 text-xs">
                                    {{ number_format($reel['user']['subscribers'] ?? 0) }} iscritti
                                </p>
                            </div>
                        </div>

                        <!-- Title -->
                        <h2 class="text-white font-medium text-base mb-3 leading-tight">
                            {{ $reel['title'] }}
                        </h2>

                        <!-- Description -->
                        @if ($reel['description'])
                            <p class="text-gray-300 text-sm leading-relaxed mb-4 line-clamp-3">
                                {{ $reel['description'] }}
                            </p>
                        @endif

                        <!-- Tags -->
                        @if (isset($reel['tags']) && count($reel['tags']) > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach (array_slice($reel['tags'], 0, 3) as $tag)
                                    <span class="px-2 py-1 bg-gray-800/50 rounded text-gray-300 text-xs">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="text-gray-400 text-sm">
                            <span>{{ $this->formatCount($reel['views_count']) }} visualizzazioni</span>
                            <span class="mx-2">•</span>
                            <span>{{ \Carbon\Carbon::parse($reel['created_at'])->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Area Centrale: Scroll Reels -->
        <div class="flex-1 flex items-center justify-center relative overflow-hidden">
            <!-- Scroll Container -->
            <div class="w-full h-screen overflow-y-auto snap-y snap-mandatory scrollbar-hide" id="reelsScrollContainer"
                style="scroll-behavior: smooth; -ms-overflow-style: none; scrollbar-width: none;">

                <!-- Reels Stack Verticale -->
                <div class="flex flex-col items-center justify-center py-0 space-y-0">
                    @foreach ($reels as $index => $reel)
                        <div class="snap-start flex items-center justify-center w-full min-h-screen max-h-screen"
                            data-reel-index="{{ $index }}" data-video-id="{{ $reel['id'] }}"
                            id="reelContainer{{ $index }}">

                            <!-- Video Card con Overlay Controlli -->
                            <div class="relative w-xl aspect-[9/16] bg-black rounded-2xl overflow-hidden shadow-2xl cursor-pointer group mx-auto"
                                onclick="togglePlayPause()" id="videoContainer{{ $index }}">

                                <!-- Video Element -->
                                <video class="w-full h-full object-cover" muted="{{ $isMuted }}" loop playsinline
                                    preload="metadata" id="videoElement{{ $index }}">
                                    <source src="{{ asset('storage/' . $reel['video_path']) }}" type="video/mp4"
                                        data-original-src="{{ asset('storage/' . $reel['video_path']) }}">
                                </video>

                                <!-- Loading Spinner per Cambio Qualità -->
                                <div id="reelQualityLoading{{ $index }}"
                                    class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 z-40">
                                    <div
                                        class="w-16 h-16 border-4 border-white/30 border-t-white rounded-full animate-spin">
                                    </div>
                                </div>

                                <!-- Overlay Controlli con Auto-Hide -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                    id="controlsOverlay{{ $index }}">

                                    <!-- Pulsante Play/Pause Grande -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <button onclick="togglePlayPause(); event.stopPropagation();"
                                            class="w-16 h-16 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-black/70 transition-all duration-200 hover:scale-110"
                                            id="playPauseBtn{{ $index }}">
                                            <i class="fas fa-play text-white text-xl ml-1"
                                                id="playPauseIcon{{ $index }}"></i>
                                        </button>
                                    </div>

                                    <!-- Controlli Bottom -->
                                    <div class="absolute bottom-0 left-0 right-0 p-4">
                                        <!-- Progress Bar Interattiva -->
                                        <div class="mb-3">
                                            <div class="relative h-1 bg-white/30 rounded-full cursor-pointer"
                                                onclick="seekTo(event, {{ $index }})"
                                                id="progressContainer{{ $index }}">
                                                <div class="absolute inset-y-0 left-0 bg-red-600 rounded-full transition-all duration-100"
                                                    style="width: 0%" id="progressBar{{ $index }}"></div>
                                                <div class="absolute inset-y-0 left-0 w-3 h-3 bg-red-600 rounded-full transform -translate-y-1 -translate-x-1 opacity-0 hover:opacity-100 transition-opacity duration-200"
                                                    style="left: 0%" id="progressHandle{{ $index }}"></div>
                                            </div>
                                        </div>

                                        <!-- Controlli Bottom Row -->
                                        <div class="flex items-center justify-between">
                                            <!-- Left Controls -->
                                            <div class="flex items-center gap-3">
                                                <!-- Volume Controls -->
                                                <div class="flex items-center gap-2 group/volume">
                                                    <!-- Volume Icon -->
                                                    <button onclick="toggleMute(); event.stopPropagation();"
                                                        class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                                                        <i class="fas {{ $isMuted ? 'fa-volume-mute' : 'fa-volume-up' }} text-white"
                                                            id="volumeIcon{{ $index }}"></i>
                                                    </button>

                                                    <!-- Volume Slider -->
                                                    <div
                                                        class="relative w-0 group-hover/volume:w-20 overflow-hidden transition-all duration-300">
                                                        <input type="range" min="0" max="100"
                                                            value="{{ $isMuted ? 0 : 100 }}"
                                                            class="h-1 bg-white/30 rounded-lg appearance-none cursor-pointer slider"
                                                            id="volumeSlider{{ $index }}"
                                                            oninput="setVolume({{ $index }}, this.value)"
                                                            onchange="setVolume({{ $index }}, this.value)"
                                                            style="background: linear-gradient(to right, #ef4444 0%, #ef4444 var(--progress, 100%), rgba(255,255,255,0.3) var(--progress, 100%), rgba(255,255,255,0.3) 100%)">
                                                    </div>
                                                </div>

                                                <!-- Time -->
                                                <span class="text-white text-xs font-mono"
                                                    id="timeDisplay{{ $index }}">
                                                    0:00 / 0:00
                                                </span>
                                            </div>

                                            <!-- Right Controls -->
                                            <div class="flex items-center gap-2">
                                                <!-- Video Quality -->
                                                <div class="relative group/quality">
                                                    <button onclick="toggleQualityMenu(); event.stopPropagation();"
                                                        class="p-2 hover:bg-white/20 rounded-lg transition-colors"
                                                        title="Impostazioni qualità video"
                                                        id="reelQualityBtn{{ $index }}">
                                                        <i class="fas fa-cog text-white"></i>
                                                    </button>
                                                    <div id="reelQualityMenu{{ $index }}"
                                                        class="absolute bottom-12 right-0 bg-gray-800/90 backdrop-blur-xl border border-gray-700 rounded-lg py-2 min-w-48 opacity-0 invisible group-hover/quality:opacity-100 group-hover/quality:visible transition-all duration-200 z-50">
                                                        <div
                                                            class="text-gray-400 text-xs uppercase tracking-wide mb-2 px-4">
                                                            Qualità Video</div>
                                                        @php
                                                            $availableQualities = $currentVideo->getAvailableQualities();
                                                            $qualityLabels = [
                                                                'auto' => 'Auto',
                                                                'original' => 'Originale',
                                                                '2160p' => '2160p 4K Ultra HD',
                                                                '1440p' => '1440p 2K QHD',
                                                                '1080p' => '1080p Full HD',
                                                                '720p' => '720p HD',
                                                                '480p' => '480p',
                                                                '360p' => '360p',
                                                            ];
                                                        @endphp

                                                        <!-- Auto option -->
                                                        <button
                                                            onclick="selectReelQuality('auto', {{ $index }})"
                                                            class="quality-option w-full text-left px-4 py-2 text-white hover:bg-white/10 text-sm transition-colors duration-150 flex items-center">
                                                            <i class="fas fa-magic mr-3"></i>
                                                            <span class="flex-1">Auto</span>
                                                            <i
                                                                class="fas fa-check text-red-400 opacity-0 check-icon"></i>
                                                        </button>

                                                        <!-- Quality options -->
                                                        @foreach ($availableQualities as $quality => $qualityData)
                                                            <button
                                                                onclick="selectReelQuality('{{ $quality }}', {{ $index }})"
                                                                class="quality-option w-full text-left px-4 py-2 text-white hover:bg-white/10 text-sm transition-colors duration-150 flex items-center">
                                                                @php
                                                                    $icon = 'fas fa-video';
                                                                    switch ($quality) {
                                                                        case 'original':
                                                                            $icon = 'fas fa-crown';
                                                                            break;
                                                                        case '2160p':
                                                                        case '1440p':
                                                                        case '1080p':
                                                                            $icon = 'fas fa-desktop';
                                                                            break;
                                                                        case '720p':
                                                                            $icon = 'fas fa-tablet-alt';
                                                                            break;
                                                                        case '480p':
                                                                        case '360p':
                                                                            $icon = 'fas fa-mobile-alt';
                                                                            break;
                                                                    }
                                                                @endphp
                                                                <i class="{{ $icon }} mr-3"></i>
                                                                <span class="flex-1">
                                                                    {{ $qualityLabels[$quality] ?? $quality }}
                                                                    @if (isset($qualityData['formatted_file_size']) && $qualityData['formatted_file_size'] !== 'N/A')
                                                                        <span
                                                                            class="text-gray-400 text-xs ml-2">({{ $qualityData['formatted_file_size'] }})</span>
                                                                    @endif
                                                                </span>
                                                                <i
                                                                    class="fas fa-check text-red-400 opacity-0 check-icon"></i>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Fullscreen -->
                                                <button onclick="toggleFullscreen(); event.stopPropagation();"
                                                    class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                                                    <i class="fas fa-expand text-white"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Destra: Azioni -->
        <div
            class="hidden md:flex w-20 flex-col items-center justify-center space-y-6 p-4 border-l border-gray-800 bg-gray-900/50">
            @if (isset($reels[$currentIndex]))
                @php
                    $currentReel = $reels[$currentIndex];
                    $currentUser = (object) $currentReel['user'];
                @endphp
                <!-- User Avatar -->
                <div class="w-12 h-12 rounded-full overflow-hidden">
                    @if ($currentUser->avatar_url)
                        <a href="{{ route('channel.show', $currentUser->channel_name) }}">
                            <img src="{{ asset('storage/' . $currentUser->avatar_url) }}"
                                alt="{{ $currentUser->channel_name }}" class="w-full h-full object-cover">
                        </a>
                    @else
                        <div
                            class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">
                                {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Azioni -->
                <livewire:reel-like-dislike :video="App\Models\Video::find($currentReel['id'])" :key="'like-dislike-' . $currentReel['id'] . '-' . $currentIndex" />

                <!-- Comment -->
                <button wire:click="toggleComments({{ $currentReel['id'] }})"
                    class="flex flex-col items-center space-y-1">
                    <div
                        class="w-14 h-14 bg-black/40 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 hover:border-white/20 shadow-lg cursor-pointer">
                        <i class="fas fa-comment text-white"></i>
                    </div>
                    <span class="text-white text-xs">
                        {{ $this->formatCount($currentReel['comments_count']) }}
                    </span>
                </button>

                <!-- Share -->
                <button wire:click="shareVideo" class="flex flex-col items-center space-y-1">
                    <div
                        class="w-14 h-14 bg-black/40 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 hover:border-white/20 shadow-lg cursor-pointer">
                        <i class="fas fa-share text-white"></i>
                    </div>
                    <span class="text-white text-xs">Condividi</span>
                </button>

                <!-- Save -->
                <button wire:click="toggleWatchLater" class="flex flex-col items-center space-y-1">
                    <div
                        class="w-14 h-14 bg-black/40 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 hover:border-white/20 shadow-lg cursor-pointer">
                        <i class="fas {{ $currentReel['is_in_watch_later'] ? 'fa-check-double' : 'fa-clock' }} text-white"
                            style="color: {{ $currentReel['is_in_watch_later'] ? '#22c55e' : 'inherit' }}"></i>
                    </div>
                    <span class="text-white text-xs">Guarda più tardi</span>
                </button>
            @endif
        </div>

        <!-- Sidebar Navigazione -->
        <div
            class="hidden md:flex w-16 flex-col items-center justify-center space-y-4 p-4 border-l border-gray-800 bg-gray-900/50 relative">
            <!-- Freccia Su -->
            <button wire:click="previousVideo" wire:target="previousVideo" onclick="navigateReel(-1)"
                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-gray-700 transition-all duration-200 hover:scale-105 {{ $currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'opacity-80 hover:opacity-100' }}"
                {{ $currentIndex === 0 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-up text-white text-sm"></i>
            </button>

            <!-- Indicatore -->
            <div class="text-center bg-gray-800/80 backdrop-blur-sm rounded-lg px-3 py-2 border border-gray-700">
                <div class="text-white font-medium text-sm">{{ $currentIndex + 1 }}</div>
                <div class="text-gray-400 text-xs">di {{ count($reels) }}</div>
            </div>

            <!-- Freccia Giù -->
            <button wire:click="nextVideo" wire:target="nextVideo" onclick="navigateReel(1)"
                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-gray-700 transition-all duration-200 hover:scale-105 {{ $currentIndex >= count($reels) - 1 ? 'opacity-30 cursor-not-allowed' : 'opacity-80 hover:opacity-100' }}"
                {{ $currentIndex >= count($reels) - 1 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-down text-white text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-20 backdrop-blur-sm">
        <div class="flex items-center justify-around">
            <button wire:click="previousVideo" wire:target="previousVideo" onclick="navigateReel(-1)"
                class="p-4 bg-gray-800/80 backdrop-blur-sm rounded-full {{ $currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-700 hover:scale-105' }} transition-all duration-200 border border-gray-700/50"
                {{ $currentIndex === 0 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-up text-white"></i>
            </button>
            <div class="text-center bg-gray-800/80 backdrop-blur-sm rounded-lg px-6 py-3 border border-gray-700/50">
                <div class="text-white font-medium">{{ $currentIndex + 1 }}</div>
                <div class="text-gray-400 text-xs">di {{ count($reels) }}</div>
                <!-- Progress bar mobile -->
                <div class="mt-2 w-20 h-1 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-red-600 rounded-full transition-all duration-300 mobile-progress-bar"
                        style="width: {{ (($currentIndex + 1) / count($reels)) * 100 }}%"></div>
                </div>
            </div>
            <button wire:click="nextVideo" wire:target="nextVideo" onclick="navigateReel(1)"
                class="p-4 bg-gray-800/80 backdrop-blur-sm rounded-full {{ $currentIndex >= count($reels) - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-700 hover:scale-105' }} transition-all duration-200 border border-gray-700/50"
                {{ $currentIndex >= count($reels) - 1 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-down text-white"></i>
            </button>
        </div>
    </div>

    <!-- Comments Panel -->
    @if ($showComments && $currentCommentVideoId && isset($reels[$currentIndex]))
        <div class="fixed inset-y-0 right-0 w-full md:w-96 bg-gray-900 border-l border-gray-800 z-50">
            <div class="h-full flex flex-col">
                <!-- Comments Header -->
                <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button wire:click="$set('showComments', false)"
                            class="p-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <i class="fas fa-arrow-left text-white"></i>
                        </button>
                        <h3 class="text-white font-medium">Commenti</h3>
                    </div>
                    <span
                        class="text-gray-400 text-sm">{{ $this->formatCount($reels[$currentIndex]['comments_count']) }}</span>
                </div>
                <livewire:reel-comments :video="App\Models\Video::find($reels[$currentIndex]['id'])" :key="'comments-' . $reels[$currentIndex]['id']" />
            </div>
        </div>
    @endif

    <!-- Context Menu -->
    @if ($showContextMenu && $contextMenuVideo)
        <div class="fixed inset-0 z-50" wire:click="hideContextMenu">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50"></div>
            <!-- Menu Content -->
            <div class="absolute bg-gray-800 border border-gray-700 rounded-lg shadow-xl min-w-64 overflow-hidden"
                style="top: {{ $contextMenuPosition['y'] }}px; left: {{ $contextMenuPosition['x'] }}px; z-index: 51;"
                onclick="event.stopPropagation()">
                <div class="py-2">
                    <!-- Video Quality Section -->
                    @php
                        $contextMenuVideoObj = $this->getContextMenuVideo();
                        $availableQualities = $this->getContextMenuVideoQualities();
                    @endphp
                    @if ($contextMenuVideoObj && !empty($availableQualities))
                        <div class="px-4 py-2 border-b border-gray-700">
                            <div class="text-gray-400 text-xs uppercase tracking-wide mb-2">Qualità video</div>
                            <div class="flex flex-col gap-1">
                                <button onclick="selectVideoQuality('Auto')"
                                    class="text-left text-white hover:bg-gray-700 px-2 py-1 rounded text-sm flex items-center gap-2">
                                    <i class="fas fa-check text-green-400 w-3"></i>
                                    <span>Auto</span>
                                </button>
                                @foreach ($availableQualities as $quality => $qualityData)
                                    <button onclick="selectVideoQuality('{{ $quality }}')"
                                        class="text-left text-white hover:bg-gray-700 px-2 py-1 rounded text-sm">
                                        {{ $qualityData['label'] ?? $quality }}
                                        @if (isset($qualityData['formatted_file_size']))
                                            <span
                                                class="text-gray-500 text-xs">({{ $qualityData['formatted_file_size'] }})</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @elseif($contextMenuVideoObj)
                        <div class="px-4 py-2 border-b border-gray-700">
                            <div class="text-gray-400 text-xs uppercase tracking-wide mb-2">Qualità video</div>
                            <div class="text-gray-500 text-sm px-2 py-1">
                                Qualità non disponibili
                            </div>
                        </div>
                    @endif

                    <!-- Actions Section -->
                    <div class="py-1">
                        <button wire:click="addToWatchLaterFromContext"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i
                                class="fas fa-bookmark w-4 {{ $contextMenuVideoObj && WatchLater::isInWatchLater($userId ?? 0, $contextMenuVideoObj->id) ? 'text-yellow-400' : 'text-gray-400' }}"></i>
                            <span class="text-sm">
                                {{ $contextMenuVideoObj && WatchLater::isInWatchLater($userId ?? 0, $contextMenuVideoObj->id) ? 'Rimuovi da' : 'Aggiungi a' }}
                                Guarda più tardi
                            </span>
                        </button>

                        <button onclick="openInNewTab()"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-external-link-alt text-gray-400 w-4"></i>
                            <span class="text-sm">Apri in nuova scheda</span>
                        </button>

                        <button wire:click="shareVideo"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-share text-gray-400 w-4"></i>
                            <span class="text-sm">Condividi</span>
                        </button>

                        <button onclick="copyVideoLink()"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-link text-gray-400 w-4"></i>
                            <span class="text-sm">Copia link</span>
                        </button>

                        <button onclick="downloadVideo()"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-download text-gray-400 w-4"></i>
                            <span class="text-sm">Scarica video</span>
                        </button>

                        <button wire:click="notInterested"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-eye-slash text-gray-400 w-4"></i>
                            <span class="text-sm">Non mi interessa questo canale</span>
                        </button>
                    </div>

                    <div class="border-t border-gray-700 my-1"></div>

                    <!-- Additional Options -->
                    <div class="py-1">
                        <button onclick="addToPlaylist()"
                            class="w-full px-4 py-2 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-list text-gray-400 w-4"></i>
                            <span class="text-sm">Aggiungi a playlist</span>
                        </button>

                        <button wire:click="reportVideo"
                            class="w-full px-4 py-2 text-left text-red-400 hover:bg-gray-700 transition-colors flex items-center gap-3">
                            <i class="fas fa-flag w-4"></i>
                            <span class="text-sm">Segnala</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black/80 flex items-center justify-center z-40 hidden">
        <div class="text-center">
            <div class="w-12 h-12 border-4 border-gray-600 border-t-red-600 rounded-full animate-spin mx-auto mb-4">
            </div>
            <p class="text-white">Caricamento...</p>
        </div>
    </div>

    <script>
        class ReelScrollManager {
            constructor() {
                this.currentIndex = {{ $currentIndex }};
                this.totalReels = {{ count($reels) }};
                this.isScrolling = false;
                this.videoElements = new Map();
                this.volumeLevels = new Map();
                this.isMuted = {{ $isMuted ? 'true' : 'false' }};
                this.observer = null;

                // Reel quality management variables
                this.reelCurrentQualities = new Map(); // Per ogni video
                this.reelVideoQualities = new Map(); // Per ogni video
                this.reelRecommendedQualities = new Map(); // Per ogni video
                this.reelConnectionSpeeds = new Map(); // Per ogni video

                this.init();
            }

            init() {
                this.cacheElements();
                this.setupIntersectionObserver();
                this.setupScrollEvents();
                this.bindEvents();
                this.setupKeyboardNavigation();
                this.initializeCurrentVideo();
                this.setupContextMenu();
                this.initializeReelQualities();
            }

            cacheElements() {
                // Cache all video elements
                document.querySelectorAll('[id^="videoElement"]').forEach((video, index) => {
                    this.videoElements.set(index, video);

                    // Initialize volume levels
                    this.volumeLevels.set(index, this.isMuted ? 0 : 100);

                    // Setup video event listeners
                    video.addEventListener('timeupdate', () => {
                        this.updateProgressBar(index);
                        this.updateTimeDisplay(index);
                    });

                    video.addEventListener('loadedmetadata', () => {
                        this.updateTimeDisplay(index);
                    });

                    video.addEventListener('play', () => {
                        this.updatePlayButton(index, false);
                    });

                    video.addEventListener('pause', () => {
                        this.updatePlayButton(index, true);
                    });

                    // Auto-advance to next reel when video ends
                    video.addEventListener('ended', () => {
                        if (index < this.totalReels - 1) {
                            this.switchToVideo(index + 1);
                        }
                    });
                });
            }

            setupIntersectionObserver() {
                const options = {
                    root: document.getElementById('reelsScrollContainer'),
                    rootMargin: '0px',
                    threshold: 0.7 // Quando il 70% del video è visibile
                };

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const videoIndex = parseInt(entry.target.closest('[data-reel-index]')
                                .dataset.reelIndex);
                            if (videoIndex !== this.currentIndex) {
                                this.switchToVideo(videoIndex);
                            }
                        }
                    });
                }, options);

                // Observe all reel containers
                document.querySelectorAll('[data-reel-index]').forEach(container => {
                    this.observer.observe(container);
                });
            }

            setupScrollEvents() {
                const container = document.getElementById('reelsScrollContainer');
                if (!container) return;

                let scrollTimeout;
                container.addEventListener('scroll', () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        this.handleScrollEnd();
                    }, 100);
                });
            }

            handleScrollEnd() {
                const container = document.getElementById('reelsScrollContainer');
                if (!container || this.isScrolling) return;

                // Find the reel that's most centered in the viewport
                const containerRect = container.getBoundingClientRect();
                const containerCenter = containerRect.top + containerRect.height / 2;

                let closestIndex = this.currentIndex;
                let minDistance = Infinity;

                document.querySelectorAll('[data-reel-index]').forEach(reel => {
                    const reelRect = reel.getBoundingClientRect();
                    const reelCenter = reelRect.top + reelRect.height / 2;
                    const distance = Math.abs(reelCenter - containerCenter);

                    if (distance < minDistance) {
                        minDistance = distance;
                        closestIndex = parseInt(reel.dataset.reelIndex);
                    }
                });

                if (closestIndex !== this.currentIndex) {
                    this.switchToVideo(closestIndex);
                }
            }

            switchToVideo(newIndex) {
                if (newIndex < 0 || newIndex >= this.totalReels || newIndex === this.currentIndex) return;

                // Pause current video
                const currentVideo = this.videoElements.get(this.currentIndex);
                if (currentVideo) {
                    currentVideo.pause();
                    // Reset controls for old video
                    this.updatePlayButton(this.currentIndex, true);
                }

                // Update current index
                this.currentIndex = newIndex;

                // Play new video
                const newVideo = this.videoElements.get(newIndex);
                if (newVideo) {
                    newVideo.muted = this.isMuted;
                    
                    // Try to play with fallback for browser autoplay policies
                    const playPromise = newVideo.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.catch(e => {
                            console.log('Auto-play prevented:', e);
                            // If autoplay is blocked, try with muted
                            newVideo.muted = true;
                            newVideo.play().catch(e2 => {
                                console.log('Muted auto-play also prevented:', e2);
                            });
                        });
                    }
                    
                    this.updatePlayButton(newIndex, false);
                }

                // Update UI
                this.updateNavigationButtons();
                this.updateInfoPanels();

                // Notify Livewire if available
                if (window.Livewire) {
                    window.Livewire.dispatch('reelChanged', {
                        index: newIndex,
                        videoId: document.querySelector(`[data-reel-index="${newIndex}"]`).dataset.videoId
                    });

                    // Update like-dislike component data
                    const currentReelData = this.getCurrentReelData();
                    if (currentReelData) {
                        window.Livewire.dispatch('updateVideoForReelLikeDislike', currentReelData.videoId);
                    }
                }
            }

            initializeCurrentVideo() {
                // Play current video on init
                const currentVideo = this.videoElements.get(this.currentIndex);
                if (currentVideo) {
                    currentVideo.muted = this.isMuted;
                    
                    // Try to play with fallback for browser autoplay policies
                    const playPromise = currentVideo.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.catch(e => {
                            console.log('Initial auto-play prevented:', e);
                            // If autoplay is blocked, try with muted
                            currentVideo.muted = true;
                            currentVideo.play().catch(e2 => {
                                console.log('Muted auto-play also prevented:', e2);
                            });
                        });
                    }
                    
                    this.updatePlayButton(this.currentIndex, false);
                }

                // Initialize volume controls for current video
                setTimeout(() => {
                    const currentSlider = document.getElementById(`volumeSlider${this.currentIndex}`);
                    if (currentSlider) {
                        currentSlider.value = this.isMuted ? 0 : 100;
                        currentSlider.style.setProperty('--progress', `${this.isMuted ? 0 : 100}%`);
                    }
                }, 100);

                // Update like-dislike component for current video
                const currentReelData = this.getCurrentReelData();
                if (currentReelData && window.Livewire) {
                    setTimeout(() => {
                        window.Livewire.dispatch('updateVideoForReelLikeDislike', currentReelData.videoId);
                    }, 200);
                }
            }

            updatePlayButton(index, showPlay) {
                const playIcon = document.getElementById(`playPauseIcon${index}`);
                if (playIcon) {
                    playIcon.className = showPlay ? 'fas fa-play text-white text-xl ml-1' :
                        'fas fa-pause text-white text-xl';
                }
            }

            updateProgressBar(index) {
                const video = this.videoElements.get(index);
                const progressBar = document.getElementById(`progressBar${index}`);
                const progressHandle = document.getElementById(`progressHandle${index}`);

                if (video && progressBar && progressHandle && video.duration) {
                    const progress = (video.currentTime / video.duration) * 100;
                    progressBar.style.width = `${progress}%`;
                    progressHandle.style.left = `${progress}%`;
                }
            }

            updateTimeDisplay(index) {
                const video = this.videoElements.get(index);
                const timeDisplay = document.getElementById(`timeDisplay${index}`);

                if (video && timeDisplay && video.duration) {
                    const current = this.formatTime(video.currentTime);
                    const duration = this.formatTime(video.duration);
                    timeDisplay.textContent = `${current} / ${duration}`;
                }
            }

            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            }

            updateNavigationButtons() {
                // Update all navigation buttons
                const prevBtns = document.querySelectorAll('[wire\\:click*="previousVideo"]');
                const nextBtns = document.querySelectorAll('[wire\\:click*="nextVideo"]');

                prevBtns.forEach(btn => {
                    btn.disabled = this.currentIndex === 0;
                    btn.classList.toggle('opacity-30', this.currentIndex === 0);
                    btn.classList.toggle('cursor-not-allowed', this.currentIndex === 0);
                });

                nextBtns.forEach(btn => {
                    btn.disabled = this.currentIndex === this.totalReels - 1;
                    btn.classList.toggle('opacity-30', this.currentIndex === this.totalReels - 1);
                    btn.classList.toggle('cursor-not-allowed', this.currentIndex === this.totalReels - 1);
                });

                // Non aggiorniamo più i contatori qui per evitare conflitti
                // I contatori vengono aggiornati in updateSidebarData()
            }

            updateInfoPanels() {
                document.querySelectorAll('.reel-info-panel').forEach((panel, index) => {
                    if (index === this.currentIndex) {
                        panel.classList.remove('hidden');
                        panel.classList.add('block');
                    } else {
                        panel.classList.remove('block');
                        panel.classList.add('hidden');
                    }
                });

                // Aggiorna anche i contatori e i dati nelle sidebar
                this.updateSidebarData();
            }

            updateSidebarData() {
                // Aggiorna il contatore principale
                const counters = document.querySelectorAll('.font-medium');
                counters.forEach(counter => {
                    if (counter.textContent.includes('/') || counter.textContent.includes('di')) {
                        // Non modificare i contatori "di X"
                        return;
                    }
                    if (!isNaN(parseInt(counter.textContent))) {
                        counter.textContent = this.currentIndex + 1;
                    }
                });

                // Aggiorna il contatore nei pannelli mobile
                const mobileCounters = document.querySelectorAll('.md\\:hidden .font-medium');
                mobileCounters.forEach(counter => {
                    if (!counter.textContent.includes('/') && !counter.textContent.includes('di')) {
                        counter.textContent = this.currentIndex + 1;
                    }
                });

                // Aggiorna la progress bar mobile
                const mobileProgressBar = document.querySelector('.mobile-progress-bar');
                if (mobileProgressBar) {
                    const progressPercentage = ((this.currentIndex + 1) / this.totalReels) * 100;
                    mobileProgressBar.style.width = `${progressPercentage}%`;
                }
            }

            setupContextMenu() {
                // Remove the inline contextmenu handler and use this instead
                document.querySelectorAll('[data-reel-index]').forEach(container => {
                    container.addEventListener('contextmenu', (e) => {
                        e.preventDefault();

                        const videoIndex = parseInt(container.dataset.reelIndex);
                        const videoId = container.dataset.videoId;

                        if (window.Livewire) {
                            window.Livewire.dispatch('showContextMenu', {
                                videoId: videoId,
                                x: e.clientX,
                                y: e.clientY
                            });
                        }
                    });
                });

                // Close context menu on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && window.Livewire) {
                        window.Livewire.dispatch('hideContextMenu');
                    }
                });

                // Close context menu when clicking outside
                document.addEventListener('click', (e) => {
                    const contextMenu = document.querySelector('[wire\\:click="hideContextMenu"]');
                    if (contextMenu && !contextMenu.contains(e.target)) {
                        if (window.Livewire) {
                            window.Livewire.dispatch('hideContextMenu');
                        }
                    }
                });
            }

            togglePlayPause() {
                const video = this.videoElements.get(this.currentIndex);
                if (!video) return;

                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            }

            toggleMute() {
                this.isMuted = !this.isMuted;

                // Update all videos
                this.videoElements.forEach(video => {
                    video.muted = this.isMuted;
                });

                // Update volume sliders and icons
                for (let i = 0; i < this.totalReels; i++) {
                    const slider = document.getElementById(`volumeSlider${i}`);
                    const icon = document.getElementById(`volumeIcon${i}`);

                    if (slider && icon) {
                        if (this.isMuted) {
                            // Store current volume before muting
                            if (slider.value > 0) {
                                this.volumeLevels.set(i, parseInt(slider.value));
                            }
                            slider.value = 0;
                            slider.style.setProperty('--progress', '0%');
                            icon.className = 'fas fa-volume-mute text-white';
                        } else {
                            // Restore previous volume
                            const previousVolume = this.volumeLevels.get(i) || 100;
                            slider.value = previousVolume;
                            slider.style.setProperty('--progress', `${previousVolume}%`);

                            if (previousVolume == 0) {
                                icon.className = 'fas fa-volume-mute text-white';
                            } else if (previousVolume < 50) {
                                icon.className = 'fas fa-volume-down text-white';
                            } else {
                                icon.className = 'fas fa-volume-up text-white';
                            }
                        }
                    }
                }

                // Notify Livewire
                if (window.Livewire) {
                    window.Livewire.dispatch('toggleMute');
                }
            }

            bindEvents() {
                // Global toggle play/pause
                window.togglePlayPause = () => this.togglePlayPause();
                window.toggleMute = () => this.toggleMute();
                window.navigateReel = (direction) => {
                    const newIndex = this.currentIndex + direction;
                    if (newIndex >= 0 && newIndex < this.totalReels) {
                        this.switchToVideo(newIndex);
                        // Scroll to the new reel
                        const container = document.getElementById('reelsScrollContainer');
                        const targetReel = document.getElementById(`reelContainer${newIndex}`);
                        if (container && targetReel) {
                            const containerHeight = container.clientHeight;
                            const targetPosition = targetReel.offsetTop - (containerHeight - targetReel
                                .clientHeight) / 2;
                            container.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                };

                // Bind volume slider function
                window.setVolume = (index, value) => {
                    const video = this.videoElements.get(index);
                    const slider = document.getElementById(`volumeSlider${index}`);
                    const icon = document.getElementById(`volumeIcon${index}`);

                    if (video && slider && icon) {
                        const volume = value / 100;
                        video.volume = volume;
                        video.muted = volume === 0;

                        // Update slider progress
                        slider.style.setProperty('--progress', `${value}%`);

                        // Store volume level
                        this.volumeLevels.set(index, parseInt(value));

                        // Update icon
                        if (value == 0) {
                            icon.className = 'fas fa-volume-mute text-white';
                            this.isMuted = true;
                        } else {
                            if (value < 50) {
                                icon.className = 'fas fa-volume-down text-white';
                            } else {
                                icon.className = 'fas fa-volume-up text-white';
                            }
                            this.isMuted = false;
                        }
                    }
                };

                // Bind seek function
                window.seekTo = (event, index) => {
                    const video = this.videoElements.get(index);
                    const progressContainer = document.getElementById(`progressContainer${index}`);

                    if (video && progressContainer && video.duration) {
                        const rect = progressContainer.getBoundingClientRect();
                        const clickX = event.clientX - rect.left;
                        const percentage = clickX / rect.width;
                        video.currentTime = video.duration * percentage;
                    }
                };

                // Bind fullscreen function
                window.toggleFullscreen = () => {
                    const videoContainer = document.getElementById(`videoContainer${this.currentIndex}`);
                    if (!document.fullscreenElement) {
                        videoContainer?.requestFullscreen().catch(err => {
                            console.log('Error attempting to enable fullscreen:', err);
                        });
                    } else {
                        document.exitFullscreen();
                    }
                };

                // Context menu functions
                window.openInNewTab = () => {
                    const currentReelData = this.getCurrentReelData();
                    if (currentReelData && currentReelData.videoId) {
                        window.open(`/reel/show/${currentReelData.videoId}`, '_blank');
                    }
                    if (window.Livewire) {
                        window.Livewire.dispatch('hideContextMenu');
                    }
                };

                window.copyVideoLink = () => {
                    const currentReelData = this.getCurrentReelData();
                    if (currentReelData && currentReelData.videoId) {
                        const shareUrl = `${window.location.origin}/reel/show/${currentReelData.videoId}`;
                        navigator.clipboard.writeText(shareUrl).then(() => {
                            this.showToast('Link copiato negli appunti!', 'success');
                        }).catch(err => {
                            console.log('Failed to copy link:', err);
                            this.showToast('Errore nel copiare il link', 'error');
                        });
                    }
                    if (window.Livewire) {
                        window.Livewire.dispatch('hideContextMenu');
                    }
                };

                window.downloadVideo = () => {
                    const currentVideo = this.videoElements.get(this.currentIndex);
                    if (currentVideo) {
                        const videoSrc = currentVideo.querySelector('source')?.src;
                        if (videoSrc) {
                            const link = document.createElement('a');
                            link.href = videoSrc;
                            link.download = 'reel-video.mp4';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            this.showToast('Download iniziato', 'success');
                        }
                    }
                    if (window.Livewire) {
                        window.Livewire.dispatch('hideContextMenu');
                    }
                };

                window.addToPlaylist = () => {
                    this.showToast('Funzione playlist in arrivo!', 'info');
                    if (window.Livewire) {
                        window.Livewire.dispatch('hideContextMenu');
                    }
                };

                // Quality selection handler
                window.selectVideoQuality = (quality) => {
                    if (window.Livewire) {
                        window.Livewire.dispatch('changeVideoQuality', {
                            quality: quality
                        });
                    } else {
                        // Fallback se Livewire non è disponibile
                        if (quality === 'Auto') {
                            this.showToast('Qualità impostata su Auto', 'success');
                        } else {
                            this.showToast(`Qualità cambiata a: ${quality}`, 'success');
                        }
                        this.hideContextMenu();
                    }
                };

                // Reel quality menu handler
                window.toggleQualityMenu = () => {
                    const qualityMenu = document.getElementById(`reelQualityMenu${this.currentIndex}`);
                    if (qualityMenu) {
                        const isVisible = qualityMenu.classList.contains('opacity-100');
                        if (isVisible) {
                            qualityMenu.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                            qualityMenu.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                        } else {
                            qualityMenu.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
                            qualityMenu.classList.add('opacity-100', 'visible', 'pointer-events-auto');
                        }
                    }
                };

                // Legacy function for backward compatibility
                window.showReelContextMenu = (index, videoId, event) => {
                    event.preventDefault();
                    if (window.Livewire) {
                        window.Livewire.dispatch('showContextMenu', {
                            videoId: videoId,
                            x: event.clientX,
                            y: event.clientY
                        });
                    }
                };
            }

            getCurrentVideoId() {
                const currentContainer = document.querySelector(`[data-reel-index="${this.currentIndex}"]`);
                return currentContainer?.dataset.videoId || '';
            }

            getCurrentReelData() {
                const currentContainer = document.querySelector(`[data-reel-index="${this.currentIndex}"]`);
                if (!currentContainer) return null;

                const videoId = currentContainer.dataset.videoId;
                const videoElement = currentContainer.querySelector('video');
                const sourceElement = videoElement?.querySelector('source');

                return {
                    videoId: videoId,
                    videoSrc: sourceElement?.src || '',
                    index: this.currentIndex
                };
            }

            handleLikeDislikeUpdate(data) {
                // Update the like-dislike counts in the UI
                if (data && data.videoId && data.likesCount !== undefined && data.dislikesCount !== undefined) {
                    // Update any UI elements that display like/dislike counts
                    // This ensures the numbers shown are always in sync
                    console.log('Like/dislike updated:', data);
                }
            }

            showToast(message, type = 'info') {
                const colors = {
                    success: 'from-green-500 to-emerald-600',
                    error: 'from-red-500 to-pink-600',
                    info: 'from-blue-500 to-indigo-600',
                    warning: 'from-yellow-500 to-orange-600'
                };

                const icons = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-circle',
                    info: 'fas fa-info-circle',
                    warning: 'fas fa-exclamation-triangle'
                };

                const toast = document.createElement('div');
                toast.className =
                    `fixed top-6 right-6 bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-2xl shadow-2xl z-50 transform translate-x-full transition-transform duration-500 border border-white/20 backdrop-blur-sm`;
                toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="${icons[type]} text-lg"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                }, 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 500);
                }, 3000);
            }

            // =================== REEL QUALITY SYSTEM ===================

            // Initialize reel quality system for all videos
            initializeReelQualities() {
                // Initialize quality system for each video
                for (let i = 0; i < this.totalReels; i++) {
                    this.initializeVideoQuality(i);
                }
            }

            // Initialize quality system for a specific video
            initializeVideoQuality(videoIndex) {
                const video = this.videoElements.get(videoIndex);
                if (!video) return;

                // Get available qualities for this specific video
                const videoContainer = document.querySelector(`[data-reel-index="${videoIndex}"]`);
                if (!videoContainer) return;

                const videoId = videoContainer.dataset.videoId;

                // Load video qualities from the server (this would need to be implemented)
                // For now, we'll use the same qualities as the current video
                @php
                    $currentVideoQualities = $currentVideo->getAvailableQualities();
                @endphp
                const videoQualities = @json($currentVideoQualities);

                this.reelVideoQualities.set(videoIndex, videoQualities);

                // Detect connection and set recommended quality for this video
                this.detectConnectionForVideo(videoIndex);

                // Load saved quality preference for this video
                const savedQuality = localStorage.getItem(`reelVideoQuality_${videoId}`);
                const currentQuality = savedQuality || 'auto';
                this.reelCurrentQualities.set(videoIndex, currentQuality);

                // Update UI for this video
                this.updateReelQualityUI(videoIndex, currentQuality);
            }

            // Detect connection speed for a specific video
            detectConnectionForVideo(videoIndex) {
                if ('connection' in navigator) {
                    const connection = navigator.connection;
                    const connectionSpeed = connection.effectiveType;
                    this.reelConnectionSpeeds.set(videoIndex, connectionSpeed);

                    // Determine recommended quality based on connection
                    const recommendedQuality = this.getRecommendedQuality(connectionSpeed);
                    this.reelRecommendedQualities.set(videoIndex, recommendedQuality);
                } else {
                    // Fallback: assume good connection
                    this.reelConnectionSpeeds.set(videoIndex, '4g');
                    this.reelRecommendedQualities.set(videoIndex, '1080p');
                }
            }

            // Get recommended quality based on connection
            getRecommendedQuality(connectionType) {
                const qualityMap = {
                    'slow-2g': '360p',
                    '2g': '360p',
                    '3g': '480p',
                    '4g': '1080p',
                    'wifi': '1080p'
                };
                return qualityMap[connectionType] || '720p';
            }

            // Update reel quality UI for a specific video
            updateReelQualityUI(videoIndex, selectedQuality) {
                const qualityMenu = document.getElementById(`reelQualityMenu${videoIndex}`);
                if (!qualityMenu) return;

                const qualityOptions = qualityMenu.querySelectorAll('.quality-option');

                qualityOptions.forEach(option => {
                    const quality = option.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                    const isActive = quality === selectedQuality;

                    // Remove active classes
                    option.classList.remove('bg-red-600/20', 'text-red-400');
                    option.classList.add('text-white');

                    // Find check icon
                    const checkIcon = option.querySelector('.check-icon');
                    if (checkIcon) {
                        checkIcon.style.opacity = '0';
                    }

                    if (isActive) {
                        // Apply active style
                        option.classList.remove('text-white');
                        option.classList.add('bg-red-600/20', 'text-red-400');

                        // Show check icon
                        if (checkIcon) {
                            checkIcon.style.opacity = '1';
                        }
                    }
                });

                // Update quality button tooltip
                const qualityBtn = document.getElementById(`reelQualityBtn${videoIndex}`);
                if (qualityBtn) {
                    let displayQuality;
                    if (selectedQuality === 'auto') {
                        const recommendedQuality = this.reelRecommendedQualities.get(videoIndex) || '720p';
                        displayQuality = `Auto (${this.getQualityLabel(recommendedQuality)})`;
                    } else {
                        displayQuality = this.getQualityLabel(selectedQuality);
                    }
                    qualityBtn.title = `Qualità video: ${displayQuality}`;
                }
            }

            // Get quality label
            getQualityLabel(quality) {
                const labels = {
                    'auto': 'Auto',
                    '1080p': '1080p Full HD',
                    '720p': '720p HD',
                    '480p': '480p',
                    '360p': '360p',
                    'original': 'Originale'
                };
                return labels[quality] || quality;
            }

            setupKeyboardNavigation() {
                document.addEventListener('keydown', (e) => {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

                    switch (e.code) {
                        case 'ArrowUp':
                            e.preventDefault();
                            navigateReel(-1);
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            navigateReel(1);
                            break;
                        case 'Space':
                            e.preventDefault();
                            this.togglePlayPause();
                            break;
                        case 'KeyM':
                            e.preventDefault();
                            this.toggleMute();
                            break;
                        case 'KeyF':
                            e.preventDefault();
                            window.toggleFullscreen();
                            break;
                    }
                });
            }
        }

        // =================== REEL QUALITY CHANGE FUNCTION ===================

        // Global function to change reel quality - this uses the same method as normal videos
        window.selectReelQuality = (quality, videoIndex) => {
            if (!window.reelManager) {
                console.error('Reel manager not initialized');
                return;
            }

            const reelManager = window.reelManager;
            const currentQuality = reelManager.reelCurrentQualities.get(videoIndex) || 'auto';

            // Se la qualità richiesta è la stessa di quella corrente, non fare nulla
            if (quality === currentQuality) {
                return;
            }

            // Determina la qualità effettiva da usare
            let actualQuality = quality;
            let displayQuality = quality;

            if (quality === 'auto') {
                actualQuality = reelManager.reelRecommendedQualities.get(videoIndex) || '720p';
                displayQuality = `Auto (${reelManager.getQualityLabel(actualQuality)})`;
            } else {
                displayQuality = reelManager.getQualityLabel(quality);
            }

            // Verifica che la qualità sia disponibile
            const videoQualities = reelManager.reelVideoQualities.get(videoIndex);
            const qualityData = videoQualities?.[actualQuality];
            if (!qualityData) {
                reelManager.showToast(`Qualità ${actualQuality} non disponibile`, 'error');
                return;
            }

            // Salva la preferenza dell'utente
            const videoContainer = document.querySelector(`[data-reel-index="${videoIndex}"]`);
            const videoId = videoContainer?.dataset.videoId;
            if (videoId) {
                localStorage.setItem(`reelVideoQuality_${videoId}`, quality);
            }

            // Aggiorna lo stato globale immediatamente
            const previousQuality = currentQuality;
            reelManager.reelCurrentQualities.set(videoIndex, quality);

            // Aggiorna l'UI immediatamente
            reelManager.updateReelQualityUI(videoIndex, quality);

            // Salva lo stato di riproduzione corrente
            const video = reelManager.videoElements.get(videoIndex);
            if (!video) return;

            const currentTime = video.currentTime;
            const wasPlaying = !video.paused;
            const currentVolume = video.volume;

            // Mostra il loading
            const loadingSpinner = document.getElementById(`reelQualityLoading${videoIndex}`);
            if (loadingSpinner) {
                loadingSpinner.classList.remove('opacity-0');
            }

            // Gestione errori
            const handleVideoError = () => {
                reelManager.showToast(`Errore nel caricamento della qualità ${displayQuality}`, 'error');

                // Ripristina la qualità precedente
                reelManager.reelCurrentQualities.set(videoIndex, previousQuality);
                reelManager.updateReelQualityUI(videoIndex, previousQuality);

                if (loadingSpinner) {
                    loadingSpinner.classList.add('opacity-0');
                }
            };

            // Cambia la sorgente del video
            video.removeEventListener('error', handleVideoError);
            video.addEventListener('error', handleVideoError, {
                once: true
            });

            // Cambia la sorgente con un piccolo delay per assicurare la pulizia
            setTimeout(() => {
                let videoUrl = qualityData.url;

                // Normalizza l'URL per assicurare il prefisso /storage/
                try {
                    const rawUrl = videoUrl;
                    if (videoUrl.startsWith('//')) {
                        videoUrl = window.location.protocol + videoUrl;
                    }

                    const isAbsolute = videoUrl.startsWith('http://') || videoUrl.startsWith('https://');

                    if (isAbsolute) {
                        const parsed = new URL(videoUrl);
                        if (parsed.origin === window.location.origin) {
                            if (!parsed.pathname.startsWith('/storage/')) {
                                const cleanedPath = parsed.pathname.replace(/^\/+/, '');
                                parsed.pathname = '/storage/' + cleanedPath;
                            }
                            videoUrl = parsed.toString();
                        } else {
                            videoUrl = parsed.toString();
                        }
                    } else {
                        if (!videoUrl.startsWith('/storage/')) {
                            if (!videoUrl.startsWith('/')) {
                                videoUrl = '/storage/' + videoUrl;
                            } else {
                                videoUrl = '/storage' + videoUrl;
                            }
                        }
                        videoUrl = new URL(videoUrl, window.location.origin).toString();
                    }
                } catch (err) {
                    if (!qualityData.url.startsWith('/storage/') && !qualityData.url.startsWith('http') && !
                        qualityData.url.startsWith('//')) {
                        videoUrl = '/storage/' + qualityData.url.replace(/^\/+/, '');
                    } else {
                        videoUrl = qualityData.url;
                    }
                }

                // Aggiorna la sorgente del video
                const source = video.querySelector('source');
                if (source) {
                    source.src = videoUrl;
                } else {
                    video.src = videoUrl;
                }
                video.load();
            }, 50);

            // Ripristina lo stato di riproduzione quando il video è caricato
            video.addEventListener('loadedmetadata', () => {
                // Ripristina lo stato di riproduzione
                video.currentTime = currentTime;
                video.volume = currentVolume;

                if (wasPlaying) {
                    video.play().catch(e => {
                        // Errore silenzioso durante la riproduzione dopo cambio qualità
                    });
                }

                // Nascondi il loading
                if (loadingSpinner) {
                    loadingSpinner.classList.add('opacity-0');
                }

                // Mostra notifica di successo
                reelManager.showToast(`Qualità cambiata a: ${displayQuality}`, 'success');

                // Notifica anche Livewire per compatibilità
                if (window.Livewire) {
                    window.Livewire.dispatch('changeVideoQuality', {
                        quality: quality
                    });
                }
            }, {
                once: true
            });

            // Hide menu after selection
            const qualityMenu = document.getElementById(`reelQualityMenu${videoIndex}`);
            if (qualityMenu) {
                qualityMenu.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                qualityMenu.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            }
        };

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for Livewire to be available
            const initWhenLivewireReady = () => {
                if (window.Livewire) {
                    console.log('Livewire is available, initializing...');
                    window.reelManager = new ReelScrollManager();

                    // Listen for like-dislike updates from the component
                    window.Livewire.on('videoLiked', (data) => {
                        if (window.reelManager) {
                            window.reelManager.handleLikeDislikeUpdate(data);
                        }
                    });

                    window.Livewire.on('videoDisliked', (data) => {
                        if (window.reelManager) {
                            window.reelManager.handleLikeDislikeUpdate(data);
                        }
                    });

                    window.Livewire.on('videoUpdated', (data) => {
                        if (window.reelManager) {
                            window.reelManager.handleLikeDislikeUpdate(data);
                        }
                    });
                } else {
                    console.log('Livewire not yet available, waiting...');
                    setTimeout(initWhenLivewireReady, 100);
                }
            };

            initWhenLivewireReady();

            // Add CSS for line clamp
            const style = document.createElement('style');
            style.textContent = `
                .line-clamp-3 {
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .scrollbar-hide {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
                .scrollbar-hide::-webkit-scrollbar {
                    display: none;
                }
                .snap-y {
                    scroll-snap-type: y mandatory;
                }
                .snap-start {
                    scroll-snap-align: start;
                }
                .slider {
                    -webkit-appearance: none;
                    width: 100%;
                    height: 4px;
                    border-radius: 2px;
                    outline: none;
                }
                .slider::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background: #ef4444;
                    cursor: pointer;
                }
                .slider::-moz-range-thumb {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background: #ef4444;
                    cursor: pointer;
                    border: none;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</div>
