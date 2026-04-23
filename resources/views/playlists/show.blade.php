<x-layout>
    {!! \App\Helpers\AdvertisementHelper::generateClickTrackingScript() !!}

    <!-- Main Content Container -->
    <div class="w-full mx-auto px-3 sm:px-4 md:px-5 lg:px-6 xl:px-8 py-4 sm:py-6 overflow-hidden">
        <!-- Playlist Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                <!-- Playlist Thumbnail -->
                <div class="w-full md:w-80 flex-shrink-0">
                    <div class="relative overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 aspect-video">
                        @if ($playlist->videos->count() > 0 && $currentVideo)
                            @if ($currentVideo->thumbnail_path)
                                <img src="{{ asset('storage/' . $currentVideo->thumbnail_path) }}"
                                    alt="{{ $playlist->title }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                                    <div class="text-center">
                                        <div
                                            class="w-16 h-16 mx-auto mb-2 bg-white/30 dark:bg-black/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                            <i class="fas fa-folder text-2xl text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Playlist</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 mx-auto mb-2 bg-white/30 dark:bg-black/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                        <i class="fas fa-folder text-2xl text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Playlist</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Playlist Info -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $playlist->title }}
                        </h1>
                        @auth
                            @if ($playlist->user_id === Auth::id())
                                <div class="flex gap-2">
                                    <button onclick="window.location.href='{{ route('playlists') }}'"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                        <i class="fas fa-edit mr-1"></i> Modifica
                                    </button>
                                    <button onclick="deletePlaylist('{{ $playlist->id }}')"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                                        <i class="fas fa-trash mr-1"></i> Elimina
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            @if ($playlist->user->userProfile->avatar_url)
                                <img src="{{ asset('storage/' . $playlist->user->userProfile->avatar_url) }}"
                                    alt="{{ $playlist->user->userProfile->channel_name }}"
                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            @else
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-lg font-medium">
                                        {{ strtoupper(substr($playlist->user->userProfile->channel_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <a href="{{ route('channel.show', $playlist->user->userProfile?->channel_name) }}"
                                class="font-medium text-gray-900 dark:text-white hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                {{ $playlist->user->userProfile?->channel_name ?: $playlist->user->name }}
                            </a>
                        </div>
                        <span class="text-gray-500 dark:text-gray-400">{{ number_format($playlist->views_count) }}
                            visualizzazioni</span>
                    </div>

                    @if ($playlist->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ $playlist->description }}
                        </p>
                    @endif

                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-video"></i>
                            <span>{{ $playlist->videos->count() }} video</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $playlist->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Advertisement Section -->
        <div class="mb-6 md:mb-8">
            <x-advertisements position="playlist_video" />
        </div>

        <!-- Playlist Video Player Layout (YouTube Style) -->
        @if ($playlist->videos->count() > 0 && $currentVideo)
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <!-- Video Player Section -->
                <div class="xl:col-span-8 space-y-4">
                    <div id="videoWrapper" class="relative rounded-xl">
                        <x-video-player-with-ads :video="$currentVideo" :next-video="$nextPlaylistVideo" class="w-full aspect-video" />

                        <!-- Video Overlay Advertisement -->
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="relative w-full h-full">
                                <x-advertisements position="video_overlay" />
                            </div>
                        </div>
                    </div>

                    <!-- Current Video Info -->
                    <div class="space-y-4 px-1">
                        <h1 class="text-xl font-medium text-white leading-7">
                            {{ $currentVideo->title }}
                        </h1>

                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <span class="font-medium text-gray-300">
                                {{ number_format($currentVideo->views_count) }} visualizzazioni
                            </span>
                            <span>•</span>
                            <span>{{ $currentVideo->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Playlist Videos Sidebar (YouTube Style) -->
                <div class="xl:col-span-4 space-y-3">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Video nella playlist
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $playlistVideos->count() }} video
                        </span>
                    </div>

                    <div class="space-y-2 max-h-[600px] overflow-y-auto pr-2" id="playlistSidebar">
                        @foreach ($playlistVideos as $index => $video)
                            <a href="{{ route('playlists.show', $playlist) }}?video={{ $video->id }}"
                                class="group block p-2 rounded-lg transition-colors {{ $currentVideo && $video->id === $currentVideo->id ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                                data-playlist-video-id="{{ $video->id }}"
                                onclick="playPlaylistVideo({{ $video->id }}, {{ $index }}, event)">
                                <div class="flex gap-3">
                                    <!-- Thumbnail -->
                                    <div
                                        class="relative w-40 h-24 flex-shrink-0 bg-gray-900 rounded-lg overflow-hidden">
                                        @if ($video->thumbnail_path)
                                            <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                alt="{{ $video->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @endif

                                        <!-- Duration Badge -->
                                        <div
                                            class="absolute bottom-1 right-1 bg-black/80 text-white text-xs px-1.5 py-0.5 rounded font-semibold">
                                            {{ $video->formatted_duration }}
                                        </div>

                                        <!-- Playing Indicator -->
                                        @if ($currentVideo && $video->id === $currentVideo->id)
                                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                                <div
                                                    class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                                    <i class="fas fa-play text-white text-sm ml-0.5"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Video Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors leading-tight mb-1">
                                            {{ $video->title }}
                                        </h4>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
                                            {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                                        </p>
                                        <div
                                            class="flex items-center gap-1 text-xs text-gray-400 group-hover:text-gray-500 transition-colors">
                                            <span>{{ number_format($video->views_count) }} visualizzazioni</span>
                                            <span>•</span>
                                            <span>{{ $video->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Playlist Message -->
            <div class="text-center py-12 sm:py-16">
                <div
                    class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-video text-xl sm:text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Nessun video nella playlist
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Questa playlist è vuota. Torna presto per scoprire nuovi contenuti!
                </p>
            </div>
        @endif

        <!-- Related Playlists Section -->
        @if ($relatedPlaylists->count() > 0)
            <section class="mt-8 md:mt-12">
                <div class="mb-4 md:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                        Playlist simili
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5 md:gap-6">
                    @foreach ($relatedPlaylists as $relatedPlaylist)
                        <x-playlist :playlist="$relatedPlaylist" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        // Store playlist data globally
        window.playlistData = {
            videos: @json($playlistVideos ?? []),
            currentIndex: {{ $currentIndex ?? 0 }},
            playlistId: {{ $playlist->id }},
            currentVideoId: {{ $currentVideo->id ?? 'null' }}
        };

        // Function to play a specific video from the playlist
        function playPlaylistVideo(videoId, index, event) {
            if (event) {
                event.preventDefault();
            }

            // Update current index
            window.playlistData.currentIndex = index;
            window.playlistData.currentVideoId = videoId;

            // Navigate to the video with the parameter
            window.location.href = `/playlists/${window.playlistData.playlistId}?video=${videoId}`;
        }

        // Function to delete a playlist
        function deletePlaylist(playlistId) {
            if (confirm('Sei sicuro di voler eliminare questa playlist?')) {
                fetch(`/playlists/${playlistId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Playlist eliminata con successo!', 'success');
                            window.location.href = '/playlists';
                        } else {
                            showToast(data.message || 'Errore nell\'eliminazione della playlist', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Errore nell\'eliminazione della playlist', 'error');
                    });
            }
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
            }, 4000);
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll current video into view in sidebar
            const currentVideoId = window.playlistData.currentVideoId;
            if (currentVideoId) {
                const currentVideoElement = document.querySelector(`[data-playlist-video-id="${currentVideoId}"]`);
                if (currentVideoElement) {
                    setTimeout(() => {
                        currentVideoElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 500);
                }
            }
        });
    </script>
</x-layout>
