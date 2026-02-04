<div id="miniPlayer"
    class="fixed bottom-4 right-4 w-80 bg-gradient-to-br from-gray-900/95 to-black/95 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl z-50 transform translate-y-full opacity-0 transition-all duration-300 ease-out"
    style="{{ $isVisible ? 'display: block; transform: translateY(0); opacity: 1;' : 'display: none;' }}"
    wire:loading.class="pointer-events-none">

    <!-- Header del MiniPlayer -->
    <div
        class="flex items-center justify-between p-3 border-b border-gray-700/50 bg-gradient-to-r from-gray-800/50 to-transparent">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <!-- Thumbnail Video -->
            <div
                class="w-12 h-8 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg overflow-hidden flex-shrink-0 border border-gray-600/50">
                @if ($currentVideo && $currentVideo->thumbnail_url)
                    <img src="{{ $currentVideo->thumbnail_url }}" alt="Video thumbnail"
                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                        onerror="this.src='{{ asset('images/placeholder-video.jpg') }}'">
                @else
                    <div
                        class="w-full h-full bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center">
                        <i class="fas fa-video text-white/50 text-2xl"></i>
                    </div>
                @endif
            </div>

            <!-- Info Video -->
            <div class="flex-1 min-w-0">
                <h4 class="text-white text-sm font-medium line-clamp-2 leading-tight hover:text-red-400 transition-colors cursor-pointer"
                    title="{{ $currentVideo?->title }}"
                    wire:click="$dispatch('navigate', '{{ $currentVideo ? route('videos.show', $currentVideo) : '#' }}')">
                    {{ $currentVideo?->title ?? 'Mini Player' }}
                </h4>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-gray-400 text-xs line-clamp-1">
                        {{ $currentVideo?->user->userProfile?->channel_name ?: $currentVideo?->user->name ?? 'Caricamento...' }}
                    </p>
                    @if ($currentVideo)
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse" title="In riproduzione"></span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pulsanti Header -->
        <div class="flex items-center gap-1 ml-2">

            <!-- Speed Button -->
            <div class="relative">
                <button onclick="toggleSpeedMenu()"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200 group"
                    title="Velocità riproduzione">
                    <i class="fas fa-tachometer-alt text-xs"></i>
                </button>

                <div id="speedMenu" class="hidden absolute bottom-10 right-0 w-32 bg-black/95 backdrop-blur-xl border border-gray-600/50 rounded-lg py-2 shadow-xl z-50">
                    <div class="px-3 py-2 border-b border-gray-600/30">
                        <p class="text-white text-xs font-medium">Velocità</p>
                    </div>
                    <div class="py-1">
                        @foreach (['0.5', '0.75', '1', '1.25', '1.5', '1.75', '2'] as $rate)
                            <button wire:click="changePlaybackRate('{{ $rate }}')"
                                onclick="closeSpeedMenu()"
                                class="w-full text-left px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 text-sm transition-colors duration-150 {{ $playbackRate === $rate ? 'bg-blue-600/20 border-l-2 border-blue-500' : '' }}">
                                {{ $rate }}x
                                @if ($playbackRate === $rate)
                                    <i class="fas fa-check text-blue-400 text-xs float-right"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quality Button -->
            <div class="relative">
                <button onclick="toggleQualityMenu()"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200 group"
                    title="Qualità video">
                    <i class="fas fa-cog text-xs"></i>
                </button>

                <div id="qualityMenu" class="hidden absolute bottom-10 right-0 w-32 bg-black/95 backdrop-blur-xl border border-gray-600/50 rounded-lg py-2 shadow-xl z-50">
                    <div class="px-3 py-2 border-b border-gray-600/30">
                        <p class="text-white text-xs font-medium">Qualità</p>
                    </div>
                    <div class="py-1">
                        @foreach (['auto', '144p', '240p', '360p', '480p', '720p', '1080p'] as $quality)
                            <button wire:click="changeQuality('{{ $quality }}')"
                                onclick="closeQualityMenu()"
                                class="w-full text-left px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 text-sm transition-colors duration-150 {{ $currentQuality === $quality ? 'bg-blue-600/20 border-l-2 border-blue-500' : '' }}">
                                {{ $quality }}
                                @if ($currentQuality === $quality)
                                    <i class="fas fa-check text-blue-400 text-xs float-right"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Autoplay Toggle -->
            <button wire:click="toggleAutoplay"
                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200 group"
                title="Autoplay">
                <i class="fas fa-play text-xs {{ $autoplay ? 'text-green-400' : '' }}"></i>
                <div
                    class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full opacity-{{ $autoplay ? '100' : '0' }} transition-opacity duration-200">
                </div>
            </button>

            <!-- Espandi -->
            <button wire:click="toggleExpand"
                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200 group"
                title="{{ $isExpanded ? 'Riduci' : 'Espandi' }}">
                <i class="fas {{ $isExpanded ? 'fa-compress-alt' : 'fa-expand-alt' }} text-xs"></i>
            </button>

            <!-- Chiudi -->
            <button wire:click="closeMiniPlayer"
                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-red-600/20 rounded-lg transition-all duration-200 group"
                title="Chiudi miniplayer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Area Video -->
    <div class="relative bg-black overflow-hidden">
        <video id="miniPlayerVideo" class="w-full h-44 object-cover rounded-b-xl" preload="metadata"
            {{ $currentVideo ? 'src="' . ($currentVideo->video_file_url ? asset('storage/' . $currentVideo->video_file_url) : '') . '"' : '' }}
            poster="{{ $currentVideo && $currentVideo->thumbnail_url ? asset('storage/' . $currentVideo->thumbnail_url) : '' }}">
        </video>

        <!-- Overlay Play/Pause -->
        <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 hover:opacity-100 transition-opacity duration-200 cursor-pointer"
            wire:click="{{ $isPlaying ? 'pauseVideo' : 'resumeVideo' }}">
            <div
                class="w-12 h-12 bg-black/70 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                <i class="fas {{ $isPlaying ? 'fa-pause' : 'fa-play' }} text-white text-lg"></i>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0"
            wire:loading.class.remove="opacity-0" wire:loading.class.add="opacity-100">
            <div class="w-8 h-8 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        </div>

        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-transparent to-transparent p-3">
            <div class="relative group/progress">
                <div class="w-full h-1.5 bg-white/30 rounded-full cursor-pointer"
                    onclick="seekToPosition(event, {{ $duration }})">
                    <div class="absolute h-1.5 bg-red-600 rounded-full left-0 top-0"
                        style="width: {{ $this->getProgressPercentage() }}%"></div>
                    <div class="absolute h-1.5 bg-red-400/50 rounded-full left-0 top-0 opacity-0 group-hover/progress:opacity-100"
                        style="width: {{ $this->getProgressPercentage() }}%"></div>
                    <div class="absolute w-3 h-3 bg-red-600 rounded-full -top-0.5 opacity-0 group-hover/progress:opacity-100 transition-opacity duration-200 shadow-lg transform scale-0 group-hover/progress:scale-100"
                        style="left: calc({{ $this->getProgressPercentage() }}% - 6px)"></div>
                </div>
                <div id="progressTooltip" class="absolute bottom-3 bg-black/90 text-white text-xs py-1 px-2 rounded opacity-0 transition-all duration-200 font-medium">
                    {{ $this->formatTime($currentTime) }} / {{ $this->formatTime($duration) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Controlli -->
    <div class="p-3 space-y-3">
        <!-- Controlli Principali -->
        <div class="flex items-center justify-between">
            <!-- Controlli Sinistra -->
            <div class="flex items-center gap-2">
                <!-- Play/Pause -->
                <button wire:click="{{ $isPlaying ? 'pauseVideo' : 'resumeVideo' }}"
                    class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 rounded-full transition-all duration-200 group border border-gray-600/50">
                    <i class="fas {{ $isPlaying ? 'fa-pause' : 'fa-play' }} text-white text-sm"></i>
                </button>

                <!-- Skip Backward -->
                <button wire:click="skipTime(-10)"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200"
                    title="Indietro 10 secondi">
                    <i class="fas fa-undo text-xs"></i>
                </button>

                <!-- Skip Forward -->
                <button wire:click="skipTime(10)"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200"
                    title="Avanti 10 secondi">
                    <i class="fas fa-redo text-xs"></i>
                </button>

                <!-- Volume -->
                <div class="relative">
                    <button onclick="toggleVolumeMenu()" wire:click="toggleMute"
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200 group"
                        title="Volume">
                        <i class="fas {{ $this->getVolumeIcon() }} text-xs"></i>
                    </button>

                    <div id="volumeMenu" class="hidden absolute bottom-10 right-0 w-24 bg-black/95 backdrop-blur-xl border border-gray-600/50 rounded-lg py-2 shadow-xl z-50">
                        <div class="px-2 py-1">
                            <input type="range" min="0" max="1" step="0.01"
                                value="{{ $volume }}" wire:change="changeVolume($event.target.value)"
                                class="w-full h-1 bg-white/30 rounded-full appearance-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer">
                        </div>
                        <div class="px-2 py-1 border-t border-gray-600/30">
                            <div class="text-white text-xs text-center">{{ round($volume * 100) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controlli Destra -->
            <div class="flex items-center gap-2">
                <!-- Tempo Corrente -->
                <div class="flex items-center gap-1 text-xs font-medium text-gray-300">
                    <span>{{ $this->formatTime($currentTime) }}</span>
                    <span>/</span>
                    <span>{{ $this->formatTime($duration) }}</span>
                </div>

                <!-- Picture in Picture -->
                <button wire:click="$dispatch('enterPictureInPicture')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200"
                    title="Picture in Picture">
                    <i class="fas fa-clone text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Miniplayer Overlay Espanso -->
    <div id="miniPlayerExpandOverlay"
        class="fixed inset-0 bg-black/80 z-40 opacity-0 pointer-events-none transition-all duration-300"
        style="{{ $isExpanded ? 'display: block; opacity: 1;' : 'display: none;' }}" wire:click="toggleExpand">

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-4xl mx-auto">
                <div
                    class="bg-gradient-to-br from-gray-900 to-black rounded-xl overflow-hidden shadow-2xl border border-gray-700/50">
                    <!-- Header Espanso -->
                    <div
                        class="flex items-center justify-between p-4 border-b border-gray-700/50 bg-gradient-to-r from-gray-800/50 to-transparent">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div
                                class="w-16 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg overflow-hidden border border-gray-600/50">
                                @if ($currentVideo && $currentVideo->thumbnail_url)
                                    <img src="{{ $currentVideo->thumbnail_url }}" alt="Video thumbnail"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center">
                                        <i class="fas fa-video text-white/50 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white text-lg font-semibold line-clamp-1">
                                    {{ $currentVideo?->title ?? 'Mini Player Espanso' }}
                                </h3>
                                <p class="text-gray-400 text-sm">
                                    {{ $currentVideo?->user->userProfile?->channel_name ?: $currentVideo?->user->name ?? 'Caricamento...' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="toggleExpand"
                            class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200">
                            <i class="fas fa-compress-alt"></i>
                        </button>
                    </div>

                    <!-- Video Espanso -->
                    <div class="relative bg-black">
                        <video id="expandPlayerVideo" class="w-full aspect-video" preload="metadata"
                            {{ $currentVideo ? 'src="' . ($currentVideo->video_file_url ? asset('storage/' . $currentVideo->video_file_url) : '') . '"' : '' }}
                            poster="{{ $currentVideo && $currentVideo->thumbnail_url ? asset('storage/' . $currentVideo->thumbnail_url) : '' }}">
                        </video>
                    </div>

                    <!-- Controlli Espansi -->
                    <div class="p-4 space-y-4">
                        <div class="relative">
                            <div class="w-full h-2 bg-white/30 rounded-full cursor-pointer"
                                onclick="seekToPosition(event, {{ $duration }})">
                                <div class="absolute h-2 bg-red-600 rounded-full left-0 top-0"
                                    style="width: {{ $this->getProgressPercentage() }}%"></div>
                                <div class="absolute h-2 bg-red-400/50 rounded-full left-0 top-0 opacity-0 group-hover:opacity-100"
                                    style="width: {{ $this->getProgressPercentage() }}%"></div>
                                <div class="absolute w-4 h-4 bg-red-600 rounded-full -top-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg transform scale-0 group-hover:scale-100"
                                    style="left: calc({{ $this->getProgressPercentage() }}% - 8px)"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button wire:click="{{ $isPlaying ? 'pauseVideo' : 'resumeVideo' }}"
                                    class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 rounded-full transition-all duration-200 group border border-gray-600/50">
                                    <i
                                        class="fas {{ $isPlaying ? 'fa-pause' : 'fa-play' }} text-white text-xl mx-auto"></i>
                                </button>
                                <button wire:click="skipTime(-10)"
                                    class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200"
                                    title="Indietro 10 secondi">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button wire:click="skipTime(10)"
                                    class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200"
                                    title="Avanti 10 secondi">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <div class="flex items-center gap-2 ml-4">
                                    <button wire:click="toggleMute"
                                        class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-full transition-all duration-200">
                                        <i class="fas {{ $this->getVolumeIcon() }}"></i>
                                    </button>
                                    <input type="range" min="0" max="1" step="0.01"
                                        value="{{ $volume }}" wire:change="changeVolume($event.target.value)"
                                        class="w-32 h-1 bg-white/30 rounded-full appearance-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-gray-300">
                                    <span>{{ $this->formatTime($currentTime) }}</span>
                                    <span>/</span>
                                    <span>{{ $this->formatTime($duration) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // MiniPlayer System - Sistema avanzato per la gestione del miniplayer
            window.MiniPlayerSystem = {
                isInitialized: false,
                videoElement: null,
                expandVideoElement: null,

                init() {
                    if (this.isInitialized) return;

                    this.videoElement = document.getElementById('miniPlayerVideo');
                    this.expandVideoElement = document.getElementById('expandPlayerVideo');

                    // Gestisci eventi video principali
                    this.setupVideoEvents(this.videoElement);
                    this.setupVideoEvents(this.expandVideoElement);

                    // Gestisci Picture in Picture
                    this.setupPictureInPicture();

                    // Gestisci drag and drop
                    this.setupDragAndDrop();

                    // Gestisci menu
                    this.setupMenus();

                    // Imposta proprietà video
                    this.setupVideoProperties();

                    // Carica posizione salvata
                    this.loadPosition();

                    this.isInitialized = true;
                },

                setupVideoEvents(video) {
                    if (!video) return;

                    // Rimuovi eventuali listener precedenti
                    video.removeEventListener('timeupdate', this.timeUpdateHandler);
                    video.removeEventListener('loadedmetadata', this.loadedMetadataHandler);
                    video.removeEventListener('play', this.playHandler);
                    video.removeEventListener('pause', this.pauseHandler);
                    video.removeEventListener('ended', this.endedHandler);

                    // Definisci handler
                    this.timeUpdateHandler = (e) => {
                        @this.updateTime(video.currentTime);
                    };

                    this.loadedMetadataHandler = (e) => {
                        @this.duration = video.duration;
                    };

                    this.playHandler = () => {
                        @this.isPlaying = true;
                    };

                    this.pauseHandler = () => {
                        @this.isPlaying = false;
                    };

                    this.endedHandler = () => {
                        @this.handleVideoEnded();
                    };

                    // Aggiungi listener
                    video.addEventListener('timeupdate', this.timeUpdateHandler);
                    video.addEventListener('loadedmetadata', this.loadedMetadataHandler);
                    video.addEventListener('play', this.playHandler);
                    video.addEventListener('pause', this.pauseHandler);
                    video.addEventListener('ended', this.endedHandler);
                },

                setupPictureInPicture() {
                    document.addEventListener('enterPictureInPicture', async () => {
                        try {
                            if (this.videoElement && this.videoElement.readyState >= 1) {
                                await this.videoElement.requestPictureInPicture();
                            }
                        } catch (error) {
                            console.log('Picture in Picture non supportato o disabilitato');
                        }
                    });
                },


                savePosition() {
                    const miniPlayer = document.getElementById('miniPlayer');
                    if (!miniPlayer) return;

                    const rect = miniPlayer.getBoundingClientRect();
                    const position = {
                        bottom: window.innerHeight - rect.bottom,
                        right: window.innerWidth - rect.right
                    };

                    localStorage.setItem('miniPlayerPosition', JSON.stringify(position));
                },

                loadPosition() {
                    const savedPos = localStorage.getItem('miniPlayerPosition');
                    if (!savedPos) return;

                    const miniPlayer = document.getElementById('miniPlayer');
                    if (!miniPlayer) return;

                    try {
                        const pos = JSON.parse(savedPos);
                        miniPlayer.style.bottom = pos.bottom + 'px';
                        miniPlayer.style.right = pos.right + 'px';
                    } catch (e) {
                        console.log('Errore nel caricamento della posizione del miniplayer');
                    }
                },

                syncVideos() {
                    if (!this.videoElement || !this.expandVideoElement) return;

                    // Sincronizza tempo
                    this.expandVideoElement.currentTime = this.videoElement.currentTime;

                    // Sincronizza stato riproduzione
                    if (this.videoElement.paused) {
                        this.expandVideoElement.pause();
                    } else {
                        this.expandVideoElement.play().catch(e => {
                            console.log('Errore sincronizzazione video espanso:', e);
                        });
                    }

                    // Sincronizza altre proprietà
                    this.expandVideoElement.volume = this.videoElement.volume;
                    this.expandVideoElement.muted = this.videoElement.muted;
                    this.expandVideoElement.playbackRate = this.videoElement.playbackRate;
                },

                setupMenus() {
                    // Chiudi menu quando si clicca fuori
                    document.addEventListener('click', (e) => {
                        const speedMenu = document.getElementById('speedMenu');
                        const qualityMenu = document.getElementById('qualityMenu');
                        const volumeMenu = document.getElementById('volumeMenu');

                        if (speedMenu && !speedMenu.contains(e.target) && !e.target.closest('[onclick*="toggleSpeedMenu"]')) {
                            speedMenu.classList.add('hidden');
                        }
                        if (qualityMenu && !qualityMenu.contains(e.target) && !e.target.closest('[onclick*="toggleQualityMenu"]')) {
                            qualityMenu.classList.add('hidden');
                        }
                        if (volumeMenu && !volumeMenu.contains(e.target) && !e.target.closest('[onclick*="toggleVolumeMenu"]')) {
                            volumeMenu.classList.add('hidden');
                        }
                    });
                },

                setupVideoProperties() {
                    if (!this.videoElement) return;

                    // Imposta proprietà iniziali del video
                    this.videoElement.volume = {{ $volume }};
                    this.videoElement.muted = {{ $muted ? 'true' : 'false' }};
                    this.videoElement.playbackRate = {{ $playbackRate }};
                },

                setupDragAndDrop() {
                    const miniPlayer = document.getElementById('miniPlayer');
                    if (!miniPlayer) return;

                    let isDragging = false;
                    let dragStartX = 0;
                    let dragStartY = 0;
                    let currentX = 0;
                    let currentY = 0;

                    // Carica posizione salvata
                    this.loadPosition();

                    let clickCount = 0;
                    let clickTimer = null;

                    miniPlayer.addEventListener('mousedown', (e) => {
                        clickCount++;

                        if (clickTimer) clearTimeout(clickTimer);

                        clickTimer = setTimeout(() => {
                            if (clickCount === 1) {
                                // Single click - start dragging
                                if (e.target.closest('button') || e.target.closest('input') || e.target.closest('[onclick]')) return;

                                isDragging = true;
                                dragStartX = e.clientX - currentX;
                                dragStartY = e.clientY - currentY;
                                miniPlayer.style.cursor = 'grabbing';
                            }
                            clickCount = 0;
                        }, 300);
                    });

                    miniPlayer.addEventListener('dblclick', (e) => {
                        if (!e.target.closest('button') && !e.target.closest('input') && !e.target.closest('[onclick]')) {
                            clearTimeout(clickTimer);
                            clickCount = 0;
                            @this.toggleExpand();
                        }
                    });

                    document.addEventListener('mousemove', (e) => {
                        if (!isDragging) return;

                        currentX = e.clientX - dragStartX;
                        currentY = e.clientY - dragStartY;

                        const maxX = window.innerWidth - miniPlayer.offsetWidth;
                        const maxY = window.innerHeight - miniPlayer.offsetHeight;

                        currentX = Math.max(0, Math.min(currentX, maxX));
                        currentY = Math.max(0, Math.min(currentY, maxY));

                        miniPlayer.style.bottom = (window.innerHeight - currentY - miniPlayer.offsetHeight) + 'px';
                        miniPlayer.style.right = (window.innerWidth - currentX - miniPlayer.offsetWidth) + 'px';
                    });

                    document.addEventListener('mouseup', () => {
                        if (isDragging) {
                            isDragging = false;
                            miniPlayer.style.cursor = '';
                            this.savePosition();
                        }
                    });
                },

                startVideoSession(videoData) {
                    @this.startVideoSession(videoData);
                },

                stopVideoSession() {
                    @this.stopVideoSession();
                }
            };

            // Inizializza il sistema quando Livewire è pronto
            window.MiniPlayerSystem.init();

            // Funzioni globali per i menu
            window.toggleSpeedMenu = function() {
                const menu = document.getElementById('speedMenu');
                const isHidden = menu.classList.contains('hidden');
                document.getElementById('qualityMenu')?.classList.add('hidden');
                document.getElementById('volumeMenu')?.classList.add('hidden');
                menu.classList.toggle('hidden', !isHidden);
            };

            window.closeSpeedMenu = function() {
                document.getElementById('speedMenu')?.classList.add('hidden');
            };

            window.toggleQualityMenu = function() {
                const menu = document.getElementById('qualityMenu');
                const isHidden = menu.classList.contains('hidden');
                document.getElementById('speedMenu')?.classList.add('hidden');
                document.getElementById('volumeMenu')?.classList.add('hidden');
                menu.classList.toggle('hidden', !isHidden);
            };

            window.closeQualityMenu = function() {
                document.getElementById('qualityMenu')?.classList.add('hidden');
            };

            window.toggleVolumeMenu = function() {
                const menu = document.getElementById('volumeMenu');
                const isHidden = menu.classList.contains('hidden');
                document.getElementById('speedMenu')?.classList.add('hidden');
                document.getElementById('qualityMenu')?.classList.add('hidden');
                menu.classList.toggle('hidden', !isHidden);
            };

            window.seekToPosition = function(event, duration) {
                const rect = event.target.getBoundingClientRect();
                const percent = (event.clientX - rect.left) / rect.width;
                const time = percent * duration;
                @this.seekTo(time);
            };

            // Gestisci eventi Livewire
            @this.on('miniPlayerStarted', (event) => {
                const data = event.detail;
                if (data.video && data.startTime !== undefined && window.MiniPlayerSystem.videoElement) {
                    const video = window.MiniPlayerSystem.videoElement;

                    // Funzione per avviare la riproduzione quando il video è pronto
                    const tryPlay = () => {
                        if (video.readyState >= 2) { // HAVE_CURRENT_DATA or higher
                            video.currentTime = data.startTime;
                            video.play().catch(e => {
                                console.log('Errore riproduzione miniplayer:', e);
                                // Riprova dopo un breve delay se autoplay è bloccato
                                setTimeout(() => {
                                    video.play().catch(e2 => {
                                        console.log(
                                            'Riproduzione ancora bloccata:',
                                            e2);
                                    });
                                }, 1000);
                            });
                        } else {
                            // Aspetta che il video sia pronto
                            setTimeout(tryPlay, 100);
                        }
                    };

                    tryPlay();
                }
            });

            @this.on('qualityChanged', (event) => {
                const data = event.detail;
                if (window.MiniPlayerSystem.videoElement) {
                    window.MiniPlayerSystem.videoElement.src = data.videoUrl;
                    window.MiniPlayerSystem.videoElement.load();
                    window.MiniPlayerSystem.videoElement.play().catch(e => {
                        console.log('Errore cambio qualità:', e);
                    });
                }
            });

            @this.on('playbackRateChanged', (event) => {
                const rate = event.detail.rate;
                if (window.MiniPlayerSystem.videoElement) {
                    window.MiniPlayerSystem.videoElement.playbackRate = parseFloat(rate);
                }
                if (window.MiniPlayerSystem.expandVideoElement) {
                    window.MiniPlayerSystem.expandVideoElement.playbackRate = parseFloat(rate);
                }
            });

            @this.on('volumeChanged', (event) => {
                const data = event.detail;
                if (window.MiniPlayerSystem.videoElement) {
                    window.MiniPlayerSystem.videoElement.volume = data.volume;
                    window.MiniPlayerSystem.videoElement.muted = data.muted;
                }
                if (window.MiniPlayerSystem.expandVideoElement) {
                    window.MiniPlayerSystem.expandVideoElement.volume = data.volume;
                    window.MiniPlayerSystem.expandVideoElement.muted = data.muted;
                }
            });

            @this.on('timeSeeked', (event) => {
                const time = event.detail.time;
                if (window.MiniPlayerSystem.videoElement) {
                    window.MiniPlayerSystem.videoElement.currentTime = time;
                }
                if (window.MiniPlayerSystem.expandVideoElement) {
                    window.MiniPlayerSystem.expandVideoElement.currentTime = time;
                }
            });

            @this.on('miniPlayerToggled', (event) => {
                const expanded = event.detail.expanded;
                if (expanded && window.MiniPlayerSystem) {
                    // Quando si espande, sincronizza i video
                    setTimeout(() => {
                        window.MiniPlayerSystem.syncVideos();
                    }, 100);
                }
            });

            // Gestisci il click sul titolo per navigare al video
            @this.on('navigate', (event) => {
                const url = event.detail;
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush
