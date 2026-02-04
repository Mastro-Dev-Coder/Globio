<!-- MiniPlayer Component - Moderno e Professionale -->
<div id="miniPlayer"
    class="fixed bottom-4 right-4 w-80 bg-gray-900/95 backdrop-blur-xl border border-gray-700 rounded-xl shadow-2xl z-50 transform translate-y-full opacity-0 transition-all duration-300 ease-out"
    style="display: none;">

    <!-- Header del MiniPlayer -->
    <div class="flex items-center justify-between p-3 border-b border-gray-700/50">
        <div class="flex items-center gap-2 flex-1 min-w-0">
            <!-- Thumbnail Video -->
            <div class="w-12 h-8 bg-gray-800 rounded overflow-hidden flex-shrink-0">
                <img id="miniPlayerThumbnail" src="" alt="Video thumbnail" class="w-full h-full object-cover">
            </div>

            <!-- Info Video -->
            <div class="flex-1 min-w-0">
                <h4 id="miniPlayerTitle" class="text-white text-sm font-medium line-clamp-1 leading-tight">
                    Caricamento...
                </h4>
                <p id="miniPlayerChannel" class="text-gray-400 text-xs line-clamp-1">
                    Canale
                </p>
            </div>
        </div>

        <!-- Pulsanti Header -->
        <div class="flex items-center gap-1 ml-2">
            <!-- Espandi -->
            <button id="miniPlayerExpand"
                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                title="Espandi video">
                <i class="fas fa-expand-alt text-xs"></i>
            </button>

            <!-- Chiudi -->
            <button id="miniPlayerClose"
                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                title="Chiudi miniplayer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Area Video -->
    <div class="relative bg-black">
        <video id="miniPlayerVideo" class="w-full h-44 object-cover rounded-b-xl" preload="metadata">
        </video>

        <!-- Overlay Play/Pause -->
        <div id="miniPlayerOverlay"
            class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 hover:opacity-100 transition-opacity duration-200 cursor-pointer">
            <div class="w-12 h-12 bg-black/70 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i id="miniPlayerOverlayIcon" class="fas fa-play text-white text-lg"></i>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="miniPlayerLoading" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0">
            <div class="w-8 h-8 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        </div>
    </div>

    <!-- Controlli -->
    <div class="p-3 space-y-3">
        <!-- Progress Bar -->
        <div class="relative">
            <div id="miniPlayerProgressContainer"
                class="w-full h-1.5 bg-gray-600 rounded-full cursor-pointer group/progress">
                <div id="miniPlayerProgressBar" class="absolute h-1.5 bg-red-600 rounded-full left-0 top-0"
                    style="width: 0%;"></div>
                <div id="miniPlayerProgressHover"
                    class="absolute h-1.5 bg-red-400/50 rounded-full left-0 top-0 opacity-0 group-hover/progress:opacity-100"
                    style="width: 0%;"></div>
                <div id="miniPlayerProgressThumb"
                    class="absolute w-2.5 h-2.5 bg-red-600 rounded-full -top-0.5 opacity-0 group-hover/progress:opacity-100 transition-opacity duration-200 shadow transform scale-0 group-hover/progress:scale-100"
                    style="left: 0%;"></div>
            </div>

            <!-- Tooltip Tempo -->
            <div id="miniPlayerTimeTooltip"
                class="absolute bottom-3 bg-black/90 text-white text-xs py-1 px-2 rounded opacity-0 pointer-events-none transition-all duration-200 font-medium">
                0:00
            </div>
        </div>

        <!-- Controlli Principali -->
        <div class="flex items-center justify-between">
            <!-- Controlli Sinistra -->
            <div class="flex items-center gap-2">
                <!-- Play/Pause -->
                <button id="miniPlayerPlayPause"
                    class="w-8 h-8 flex items-center justify-center text-white hover:bg-gray-700 rounded-full transition-all duration-200">
                    <i class="fas fa-play text-sm"></i>
                </button>

                <!-- Skip Backward -->
                <button id="miniPlayerSkipBack"
                    class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                    title="Indietro 10 secondi">
                    <i class="fas fa-undo text-xs"></i>
                </button>

                <!-- Skip Forward -->
                <button id="miniPlayerSkipForward"
                    class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                    title="Avanti 10 secondi">
                    <i class="fas fa-redo text-xs"></i>
                </button>

                <!-- Volume -->
                <div class="flex items-center gap-1 group/volume">
                    <button id="miniPlayerMute"
                        class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200">
                        <i class="fas fa-volume-up text-xs"></i>
                    </button>
                    <div
                        class="w-0 opacity-0 group-hover/volume:w-16 group-hover/volume:opacity-100 transition-all duration-300 overflow-hidden">
                        <input id="miniPlayerVolume" type="range" min="0" max="1" step="0.01"
                            value="1"
                            class="w-full h-1 bg-gray-600 rounded-full appearance-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-2.5 [&::-webkit-slider-thumb]:h-2.5 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Controlli Destra -->
            <div class="flex items-center gap-2">
                <!-- Tempo Corrente -->
                <div class="flex items-center gap-1 text-xs font-medium text-gray-300">
                    <span id="miniPlayerCurrentTime">0:00</span>
                    <span>/</span>
                    <span id="miniPlayerDuration">0:00</span>
                </div>

                <!-- Settings -->
                <button id="miniPlayerSettings"
                    class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                    title="Impostazioni">
                    <i class="fas fa-cog text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay per espandere il miniplayer -->
<div id="miniPlayerExpandOverlay"
    class="fixed inset-0 bg-black/80 z-40 opacity-0 pointer-events-none transition-all duration-300"
    style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-full max-w-4xl mx-4">
            <div class="bg-gray-900 rounded-xl overflow-hidden shadow-2xl">
                <!-- Header Espanso -->
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-16 h-10 bg-gray-800 rounded overflow-hidden">
                            <img id="expandPlayerThumbnail" src="" alt="Video thumbnail"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 id="expandPlayerTitle" class="text-white text-lg font-semibold line-clamp-1">
                                Titolo del video
                            </h3>
                            <p id="expandPlayerChannel" class="text-gray-400 text-sm">
                                Nome Canale
                            </p>
                        </div>
                    </div>
                    <button id="expandPlayerClose"
                        class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded-full transition-all duration-200">
                        <i class="fas fa-compress-alt"></i>
                    </button>
                </div>

                <!-- Video Espanso -->
                <div class="relative bg-black">
                    <video id="expandPlayerVideo" class="w-full aspect-video" preload="metadata">
                    </video>
                </div>

                <!-- Controlli Espansi -->
                <div class="p-4 space-y-4">
                    <div class="relative">
                        <div id="expandPlayerProgressContainer"
                            class="w-full h-2 bg-gray-600 rounded-full cursor-pointer group/progress">
                            <div id="expandPlayerProgressBar"
                                class="absolute h-2 bg-red-600 rounded-full left-0 top-0" style="width: 0%;"></div>
                            <div id="expandPlayerProgressHover"
                                class="absolute h-2 bg-red-400/50 rounded-full left-0 top-0 opacity-0 group-hover/progress:opacity-100"
                                style="width: 0%;"></div>
                            <div id="expandPlayerProgressThumb"
                                class="absolute w-3 h-3 bg-red-600 rounded-full -top-0.5 opacity-0 group-hover/progress:opacity-100 transition-opacity duration-200 shadow transform scale-0 group-hover/progress:scale-100"
                                style="left: 0%;"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button id="expandPlayerPlayPause"
                                class="w-12 h-12 flex items-center justify-center text-white hover:bg-gray-700 rounded-full transition-all duration-200">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                            <button id="expandPlayerSkipBack"
                                class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                                title="Indietro 10 secondi">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button id="expandPlayerSkipForward"
                                class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                                title="Avanti 10 secondi">
                                <i class="fas fa-redo"></i>
                            </button>
                            <div class="flex items-center gap-2 ml-4">
                                <button id="expandPlayerMute"
                                    class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                                <input id="expandPlayerVolume" type="range" min="0" max="1"
                                    step="0.01" value="1"
                                    class="w-24 h-1 bg-gray-600 rounded-full appearance-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-300">
                                <span id="expandPlayerCurrentTime">0:00</span>
                                <span>/</span>
                                <span id="expandPlayerDuration">0:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Utility classes for text truncation */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar for better aesthetics */
    .miniplayer-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .miniplayer-scrollbar::-webkit-scrollbar-track {
        background: rgba(75, 85, 99, 0.3);
        border-radius: 2px;
    }

    .miniplayer-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.8);
        border-radius: 2px;
    }

    .miniplayer-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 1);
    }

    /* Animation for smooth transitions */
    @keyframes miniplayerSlideIn {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes miniplayerSlideOut {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }

    .miniplayer-enter {
        animation: miniplayerSlideIn 0.3s ease-out forwards;
    }

    .miniplayer-exit {
        animation: miniplayerSlideOut 0.3s ease-in forwards;
    }
</style>
