<div class="group cursor-pointer relative p-1 sm:p-2 rounded-xl w-full max-w-full" data-color-wrapper-trending>
    <a href="{{ route('videos.show', $video) }}"
        @if (isset($nextVideo)) data-next-video-url="{{ route('videos.show', $nextVideo) }}"
           data-next-video-poster="{{ $nextVideo->thumbnail_path ? asset('storage/' . $nextVideo->thumbnail_path) : '' }}"
           data-next-video-title="{{ $nextVideo->title }}" @endif>
        <div class="relative overflow-hidden w-full rounded-xl bg-gray-100 dark:bg-gray-800 aspect-video mb-3 transition-all duration-300"
            data-thumbnail-trending style="--hover-bg: rgba(255, 0, 0, 0.35);">
            @if ($video->thumbnail_path)
                <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}"
                    class="w-full h-full object-cover transition-transform duration-300">
            @else
                <div
                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                    <div class="text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-white/30 dark:bg-black/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-video text-2xl text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Video</p>
                    </div>
                </div>
            @endif

            <!-- Duration Badge -->
            <div
                class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2.5 py-1.5 rounded-lg font-medium border border-white/10">
                {{ $video->formatted_duration }}
            </div>

            <!-- Reel Badge -->
            @if ($video->is_reel)
            @endif

            <!-- Hover Play Button -->
            <div
                class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                <div
                    class="w-16 h-16 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 border border-white/30 shadow-2xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </div>
            </div>

            <!-- Professional Action Buttons -->
            @auth
                <div
                    class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                    <!-- Watch Later Button -->
                    <livewire:video-watch-later :video="$video" :compact="true" />

                    <!-- Add to Playlist Button -->
                    <button
                        class="w-9 h-9 bg-gray-900/70 text-white rounded-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl cursor-pointer"
                        title="Aggiungi a Playlist"
                        onclick="event.preventDefault(); showAddToPlaylistModal({{ $video->id }})">
                        <i class="fas fa-folder-plus text-sm"></i>
                    </button>
                </div>
            @endauth
        </div>

        <div class="space-y-2 sm:space-y-3">
            <div class="flex flex-col space-y-1 sm:space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex space-x-1 sm:space-x-2">
                    <img src="{{ asset('storage/' . $video->user->userProfile->avatar_url) }}"
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover"
                        alt="{{ $video->user->userProfile?->channel_name }}">

                    <div class="flex flex-col">
                        <h3
                            class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors duration-200">
                            <span class="block truncate">{{ $video->title }}</span>
                        </h3>
                        <a href="{{ route('channel.show', $video->user->userProfile?->channel_name) }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors font-medium text-xs sm:text-sm">
                            {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-eye text-gray-400"></i>
                        <span>{{ number_format($video->views_count) }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $video->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Professional Add to Playlist Modal -->
<div id="addToPlaylistModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl p-6 w-full max-w-md shadow-2xl border border-white/20">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-900/70 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder-plus text-white text-sm"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Aggiungi a Playlist
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
                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-medium flex items-center justify-center gap-2">
                    <i class="fas fa-plus text-sm"></i>
                    Nuova Playlist
                </button>
                <button type="button" onclick="closeAddToPlaylistModal()"
                    class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                    <i class="fas fa-close"></i>
                    Chiudi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Create Playlist Modal -->
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
                        Crea Nuova Playlist
                    </h3>
                </div>
                <button onclick="closeCreatePlaylistModal()"
                    class="w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-600 dark:text-gray-300 text-sm"></i>
                </button>
            </div>

            <form id="createPlaylistForm">
                <div class="mb-5">
                    <label for="playlistName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome Playlist
                    </label>
                    <input type="text" id="playlistName" name="name"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all"
                        placeholder="Inserisci il nome della playlist" required>
                </div>

                <div class="mb-6">
                    <label for="playlistDescription"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descrizione (opzionale)
                    </label>
                    <textarea id="playlistDescription" name="description" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all resize-none"
                        placeholder="Aggiungi una descrizione alla playlist"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCreatePlaylistModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                        Annulla
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200 font-medium">
                        Crea Playlist
                    </button>
                </div>
            </form>
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
                            showToast('Playlist creata con successo!', 'success');
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
                            <div class="font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">${playlist.name}</div>
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
                            'w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl flex items-center justify-center hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl';
                        button.innerHTML = '<i class="fas fa-check text-base"></i>';
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
                            'w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl flex items-center justify-center hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl';
                        button.innerHTML = '<i class="fas fa-bookmark text-base"></i>';
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
            toast.style.transform = 'translateX(full)';
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

    // Colored hover effect for trending videos
    function initializeColoredHoverTrending() {
        document.querySelectorAll("[data-thumbnail-trending]").forEach(card => {
            const wrapper = card.closest("[data-color-wrapper-trending]");
            const img = card.querySelector("img");

            if (!img || !wrapper) return;

            img.addEventListener("load", () => {
                const color = getDominantColorTrending(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            });

            // If image is already loaded
            if (img.complete && img.naturalHeight !== 0) {
                const color = getDominantColorTrending(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            }
        });

        function getDominantColorTrending(image) {
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
                r: r / count,
                g: g / count,
                b: b / count
            };
        }
    }

    // Initialize colored hover effect when DOM is ready
    document.addEventListener("DOMContentLoaded", initializeColoredHoverTrending);
</script>
