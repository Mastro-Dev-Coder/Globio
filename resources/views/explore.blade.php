<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-compass mr-3 text-red-600"></i>{{ __('ui.explore_title') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.explore_subtitle') }}
            </p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="{{ route('explore') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Sort By -->
                <div class="flex-1">
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.sort_by') }}
                    </label>
                    <select name="sort" id="sort" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900 dark:text-white">
                        <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>{{ __('ui.newest') }}</option>
                        <option value="popular" {{ $sortBy === 'popular' ? 'selected' : '' }}>{{ __('ui.most_popular') }}</option>
                        <option value="trending" {{ $sortBy === 'trending' ? 'selected' : '' }}>{{ __('ui.trending') }}</option>
                        <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>{{ __('ui.oldest') }}</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="flex-1">
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.category') }}
                    </label>
                    <select name="category" id="category" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900 dark:text-white">
                        <option value="">{{ __('ui.all_categories') }}</option>
                        @foreach ($popularCategories as $cat => $count)
                            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }} ({{ $count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset Button -->
                @if ($sortBy !== 'latest' || $category)
                    <div class="flex items-end">
                        <a href="{{ route('explore') }}"
                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                            <i class="fas fa-redo mr-2"></i>{{ __('ui.reset') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Videos Grid -->
        @if ($videos->count() > 0)
            <div
                class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
                @foreach ($videos as $video)
                    <x-video :video="$video" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-video text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('ui.no_videos_found') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.try_modifying_filters') }}
                </p>
                <a href="{{ route('explore') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-redo mr-2"></i>
                    {{ __('ui.reset_filters') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Add to Playlist Modal -->
    <div id="addToPlaylistModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl p-6 w-full max-w-md shadow-2xl border border-white/20">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-folder-plus text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('ui.add_to_playlist') }}
                        </h3>
                    </div>
                    <button onclick="closeAddToPlaylistModal()"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-gray-600 dark:text-gray-300 text-sm"></i>
                    </button>
                </div>

                <div id="playlistList" class="space-y-2 mb-6 max-h-60 overflow-y-auto">
                    <!-- Playlists will be loaded here -->
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="showCreatePlaylistModal()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200 font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-plus text-sm"></i>{{ __('ui.new_playlist') }}
                    </button>
                    <button type="button" onclick="closeAddToPlaylistModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                        {{ __('ui.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Playlist Modal -->
    <div id="createPlaylistModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl p-6 w-full max-w-md shadow-2xl border border-white/20">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('ui.create_playlist') }}
                        </h3>
                    </div>
                    <button onclick="closeCreatePlaylistModal()"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-gray-600 dark:text-gray-300 text-sm"></i>
                    </button>
                </div>

                <form id="createPlaylistForm">
                    <div class="mb-5">
                        <label for="playlistName"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.playlist_name') }}
                        </label>
                        <input type="text" id="playlistName" name="name"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all"
                            placeholder="{{ __('ui.enter_playlist_name') }}" required>
                    </div>

                    <div class="mb-6">
                        <label for="playlistDescription"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.description_optional') }}
                        </label>
                        <textarea id="playlistDescription" name="description" rows="3"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all resize-none"
                            placeholder="{{ __('ui.add_description_to_playlist') }}"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeCreatePlaylistModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200 font-medium">
                            {{ __('ui.create_playlist') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

<script>
    const exploreTranslations = {
        playlist_created_success: @json(__('ui.playlist_created_success')) ,
        error_creating_playlist: @json(__('ui.error_creating_playlist')) ,
        no_playlists_available: @json(__('ui.no_playlists_available')) ,
        create_first_playlist: @json(__('ui.create_first_playlist')) ,
        video_added_to_playlist: @json(__('ui.video_added_to_playlist')) ,
        error_adding_video: @json(__('ui.error_adding_video')) ,
        added_to_watch_later: @json(__('ui.added_to_watch_later')) ,
        removed_from_watch_later: @json(__('ui.removed_from_watch_later')) ,
        error_removing_video: @json(__('ui.error_removing_video')) ,
        add_to_watch_later: @json(__('ui.add_to_watch_later')) ,
        added_to_watch_later_short: @json(__('ui.added_to_watch_later')) ,
        remove_from_watch_later: @json(__('ui.remove_from_watch_later')) ,
        video_label: @json(__('ui.video_short')) ,
    };

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
                            showToast(exploreTranslations.playlist_created_success, 'success');
                            closeCreatePlaylistModal();

                            // If we have a video to add, add it to the new playlist
                            if (window.currentVideoId) {
                                addVideoToPlaylist(data.playlist.id);
                            }
                        } else {
                            showToast(exploreTranslations.error_creating_playlist, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(exploreTranslations.error_creating_playlist, 'error');
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
                        <p class="text-gray-500 dark:text-gray-400 font-medium">${exploreTranslations.no_playlists_available}</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">${exploreTranslations.create_first_playlist}</p>
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
                            <div class="text-sm text-gray-500 dark:text-gray-400">${playlist.videos_count} ${exploreTranslations.video_label}</div>
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
                    showToast(exploreTranslations.video_added_to_playlist, 'success');
                    closeAddToPlaylistModal();
                } else {
                    showToast(data.message || exploreTranslations.error_adding_video, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(exploreTranslations.error_adding_video, 'error');
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
                        button.title = exploreTranslations.added_to_watch_later_short;
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
                    showToast(exploreTranslations.added_to_watch_later, 'success');
                } else {
                    showToast(data.message || exploreTranslations.error_adding_video, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(exploreTranslations.error_adding_video, 'error');
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
                        button.title = exploreTranslations.add_to_watch_later;
                        button.onclick = function(e) {
                            e.preventDefault();
                            addToWatchLater(videoId, this);
                        };
                    }
                    showToast(exploreTranslations.removed_from_watch_later, 'info');
                } else {
                    showToast(exploreTranslations.error_removing_video, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(exploreTranslations.error_removing_video, 'error');
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

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.id === 'addToPlaylistModal') {
            closeAddToPlaylistModal();
        }
        if (e.target.id === 'createPlaylistModal') {
            closeCreatePlaylistModal();
        }
    });
</script>
