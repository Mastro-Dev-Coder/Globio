@php
    use App\Models\WatchLater;
    use App\Models\Video;
    $currentReel = $reels[$currentIndex] ?? null;
@endphp

<div class="min-h-screen bg-gray-900">
    <!-- Layout YouTube Style - 3 Columns -->
    <div class="flex h-screen max-h-screen">
        <!-- Left Sidebar: Channel Info & Description -->
        <div class="hidden lg:flex w-80 flex-col border-r border-gray-800 bg-gray-900">
            <!-- Header -->
            <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                <button onclick="history.back()" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-white text-sm"></i>
                </button>
                <h2 class="text-white font-medium">Reel</h2>
                <div class="w-8"></div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4">
                @foreach ($reels as $index => $reel)
                    <div class="reel-info-panel {{ $index === $currentIndex ? 'block' : 'hidden' }}"
                        data-reel-index="{{ $index }}" data-video-id="{{ $reel['id'] }}">

                        <!-- Channel Info -->
                        <div class="flex items-center gap-3 mb-4">
                            @if ($reel['user']['avatar_url'])
                                <a href="{{ route('channel.show', $reel['user']['channel_name']) }}" class="block">
                                    <img src="{{ asset('storage/' . $reel['user']['avatar_url']) }}"
                                        alt="{{ $reel['user']['channel_name'] }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-700">
                                </a>
                            @else
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center border-2 border-gray-700">
                                    <span
                                        class="text-white font-bold text-sm">{{ strtoupper(substr($reel['user']['name'], 0, 1)) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('channel.show', $reel['user']['channel_name']) }}">
                                    <h3 class="text-white font-medium text-sm truncate">
                                        {{ $reel['user']['channel_name'] ?: $reel['user']['name'] }}</h3>
                                </a>
                                <p class="text-gray-400 text-xs">{{ number_format($reel['user']['subscribers'] ?? 0) }}
                                    iscritti</p>
                            </div>
                        </div>

                        <!-- Subscribe Button - Livewire -->
                        <div class="mb-4">
                            @php $reelVideo = App\Models\Video::find($reel['id']); @endphp
                            @if ($reelVideo)
                                <livewire:video-subscribe :video="$reelVideo" :key="'subscribe-' . $reel['id']" />
                            @endif
                        </div>

                        <!-- Title -->
                        <h2 class="text-white font-medium text-base mb-3 leading-tight">{{ $reel['title'] }}</h2>

                        <!-- Stats -->
                        <div class="text-gray-400 text-sm mb-4">
                            <span>{{ $this->formatCount($reel['views_count']) }} visualizzazioni</span>
                            <span class="mx-2">-</span>
                            <span>{{ \Carbon\Carbon::parse($reel['created_at'])->diffForHumans() }}</span>
                        </div>

                        <!-- Description -->
                        @if ($reel['description'])
                            <p class="text-gray-300 text-sm leading-relaxed mb-4">{{ $reel['description'] }}</p>
                        @endif

                        <!-- Tags -->
                        @if (isset($reel['tags']) && count($reel['tags']) > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach ($reel['tags'] as $tag)
                                    <span class="text-blue-400 text-xs">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Center: Video Reel -->
        <div
            class="flex-1 flex items-center justify-center relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
            <!-- Scroll Container -->
            <div class="w-full h-screen overflow-y-auto snap-y snap-mandatory scrollbar-hide" id="reelsScrollContainer"
                style="scroll-behavior: smooth;">

                @foreach ($reels as $index => $reel)
                    <div class="snap-start h-full flex items-center justify-center min-h-screen max-h-screen"
                        data-reel-index="{{ $index }}" data-video-id="{{ $reel['id'] }}"
                        id="reelContainer{{ $index }}">

                        <!-- Video Container - Large and Rounded -->
                        <div class="relative w-full max-w-[500px] aspect-[9/16] rounded-3xl overflow-hidden shadow-2xl cursor-pointer ring-4 ring-gray-800/50"
                            id="videoContainer{{ $index }}">

                            <!-- Video Element -->
                            <video class="w-full h-full object-cover" @if ($isMuted) muted @endif
                                loop playsinline preload="metadata" id="videoElement{{ $index }}">
                                <source src="{{ asset('storage/' . $reel['video_path']) }}" type="video/mp4">
                            </video>

                            <!-- Gradient Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/50 pointer-events-none">
                            </div>

                            <!-- Progress Bar -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-white/20 z-10">
                                <div class="h-full bg-red-600 transition-all duration-100"
                                    id="progressBar{{ $index }}" style="width: 0%"></div>
                            </div>

                            <!-- Loading -->
                            <div id="reelQualityLoading{{ $index }}"
                                class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 pointer-events-none z-20">
                                <div
                                    class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin">
                                </div>
                            </div>

                            <!-- Play/Pause Button -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-5">
                                <div class="w-20 h-20 rounded-full bg-black/40 backdrop-blur-sm flex items-center justify-center opacity-0 transition-opacity duration-200"
                                    id="playPauseBtnCenter{{ $index }}">
                                    <i class="fas fa-play text-white text-3xl ml-1"
                                        id="playPauseIconCenter{{ $index }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Sidebar: Actions -->
        <div
            class="hidden md:flex w-24 flex-col items-center justify-center border-l border-gray-800 bg-gray-900/95 backdrop-blur-sm p-4 gap-6">
            @if (isset($reels[$currentIndex]))
                @php $currentReel = $reels[$currentIndex]; @endphp

                <!-- Like/Dislike -->
                <livewire:reel-like-dislike :video="App\Models\Video::find($currentReel['id'])" :key="'like-dislike-' . $currentReel['id'] . '-' . $currentIndex" />

                <!-- Comments -->
                <button wire:click="toggleComments({{ $currentReel['id'] }})"
                    class="flex flex-col items-center gap-1 group">
                    <div
                        class="w-14 h-14 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center hover:bg-gray-700 hover:scale-110 transition-all duration-300 border border-gray-700/50 group-hover:border-blue-500/50">
                        <i class="fas fa-comment text-white text-lg group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <span
                        class="text-gray-400 text-xs group-hover:text-white transition-colors">{{ $this->formatCount($currentReel['comments_count']) }}</span>
                </button>

                <!-- Share -->
                <button wire:click="shareVideo" class="flex flex-col items-center gap-1 group">
                    <div
                        class="w-14 h-14 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center hover:bg-gray-700 hover:scale-110 transition-all duration-300 border border-gray-700/50 group-hover:border-blue-500/50">
                        <i class="fas fa-share text-white text-lg group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <span class="text-gray-400 text-xs group-hover:text-white transition-colors">Condividi</span>
                </button>

                <!-- Save/Watch Later - Improved UI -->
                <div class="relative group">
                    <button wire:click="toggleWatchLater" class="flex flex-col items-center gap-1 w-full">
                        <div
                            class="w-14 h-14 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center hover:bg-gray-700 hover:scale-110 transition-all duration-300 border border-gray-700/50 group-hover:border-green-500/50 {{ $currentReel['is_in_watch_later'] ? 'bg-green-500/20 border-green-500/50' : '' }}">
                            <i
                                class="fas fa-bookmark text-xl @if ($currentReel['is_in_watch_later']) text-green-400 drop-shadow-lg @else text-white group-hover:text-green-300 @endif"></i>
                        </div>
                        <span class="text-gray-400 text-xs group-hover:text-green-400 transition-colors">
                            {{ $currentReel['is_in_watch_later'] ? 'Salvato' : 'Salva' }}
                        </span>
                    </button>
                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                        <div class="bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap shadow-lg">
                            {{ $currentReel['is_in_watch_later'] ? 'Rimuovi da Guarda più tardi' : 'Aggiungi a Guarda più tardi' }}
                        </div>
                    </div>
                </div>

                <!-- More Options -->
                <div class="relative group">
                    <button class="flex flex-col items-center gap-1" onclick="window.toggleReelMenu()">
                        <div
                            class="w-14 h-14 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center hover:bg-gray-700 hover:scale-110 transition-all duration-300 border border-gray-700/50 group-hover:border-blue-500/50">
                            <i class="fas fa-ellipsis-h text-white text-xl"></i>
                        </div>
                        <span class="text-gray-400 text-xs group-hover:text-blue-400 transition-colors">Altro</span>
                    </button>

                    <!-- Hover Menu -->
                    <div
                        class="absolute right-full mr-3 top-0 bg-gray-800/95 backdrop-blur-xl rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 min-w-48 py-2 z-40 border border-gray-700/50">
                        <!-- Speed -->
                        <button onclick="window.openSpeedMenu()"
                            class="w-full px-4 py-2.5 text-left text-white hover:bg-gray-700/80 transition-colors flex items-center gap-3 text-sm">
                            <i class="fas fa-tachometer-alt text-orange-400 w-5"></i>
                            <span>Velocità</span>
                        </button>

                        <!-- Loop -->
                        <button onclick="window.toggleLoop()"
                            class="w-full px-4 py-2.5 text-left text-white hover:bg-gray-700/80 transition-colors flex items-center gap-3 text-sm">
                            <i class="fas fa-redo text-green-400 w-5"></i>
                            <span>Loop</span>
                        </button>

                        <div class="border-t border-gray-700/50 my-1"></div>

                        <!-- Go to Channel -->
                        <button onclick="window.goToChannel()"
                            class="w-full px-4 py-2.5 text-left text-white hover:bg-gray-700/80 transition-colors flex items-center gap-3 text-sm">
                            <i class="fas fa-user-circle text-blue-400 w-5"></i>
                            <span>Vai al canale</span>
                        </button>

                        <div class="border-t border-gray-700/50 my-1"></div>

                        <!-- Report -->
                        <button onclick="window.reportVideo()"
                            class="w-full px-4 py-2.5 text-left text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-3 text-sm">
                            <i class="fas fa-flag w-5"></i>
                            <span>Segnala</span>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex flex-col items-center gap-2 mt-4 pt-4 border-t border-gray-800">
                    <button wire:click="previousVideo"
                        class="p-3 rounded-full hover:bg-gray-800 transition-all duration-200 hover:scale-110 {{ $currentIndex === 0 ? 'opacity-30' : '' }}"
                        {{ $currentIndex === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-up text-white text-lg"></i>
                    </button>
                    <span
                        class="text-white text-sm font-medium bg-gray-800/50 px-3 py-1 rounded-full">{{ $currentIndex + 1 }}
                        / {{ count($reels) }}</span>
                    <button wire:click="nextVideo"
                        class="p-3 rounded-full hover:bg-gray-800 transition-all duration-200 hover:scale-110 {{ $currentIndex >= count($reels) - 1 ? 'opacity-30' : '' }}"
                        {{ $currentIndex >= count($reels) - 1 ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-down text-white text-lg"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 bg-gray-900/95 backdrop-blur-lg border-t border-gray-800 p-4 z-50">
        <div class="flex items-center justify-around">
            <button wire:click="previousVideo"
                class="p-4 rounded-full hover:bg-gray-800 transition-all duration-200 hover:scale-110 {{ $currentIndex === 0 ? 'opacity-30' : '' }}"
                {{ $currentIndex === 0 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-up text-white text-xl"></i>
            </button>
            <span class="text-white font-medium bg-gray-800/50 px-4 py-2 rounded-full">{{ $currentIndex + 1 }} /
                {{ count($reels) }}</span>
            <button wire:click="nextVideo"
                class="p-4 rounded-full hover:bg-gray-800 transition-all duration-200 hover:scale-110 {{ $currentIndex >= count($reels) - 1 ? 'opacity-30' : '' }}"
                {{ $currentIndex >= count($reels) - 1 ? 'disabled' : '' }}>
                <i class="fas fa-chevron-down text-white text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Quality Menu -->
    @foreach ($reels as $index => $reel)
        <div id="reelQualityMenu{{ $index }}" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/70" onclick="window.toggleReelQualityMenu({{ $index }})">
            </div>
            <div
                class="absolute bottom-0 left-0 right-0 bg-gray-900 rounded-t-3xl p-4 animate-slide-up z-50 max-h-[70vh] overflow-y-auto">
                <div class="w-16 h-1.5 bg-gray-600 rounded-full mx-auto mb-4"></div>
                <h3 class="text-white font-semibold text-lg mb-4">Qualità video</h3>

                @php
                    $reelVideo = \App\Models\Video::find($reel['id']);
                    $availableQualities = $reelVideo ? $reelVideo->getAvailableQualities() : [];
                    $qualityLabels = [
                        'auto' => 'Auto',
                        'original' => 'Originale',
                        '2160p' => '2160p 4K',
                        '1440p' => '1440p 2K',
                        '1080p' => '1080p Full HD',
                        '720p' => '720p HD',
                        '480p' => '480p',
                        '360p' => '360p',
                    ];
                @endphp

                <div class="space-y-2">
                    <button
                        class="quality-option w-full text-left px-4 py-3 rounded-xl hover:bg-gray-800 transition-all duration-150 flex items-center gap-3 bg-gray-800/50"
                        data-quality="auto" data-video-index="{{ $index }}">
                        <i class="fas fa-magic text-purple-400"></i>
                        <div class="flex-1">
                            <div class="text-white font-medium">Auto</div>
                            <div class="text-gray-500 text-xs">Seleziona automaticamente</div>
                        </div>
                        <i class="fas fa-check text-green-400"></i>
                    </button>

                    @foreach ($availableQualities as $quality => $qualityData)
                        @php
                            $icon = 'fas fa-video';
                            $color = 'text-gray-400';
                            switch ($quality) {
                                case 'original':
                                    $icon = 'fas fa-crown';
                                    $color = 'text-yellow-400';
                                    break;
                                case '2160p':
                                case '1440p':
                                case '1080p':
                                    $icon = 'fas fa-desktop';
                                    $color = 'text-green-400';
                                    break;
                                case '720p':
                                    $icon = 'fas fa-tablet-alt';
                                    $color = 'text-blue-400';
                                    break;
                                case '480p':
                                case '360p':
                                    $icon = 'fas fa-mobile-alt';
                                    $color = 'text-orange-400';
                                    break;
                            }
                        @endphp
                        <button
                            class="quality-option w-full text-left px-4 py-3 rounded-xl hover:bg-gray-800 transition-all duration-150 flex items-center gap-3"
                            data-quality="{{ $quality }}" data-video-index="{{ $index }}">
                            <i class="{{ $icon }} {{ $color }}"></i>
                            <div class="flex-1">
                                <div class="text-white font-medium">{{ $qualityLabels[$quality] ?? $quality }}</div>
                                @if (isset($qualityData['formatted_file_size']))
                                    <div class="text-gray-500 text-xs">{{ $qualityData['formatted_file_size'] }}</div>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <!-- Context Menu - Right Click on Video -->
    <div id="contextMenu" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/50 hidden pointer-events-auto" id="contextMenuBackdrop"
            onclick="window.hideContextMenu()">
        </div>
        <div class="absolute bg-gray-900/98 backdrop-blur-2xl border border-gray-700/50 rounded-2xl shadow-2xl min-w-64 overflow-hidden transform transition-all duration-200 scale-95 opacity-0 pointer-events-auto"
            id="contextMenuContent">
            <div class="py-2">
                <!-- Header -->
                <div class="px-4 py-2 border-b border-gray-700/50">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Opzioni</p>
                </div>

                <!-- Play/Pause -->
                <button onclick="window.togglePlayPause()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-play text-gray-400 w-5 group-hover:text-white"></i>
                    <span class="text-sm">Play / Pausa</span>
                </button>

                <!-- Sound Toggle -->
                <button onclick="window.toggleMute()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-volume-up text-gray-400 w-5 group-hover:text-white" id="contextMuteIcon"></i>
                    <span class="text-sm" id="contextMuteText">Disattiva audio</span>
                </button>

                <div class="border-t border-gray-700/50 my-1"></div>

                <!-- Save/Watch Later -->
                <button onclick="window.toggleWatchLaterFromContext()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-bookmark text-gray-400 w-5 group-hover:text-green-400"
                        id="contextWatchLaterIcon"></i>
                    <span class="text-sm" id="contextWatchLaterText">Aggiungi a Salva</span>
                </button>

                <!-- Share -->
                <button onclick="window.shareCurrentReel()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-share text-gray-400 w-5 group-hover:text-blue-400"></i>
                    <span class="text-sm">Condividi</span>
                </button>

                <!-- Copy Link -->
                <button onclick="window.copyVideoLink()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-link text-gray-400 w-5 group-hover:text-blue-400"></i>
                    <span class="text-sm">Copia link</span>
                </button>

                <div class="border-t border-gray-700/50 my-1"></div>

                <!-- Speed -->
                <button onclick="window.openSpeedMenu()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-tachometer-alt text-orange-400 w-5"></i>
                    <span class="text-sm">Velocità</span>
                    <span class="ml-auto text-xs text-gray-500" id="currentSpeedDisplay">1x</span>
                </button>

                <!-- Loop -->
                <button onclick="window.toggleLoop()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-redo text-green-400 w-5"></i>
                    <span class="text-sm">Loop</span>
                    <i class="fas fa-check ml-auto text-green-400 hidden" id="loopCheck"></i>
                </button>

                <div class="border-t border-gray-700/50 my-1"></div>

                <!-- Go to Channel -->
                <button onclick="window.goToChannel()"
                    class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-user-circle text-blue-400 w-5"></i>
                    <span class="text-sm">Vai al canale</span>
                </button>

                <div class="border-t border-gray-700/50 my-1"></div>

                <!-- Report -->
                <button onclick="window.reportVideo()"
                    class="w-full px-4 py-3 text-left text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-4 group">
                    <i class="fas fa-flag w-5"></i>
                    <span class="text-sm">Segnala</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black/80 flex items-center justify-center z-40 hidden">
        <div class="text-center">
            <div class="w-12 h-12 border-4 border-gray-600 border-t-red-600 rounded-full animate-spin mx-auto mb-4">
            </div>
            <p class="text-white">Caricamento...</p>
        </div>
    </div>

    <!-- Script Configuration -->
    <script>
        window.reelShowConfig = {
            currentIndex: {{ $currentIndex }},
            totalReels: {{ count($reels) }},
            isMuted: {{ $isMuted ? 'true' : 'false' }}
        };

        // Store video URLs for JavaScript
        window.reelVideoUrls = {};
    @foreach ($reels as $index => $reel)
            window.reelVideoUrls[{{ $index }}] =
                '{{ $reel['video_url'] ? url($reel['video_url']) : url('/reels/' . $reel['id']) }}';
    @endforeach
    </script>
</div>
