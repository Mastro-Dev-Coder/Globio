<x-layout>
    {!! \App\Helpers\AdvertisementHelper::generateClickTrackingScript() !!}

    <div class="w-full mx-auto px-3 sm:px-4 md:px-5 lg:px-6 xl:px-8 py-4 sm:py-6 overflow-hidden">

        <div class="mb-6 md:mb-8">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('ui.home') }}
                </h2>
            </div>

            <div class="relative">
                <div id="filtersContainer"
                    class="flex items-center space-x-2 sm:space-x-3 overflow-x-auto scrollbar-hide pb-2 px-1"
                    style="scroll-behavior: smooth;">
                    @foreach (['Tutti', __('ui.video'), __('ui.reels'), __('ui.music'), __('ui.gaming'), __('ui.trending_page'), __('ui.live')] as $filter)
                        <button onclick="filterVideos('{{ strtolower($filter) }}')"
                            data-filter="{{ strtolower($filter) }}"
                            class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 text-xs sm:text-sm font-medium whitespace-nowrap filter-btn">
                            {{ $filter }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


        <!-- Video Advertisement Section -->
        <div class="mb-6 md:mb-8">
            <x-advertisements position="home_video" />
        </div>

        <!-- Content Sections -->
        <div class="space-y-6 md:space-y-8">

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="hidden flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-white"></div>
            </div>

            <!-- Videos and Playlists Container -->
            <div id="videosContainer">
                @if ($integratedContent->count() > 0)
                    @php
                        $videoCount = 0;
                        $reelsPerCarousel = 8;
                        $videosBeforeReels = 8;
                        $allReels = collect();
                        $displayedReelIds = collect();
                        $isReelsCategory = request()->get('category') === 'reels';
                    @endphp

                    <div id="reels-grid-container" class="{{ $isReelsCategory ? '' : 'hidden' }}">
                        <div
                            class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 gap-3 sm:gap-4 md:gap-6">
                            @foreach ($integratedContent as $item)
                                @if ($item['type'] === 'video' && $item['content']->is_reel)
                                    <div class="aspect-[9/16]">
                                        <x-reel :video="$item['content']" />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div id="regular-videos-container" class="{{ $isReelsCategory ? 'hidden' : '' }}">
                        <div
                            class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
                            @foreach ($integratedContent as $index => $item)
                                @if ($item['type'] === 'video' && !$item['content']->is_reel)
                                    @if ($videoCount === 3 || ($videoCount > 3 && $videoCount % $videosBeforeReels === 0))
                                        @php
                                            $remainingReels = $integratedContent
                                                ->where('type', 'video')
                                                ->where('content.is_reel', true)
                                                ->pluck('content')
                                                ->whereNotIn('id', $displayedReelIds)
                                                ->take($reelsPerCarousel);

                                            if ($remainingReels->isEmpty()) {
                                                $displayedReelIds = collect();
                                                $remainingReels = $integratedContent
                                                    ->where('type', 'video')
                                                    ->where('content.is_reel', true)
                                                    ->pluck('content')
                                                    ->take($reelsPerCarousel);
                                            }

                                            $allReels = $allReels->merge($remainingReels);
                                            $displayedReelIds = $displayedReelIds->merge($remainingReels->pluck('id'));
                                        @endphp

                                        @if ($remainingReels->count() > 0)
                                            <div class="col-span-full">
                                                <section class="mb-6 md:mb-8">
                                                    <div class="mb-4 md:mb-6">
                                                        <h2
                                                            class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                                            <i class="fa-solid fa-circle-play w-4 h-4"></i>
                                                            Reels
                                                        </h2>
                                                    </div>

                                                    <div class="relative group">
                                                        <div class="flex space-x-3 sm:space-x-4 overflow-x-auto scrollbar-hide pb-4 px-1"
                                                            style="scroll-behavior: smooth;"
                                                            id="reels-container-{{ $videoCount }}"
                                                            data-reels-carousel>
                                                            @foreach ($remainingReels as $reel)
                                                                <div
                                                                    class="flex-shrink-0 w-36 sm:w-40 md:w-[20%] md:min-w-[20%] xl:w-[20%] xl:min-w-[30%] cursor-pointer">
                                                                    <div class="group cursor-pointer relative p-1 sm:p-2 rounded-xl w-full max-w-full"
                                                                        data-color-wrapper-trending
                                                                        onclick="window.location.href='{{ route('videos.show', $reel) }}'">
                                                                        <div class="relative aspect-[9/16] bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden mb-2 sm:mb-3"
                                                                            data-thumbnail-trending
                                                                            style="--hover-bg: rgba(255, 0, 0, 0.35);">
                                                                            @if ($reel->thumbnail_path)
                                                                                <img src="{{ asset('storage/' . $reel->thumbnail_path) }}"
                                                                                    alt="{{ $reel->title }}"
                                                                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                                                                            @else
                                                                                <div
                                                                                    class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                                                                                    <i
                                                                                        class="fas fa-play text-sm sm:text-base text-gray-400"></i>
                                                                                </div>
                                                                            @endif

                                                                            @if ($reel->duration)
                                                                                <div
                                                                                    class="absolute bottom-1.5 right-1.5 bg-black/80 text-white text-xs px-1 py-0.5 rounded font-medium">
                                                                                    {{ gmdate('i:s', $reel->duration) }}
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <div class="space-y-0.5 sm:space-y-1 px-1">
                                                                            <h3
                                                                                class="font-medium text-gray-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-tight">
                                                                                {{ $reel->title }}
                                                                            </h3>
                                                                            <div
                                                                                class="text-xs text-gray-600 dark:text-gray-400">
                                                                                {{ number_format($reel->views_count) }}
                                                                                {{ __('ui.views') }}
                                                                                visualizzazioni
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <button
                                                            onclick="scrollReels('prev', 'reels-container-{{ $videoCount }}')"
                                                            data-reels-prev="reels-container-{{ $videoCount }}"
                                                            class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-2 sm:-translate-x-4 w-6 h-6 sm:w-8 sm:h-8 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center opacity-0 hover:opacity-100 group-hover:opacity-100 transition-opacity z-10 hover:bg-gray-50 dark:hover:bg-gray-700 hidden sm:flex">
                                                            <i
                                                                class="fas fa-chevron-left text-xs sm:text-sm text-gray-600 dark:text-gray-400"></i>
                                                        </button>
                                                        <button
                                                            onclick="scrollReels('next', 'reels-container-{{ $videoCount }}')"
                                                            data-reels-next="reels-container-{{ $videoCount }}"
                                                            class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-2 sm:translate-x-4 w-6 h-6 sm:w-8 sm:h-8 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center opacity-0 hover:opacity-100 group-hover:opacity-100 transition-opacity z-10 hover:bg-gray-50 dark:hover:bg-gray-700 hidden sm:flex">
                                                            <i
                                                                class="fas fa-chevron-right text-xs sm:text-sm text-gray-600 dark:text-gray-400"></i>
                                                        </button>
                                                    </div>
                                                </section>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Regular Video -->
                                    <div class="w-full">
                                        <x-video :video="$item['content']" />
                                    </div>

                                    @php $videoCount++; @endphp
                                @elseif ($item['type'] === 'playlist')
                                    <!-- Playlist -->
                                    <div class="w-full">
                                        @php $playlistSource = $item['source'] ?? 'recommended'; @endphp
                                        <x-playlist :playlist="$item['content']" :source="$playlistSource" />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Load More Button -->
                    @if ($videos->hasMorePages())
                        <div class="text-center mt-6 md:mt-8" id="loadMoreContainer">
                            <button onclick="loadMoreVideos()"
                                class="inline-flex items-center px-5 sm:px-6 py-2.5 sm:py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors font-medium text-sm">
                                Carica altri video
                            </button>
                        </div>
                    @else
                        <div class="text-center mt-6 md:mt-8" id="loadMoreContainer" style="display: none;">
                            <button onclick="loadMoreVideos()"
                                class="inline-flex items-center px-5 sm:px-6 py-2.5 sm:py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors font-medium text-sm">
                                Carica altri video
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 sm:py-16">
                        <div
                            class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-video text-xl sm:text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('ui.no_videos_to_show') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('ui.try_searching') }}
                        </p>
                    </div>
                @endif
            </div>


        </div>
    </div>

    <script>
        // Global variables for modals
        window.currentVideoId = null;

        // Professional modal functions
        function showCreatePlaylistModal() {
            document.getElementById('createPlaylistModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreatePlaylistModal() {
            document.getElementById('createPlaylistModal').classList.add('hidden');
            const form = document.getElementById('createPlaylistForm');
            if (form) form.reset();
            document.body.style.overflow = 'auto';
        }

        function showAddToPlaylistModal(videoId) {
            window.currentVideoId = videoId;
            document.getElementById('addToPlaylistModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loadUserPlaylists();
        }

        function closeAddToPlaylistModal() {
            document.getElementById('addToPlaylistModal').classList.add('hidden');
            window.currentVideoId = null;
            document.body.style.overflow = 'auto';
        }

        // Create playlist form submission
        document.addEventListener('DOMContentLoaded', function() {
            const createForm = document.getElementById('createPlaylistForm');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('/playlists', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast('{{ __('ui.playlist_created_success') }}', 'success');
                                closeCreatePlaylistModal();

                                // If we have a video to add, add it to the new playlist
                                if (window.currentVideoId) {
                                    addVideoToPlaylist(data.playlist.id);
                                }
                            } else {
                                showToast('Errore nella creazione della playlist', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Errore nella creazione della playlist', 'error');
                        });
                });
            }
        });

        // Load user playlists for add to playlist modal
        function loadUserPlaylists() {
            fetch('/playlists-data')
                .then(response => response.json())
                .then(data => {
                    const playlistList = document.getElementById('playlistList');
                    if (!playlistList) return;

                    playlistList.innerHTML = '';

                    if (data.playlists.length === 0) {
                        playlistList.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Nessuna playlist disponibile</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Crea la tua prima playlist</p>
                    </div>
                `;
                        return;
                    }

                    data.playlists.forEach(playlist => {
                        const button = document.createElement('button');
                        button.className =
                            'w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:bg-gray-600 rounded-xl transition-all duration-200 border border-transparent hover:border-indigo-200 dark:hover:border-gray-500 group';
                        button.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-folder text-white text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">${playlist.title || playlist.name}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${playlist.videos_count} video</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                `;
                        button.onclick = () => addVideoToPlaylist(playlist.id);
                        playlistList.appendChild(button);
                    });
                })
                .catch(error => {
                    console.error('Error loading playlists:', error);
                });
        }

        // Add video to playlist
        function addVideoToPlaylist(playlistId) {
            if (!window.currentVideoId) return;

            fetch(`/playlists/${playlistId}/videos`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        video_id: window.currentVideoId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Video aggiunto alla playlist!', 'success');
                        closeAddToPlaylistModal();
                    } else {
                        showToast(data.message || 'Errore nell\'aggiungere il video', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Errore nell\'aggiungere il video', 'error');
                });
        }

        // Add to watch later
        function addToWatchLater(videoId, button) {
            fetch('/watch-later', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        video_id: videoId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Professional button state change
                        if (button) {
                            button.className =
                                'w-8 h-8 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center transition-colors shadow-lg';
                            button.innerHTML = '<i class="fas fa-check text-xs"></i>';
                            button.title = 'Aggiunto a Guarda più tardi';
                            button.onclick = function(e) {
                                e.preventDefault();
                                removeFromWatchLater(videoId, this);
                            };

                            // Add success animation
                            button.style.transform = 'scale(1.2)';
                            setTimeout(() => {
                                button.style.transform = 'scale(1)';
                            }, 200);
                        }
                        showToast('Aggiunto a Guarda più tardi!', 'success');
                    } else {
                        showToast(data.message || 'Errore nell\'aggiungere il video', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Errore nell\'aggiungere il video', 'error');
                });
        }

        // Remove from watch later
        function removeFromWatchLater(videoId, button) {
            fetch(`/watch-later/${videoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Change button appearance back
                        if (button) {
                            button.className =
                                'w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-colors shadow-lg';
                            button.innerHTML = '<i class="fas fa-clock text-xs"></i>';
                            button.title = 'Aggiungi a Guarda più tardi';
                            button.onclick = function(e) {
                                e.preventDefault();
                                addToWatchLater(videoId, this);
                            };
                        }
                        showToast('Rimosso da Guarda più tardi', 'info');
                    } else {
                        showToast('Errore nel rimuovere il video', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Errore nel rimuovere il video', 'error');
                });
        }

        // Professional toast notification
        function showToast(message, type = 'info') {
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
                `fixed top-6 right-6 bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-2xl shadow-2xl z-[9999] transform transition-transform duration-300 border border-white/20 backdrop-blur-sm flex items-center gap-3`;
            toast.innerHTML = `
            <i class="${icons[type]} text-lg"></i>
            <span class="font-medium">${message}</span>
        `;

            // Ensure toast is visible immediately
            toast.style.transform = 'translateX(120%)';
            document.body.appendChild(toast);

            // Trigger animation
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
            });

            // Remove after delay
            setTimeout(() => {
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'addToPlaylistModal') {
                closeAddToPlaylistModal();
            }
            if (e.target.id === 'createPlaylistModal') {
                closeCreatePlaylistModal();
            }
        });

        // Touch-friendly scroll for reels on mobile
        function scrollReels(direction, containerId) {
            const container = document.getElementById(containerId);
            const scrollAmount = window.innerWidth < 640 ? 150 : 200;

            if (direction === 'next') {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        // Filter functionality
        function filterVideos(filter) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
            });

            const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
            if (activeBtn) {
                activeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                activeBtn.classList.add('bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
            }

            // Show/hide appropriate containers
            if (filter === 'reels') {
                document.getElementById('reels-grid-container').classList.remove('hidden');
                document.getElementById('regular-videos-container').classList.add('hidden');
            } else {
                document.getElementById('reels-grid-container').classList.add('hidden');
                document.getElementById('regular-videos-container').classList.remove('hidden');
            }
        }

        // Load more videos
        function loadMoreVideos() {
            // Implementation depends on your backend
            console.log('Load more videos functionality');
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-thumbnail], [data-thumbnail-trending]").forEach(card => {
                const wrapper = card.closest("[data-color-wrapper], [data-color-wrapper-trending]");
                const img = card.querySelector("img");

                if (!img || !wrapper) return;

                img.addEventListener("load", () => {
                    const color = getDominantColor(img);
                    const rgb = `rgba(${color.r}, ${color.g}, ${color.b}, 0.35)`;
                    wrapper.style.setProperty("--hover-bg", rgb);
                });
            });

            function getDominantColor(image) {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                canvas.width = 50;
                canvas.height = 50;
                ctx.drawImage(image, 0, 0, 50, 50);

                const data = ctx.getImageData(0, 0, 50, 50).data;

                let r = 0,
                    g = 0,
                    b = 0,
                    count = 0;
                for (let i = 0; i < data.length; i += 4) {
                    r += data[i];
                    g += data[i + 1];
                    b += data[i + 2];
                    count++;
                }

                return {
                    r: Math.round(r / count),
                    g: Math.round(g / count),
                    b: Math.round(b / count)
                };
            }
        });

        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = true;
        let currentCategory = 'all';

        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('scroll', handleScroll);
            updateFilterButtons('all');
            initializeFilterScroll();

            // Inizializza lo stato del pulsante di caricamento
            @if (!$videos->hasMorePages())
                hasMorePages = false;
                const loadMoreContainer = document.getElementById('loadMoreContainer');
                if (loadMoreContainer) {
                    loadMoreContainer.style.display = 'none';
                }
            @endif
        });

        // Initialize filter scroll functionality
        function initializeFilterScroll() {
            const filtersContainer = document.getElementById('filtersContainer');
            const scrollIndicator = document.getElementById('scrollIndicator');

            if (!filtersContainer) return;

            // Check if scrolling is possible
            function checkScrollability() {
                const canScroll = filtersContainer.scrollWidth > filtersContainer.clientWidth;
                if (scrollIndicator) {
                    scrollIndicator.style.display = canScroll ? 'flex' : 'none';
                }
            }

            // Initial check
            checkScrollability();

            // Check on window resize
            window.addEventListener('resize', checkScrollability);

            // Hide/show indicator based on scroll position
            filtersContainer.addEventListener('scroll', function() {
                const maxScroll = filtersContainer.scrollWidth - filtersContainer.clientWidth;
                const currentScroll = filtersContainer.scrollLeft;

                if (scrollIndicator) {
                    if (currentScroll >= maxScroll - 10) {
                        scrollIndicator.style.opacity = '0';
                    } else {
                        scrollIndicator.style.opacity = '1';
                    }
                }
            });

            // Touch swipe support for mobile
            let startX = 0;
            let startY = 0;
            let isScrolling = false;

            filtersContainer.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isScrolling = false;
            }, {
                passive: true
            });

            filtersContainer.addEventListener('touchmove', function(e) {
                if (!startX || !startY) return;

                const diffX = startX - e.touches[0].clientX;
                const diffY = startY - e.touches[0].clientY;

                // If horizontal movement is greater than vertical, prevent default
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 10) {
                    isScrolling = true;
                    e.preventDefault();
                }
            }, {
                passive: false
            });

            filtersContainer.addEventListener('touchend', function() {
                startX = 0;
                startY = 0;
                isScrolling = false;
            });
        }

        function handleScroll() {
            if (isLoading || !hasMorePages) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            // Controlla se siamo vicini al fondo della pagina
            if (scrollTop + windowHeight >= documentHeight - 500) {
                loadMoreVideos();
            }
        }

        function loadMoreVideos() {
            if (isLoading || !hasMorePages) return;

            isLoading = true;
            document.getElementById('loadingSpinner').classList.remove('hidden');

            currentPage++;

            fetch(`/home/load-more?page=${currentPage}&category=${currentCategory}&sort=latest`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Se abbiamo ricevuto HTML da aggiungere
                        if (data.html && data.html.trim() !== '') {
                            const container = document.getElementById('videosContainer');
                            const grid = container.querySelector('.grid');
                            if (grid) {
                                grid.insertAdjacentHTML('beforeend', data.html);
                                reinitializeColorExtraction();
                            }
                        }

                        hasMorePages = data.has_more;

                        // Nascondi il pulsante se non ci sono più video
                        if (!hasMorePages) {
                            const loadMoreContainer = document.getElementById('loadMoreContainer');
                            if (loadMoreContainer) {
                                loadMoreContainer.style.display = 'none';
                            }
                        }

                        // Log per debug
                        console.log(`Caricati ${data.loaded_count || 0} di ${data.total_count || 0} video totali`);
                        console.log(`Ci sono ancora video da caricare: ${hasMorePages}`);
                    } else {
                        hasMorePages = false;
                        const loadMoreContainer = document.getElementById('loadMoreContainer');
                        if (loadMoreContainer) {
                            loadMoreContainer.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more videos:', error);
                    currentPage--;

                    // Mostra un messaggio di errore all'utente
                    showToast('Errore nel caricamento dei video. Riprova.', 'error');
                })
                .finally(() => {
                    isLoading = false;
                    document.getElementById('loadingSpinner').classList.add('hidden');
                });
        }

        // Filtra i video per categoria
        function filterVideos(category) {
            currentCategory = category;
            showLoading();

            // Aggiorna stato dei bottoni
            updateFilterButtons(category);

            fetch(`/home/filter?category=${category}&sort=latest`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Gestione speciale per i reels - mantieni la struttura della griglia
                        if (category === 'reels') {
                            const reelsGridContainer = document.getElementById('reels-grid-container');
                            const regularVideosContainer = document.getElementById('regular-videos-container');

                            // Mostra griglia reels e nascondi video normali
                            if (reelsGridContainer) reelsGridContainer.classList.remove('hidden');
                            if (regularVideosContainer) regularVideosContainer.classList.add('hidden');

                            // Aggiorna solo il contenuto della griglia reels
                            if (reelsGridContainer && data.reels_html) {
                                reelsGridContainer.innerHTML = data.reels_html;
                            }
                        } else {
                            const reelsGridContainer = document.getElementById('reels-grid-container');
                            const regularVideosContainer = document.getElementById('regular-videos-container');

                            // Mostra video normali e nascondi griglia reels
                            if (reelsGridContainer) reelsGridContainer.classList.add('hidden');
                            if (regularVideosContainer) regularVideosContainer.classList.remove('hidden');

                            // Aggiorna solo il contenuto dei video normali
                            if (regularVideosContainer && data.videos_html) {
                                regularVideosContainer.innerHTML = data.videos_html;
                            }
                        }

                        currentPage = 1;
                        hasMorePages = data.has_more;

                        // Mostra/nascondi il pulsante di caricamento in base ai risultati
                        const loadMoreContainer = document.getElementById('loadMoreContainer');
                        if (loadMoreContainer) {
                            if (hasMorePages && data.total_count > 12) {
                                loadMoreContainer.style.display = 'block';
                            } else {
                                loadMoreContainer.style.display = 'none';
                            }
                        }

                        reinitializeColorExtraction();

                        // Log per debug
                        console.log(`Categoria: ${category} - Trovati ${data.total_count || 0} video`);
                    }
                })
                .catch(error => {
                    console.error('Error filtering videos:', error);
                    showToast('Errore nel filtrare i video', 'error');
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Aggiorna lo stato dei bottoni di filtro
        function updateFilterButtons(activeCategory) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(button => {
                const category = button.getAttribute('data-filter');

                if (category === activeCategory) {
                    button.className =
                        'flex-shrink-0 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-200 text-sm font-medium scroll-snap-align-start filter-btn active';
                } else {
                    button.className =
                        'flex-shrink-0 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 text-sm font-medium scroll-snap-align-start filter-btn';
                }
            });

            // Smooth scroll to active button on mobile
            if (window.innerWidth < 1024) {
                const activeButton = document.querySelector(`[data-filter="${activeCategory}"]`);
                if (activeButton) {
                    setTimeout(() => {
                        activeButton.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'center'
                        });
                    }, 100);
                }
            }
        }



        // Show/Hide loading spinner
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        // Ri-inizializza l'estrazione del colore per i nuovi elementi
        function reinitializeColorExtraction() {
            document.querySelectorAll("[data-thumbnail], [data-thumbnail-trending]").forEach(card => {
                const wrapper = card.closest("[data-color-wrapper], [data-color-wrapper-trending]");
                const img = card.querySelector("img");

                if (!img || !wrapper) return;

                img.addEventListener("load", () => {
                    const color = getDominantColor(img);
                    const rgb = `rgba(${color.r}, ${color.g}, ${color.b}, 0.35)`;
                    wrapper.style.setProperty("--hover-bg", rgb);
                });
            });
        }

        // Funzione per scroll dei reels
        function scrollReels(direction, containerId) {
            const container = typeof containerId === 'string' ?
                document.getElementById(containerId) :
                containerId.parentElement.querySelector('.overflow-x-auto');

            if (!container) return;

            const scrollAmount = container.clientWidth * 0.8;
            const currentScroll = container.scrollLeft;

            if (direction === 'prev') {
                container.scrollTo({
                    left: currentScroll - scrollAmount,
                    behavior: 'smooth'
                });
            } else if (direction === 'next') {
                container.scrollTo({
                    left: currentScroll + scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        function updateReelsArrows(container) {
            if (!container) return;
            const id = container.getAttribute('id');
            const prevBtn = document.querySelector(`[data-reels-prev="${id}"]`);
            const nextBtn = document.querySelector(`[data-reels-next="${id}"]`);
            const maxScroll = container.scrollWidth - container.clientWidth;
            const atStart = container.scrollLeft <= 0;
            const atEnd = container.scrollLeft >= maxScroll - 1;

            if (prevBtn) {
                prevBtn.classList.toggle('reels-arrow-disabled', atStart);
            }
            if (nextBtn) {
                nextBtn.classList.toggle('reels-arrow-disabled', atEnd);
            }
        }

        function bindReelsArrows() {
            document.querySelectorAll('[data-reels-carousel]').forEach(container => {
                updateReelsArrows(container);
                container.addEventListener('scroll', () => updateReelsArrows(container));
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            bindReelsArrows();
            window.addEventListener('resize', () => {
                document.querySelectorAll('[data-reels-carousel]').forEach(container => updateReelsArrows(
                    container));
            });
        });
    </script>
</x-layout>
