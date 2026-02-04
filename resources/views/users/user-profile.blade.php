<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-8 mb-8 text-white">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @if ($user->userProfile && $user->userProfile->avatar_url)
                        <img src="{{ $user->userProfile->avatar_url }}" alt="{{ $user->name }}"
                            class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                    @else
                        <div
                            class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                            <span class="text-5xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Profile Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="mb-4">
                        <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-white/90 text-lg">
                            @if ($user->userProfile && $user->userProfile->channel_name)
                                {{ $user->userProfile->channel_name }}
                            @else
                                Canale di {{ $user->name }}
                            @endif
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $user->videos()->count() }}</div>
                            <div class="text-sm text-white/80">Video</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ number_format($user->videos()->sum('views_count')) }}
                            </div>
                            <div class="text-sm text-white/80">Visualizzazioni totali</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $user->subscribers()->count() }}</div>
                            <div class="text-sm text-white/80">Iscritti</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $user->subscriptions()->count() }}</div>
                            <div class="text-sm text-white/80">Iscrizioni</div>
                        </div>
                    </div>

                    <!-- Bio -->
                    @if ($user->userProfile && $user->userProfile->bio)
                        <p class="text-white/90 max-w-2xl">
                            {{ $user->userProfile->bio }}
                        </p>
                    @endif
                </div>

                <!-- Profile Actions (only for own profile) -->
                @auth
                    @if (auth()->id() === $user->id)
                        <div class="flex-shrink-0">
                            <a href="{{ route('users.settings') }}"
                                class="inline-flex items-center px-6 py-3 bg-white text-red-600 rounded-lg hover:bg-gray-100 transition-colors font-medium">
                                <i class="fas fa-cog mr-2"></i>
                                Modifica profilo
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Profile Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8">
                    <a href="#videos" class="py-4 px-1 border-b-2 border-red-600 text-red-600 font-medium text-sm">
                        <i class="fas fa-video mr-2"></i>Video
                    </a>
                    <a href="#playlists"
                        class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm">
                        <i class="fas fa-list mr-2"></i>Playlist
                    </a>
                    <a href="#about"
                        class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm">
                        <i class="fas fa-info-circle mr-2"></i>Informazioni
                    </a>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Videos Tab -->
            <div id="videos" class="tab-pane">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Video di {{ $user->name }}
                </h2>

                @if ($user->videos()->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($user->videos()->latest()->get() as $video)
                            <div class="group relative">
                                <a href="{{ route('videos.show', $video) }}">
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                                        <!-- Thumbnail -->
                                        <div class="relative aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                            @if ($video->thumbnail_url)
                                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-4xl text-gray-400"></i>
                                                </div>
                                            @endif

                                            @if ($video->duration)
                                                <div
                                                    class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-2 py-1 rounded">
                                                    {{ gmdate('i:s', $video->duration) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Video Info -->
                                        <div class="p-4">
                                            <h3
                                                class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">
                                                {{ $video->title }}
                                            </h3>

                                            <div
                                                class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>
                                                    <i class="fas fa-eye mr-1"></i>
                                                    {{ number_format($video->views_count) }}
                                                </span>
                                                <span>
                                                    {{ $video->published_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Video Actions -->
                                <div
                                    class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Watch Later Button -->
                                    <button
                                        class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors"
                                        title="Guarda più tardi" onclick="addToWatchLater({{ $video->id }}, this)">
                                        <i class="fas fa-clock text-xs"></i>
                                    </button>

                                    <!-- Add to Playlist Button -->
                                    <button
                                        class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center hover:bg-green-700 transition-colors"
                                        title="Aggiungi a playlist"
                                        onclick="showAddToPlaylistModal({{ $video->id }})">
                                        <i class="fas fa-list-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div
                            class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                            <i class="fas fa-video text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            Nessun video disponibile
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ auth()->id() === $user->id ? 'Carica il tuo primo video!' : 'Questo canale non ha ancora caricato video' }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Playlists Tab -->
            <div id="playlists" class="tab-pane hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Playlist di {{ $user->name }}
                    </h2>
                    @auth
                        @if (auth()->id() === $user->id)
                            <button onclick="showCreatePlaylistModal()"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Crea playlist
                            </button>
                        @endif
                    @endauth
                </div>

                @if ($user->playlists()->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($user->playlists()->latest()->get() as $playlist)
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                                <!-- Playlist Thumbnail -->
                                <div class="relative aspect-video bg-gray-200 dark:bg-gray-700">
                                    @if ($playlist->dynamic_thumbnail_url)
                                        <img src="{{ $playlist->dynamic_thumbnail_url }}"
                                            alt="{{ $playlist->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-list text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    <div
                                        class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="text-white text-center">
                                            <a href="{{ route('playlists.show', $playlist->id) }}" class="block">
                                                <i class="fas fa-play-circle text-5xl mb-2"></i>
                                                <p class="text-sm">{{ $playlist->videos->count() }} video</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Playlist Info -->
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                        <a href="{{ route('playlists.show', $playlist->id) }}" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                            {{ $playlist->name }}
                                        </a>
                                    </h3>

                                    @if ($playlist->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                            {{ $playlist->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-video mr-1"></i>
                                            {{ $playlist->videos->count() }} video
                                        </span>

                                        @auth
                                            @if (auth()->id() === $user->id)
                                                <div class="flex gap-2">
                                                    <button
                                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                                        onclick="editPlaylist({{ $playlist->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button
                                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                                        onclick="deletePlaylist({{ $playlist->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div
                            class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                            <i class="fas fa-list text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            Nessuna playlist
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ auth()->id() === $user->id ? 'Crea la tua prima playlist!' : 'Questo utente non ha ancora creato playlist' }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- About Tab -->
            <div id="about" class="tab-pane hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Informazioni sul canale
                </h2>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="space-y-6">
                        <!-- Channel Description -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                Descrizione del canale
                            </h3>
                            @if ($user->userProfile && $user->userProfile->bio)
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ $user->userProfile->bio }}
                                </p>
                            @else
                                <p class="text-gray-500 dark:text-gray-500 italic">
                                    Nessuna descrizione disponibile
                                </p>
                            @endif
                        </div>

                        <!-- Channel Stats -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                Statistiche del canale
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $user->videos()->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Video pubblicati</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($user->videos()->sum('views_count')) }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Visualizzazioni totali</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $user->subscribers()->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Iscritti</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $user->created_at->format('Y') }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Iscritto dal</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Playlist Modal -->
    <div id="createPlaylistModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Crea nuova playlist
                    </h3>
                    <button onclick="closeCreatePlaylistModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="createPlaylistForm">
                    <div class="mb-4">
                        <label for="playlistName"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome playlist
                        </label>
                        <input type="text" id="playlistName" name="name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Inserisci il nome della playlist" required>
                    </div>

                    <div class="mb-6">
                        <label for="playlistDescription"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione (opzionale)
                        </label>
                        <textarea id="playlistDescription" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Aggiungi una descrizione alla playlist"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeCreatePlaylistModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Annulla
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Crea playlist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add to Playlist Modal -->
    <div id="addToPlaylistModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Aggiungi a playlist
                    </h3>
                    <button onclick="closeAddToPlaylistModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div id="playlistList" class="space-y-2 mb-6">
                    <!-- Playlists will be loaded here -->
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="showCreatePlaylistModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nuova playlist
                    </button>
                    <button type="button" onclick="closeAddToPlaylistModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Chiudi
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layout>

<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('nav a');
        const tabContents = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active classes
                tabs.forEach(t => {
                    t.classList.remove('border-red-600', 'text-red-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Add active classes
                this.classList.add('border-red-600', 'text-red-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).classList.remove('hidden');
            });
        });
    });

    // Modal functions
    function showCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.remove('hidden');
    }

    function closeCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.add('hidden');
        document.getElementById('createPlaylistForm').reset();
    }

    function showAddToPlaylistModal(videoId) {
        // Store video ID for later use
        window.currentVideoId = videoId;
        document.getElementById('addToPlaylistModal').classList.remove('hidden');

        // Load user's playlists
        loadUserPlaylists();
    }

    function closeAddToPlaylistModal() {
        document.getElementById('addToPlaylistModal').classList.add('hidden');
    }

    // Create playlist form submission
    document.getElementById('createPlaylistForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/playlists', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Playlist creata con successo!');
                    closeCreatePlaylistModal();
                    location.reload(); // Refresh to show new playlist
                } else {
                    showToast('Errore nella creazione della playlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Errore nella creazione della playlist');
            });
    });

    // Load user playlists for add to playlist modal
    function loadUserPlaylists() {
        fetch('/playlists-data')
            .then(response => response.json())
            .then(data => {
                const playlistList = document.getElementById('playlistList');
                playlistList.innerHTML = '';

                if (data.playlists.length === 0) {
                    playlistList.innerHTML =
                        '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Nessuna playlist disponibile</p>';
                    return;
                }

                data.playlists.forEach(playlist => {
                    const button = document.createElement('button');
                    button.className =
                        'w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors';
                    button.innerHTML = `
                    <div class="font-medium text-gray-900 dark:text-white">${playlist.name}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">${playlist.videos_count} video</div>
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
                    showToast('Video aggiunto alla playlist!');
                    closeAddToPlaylistModal();
                } else {
                    showToast(data.message || 'Errore nell\'aggiungere il video');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Errore nell\'aggiungere il video');
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
                    // Change button appearance
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                    button.innerHTML = '<i class="fas fa-check text-xs"></i>';
                    button.title = 'Aggiunto a guarda più tardi';
                    showToast('Video aggiunto a guarda più tardi!');
                } else {
                    showToast(data.message || 'Errore nell\'aggiungere il video');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Errore nell\'aggiungere il video');
            });
    }

    // Toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className =
            'fixed top-4 right-4 bg-black/90 text-white px-6 py-3 rounded-lg z-50 transform translate-x-full transition-transform duration-300';
        toast.textContent = message;
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
            }, 300);
        }, 3000);
    }
</script>

<style>
    .tab-pane {
        display: none;
    }

    .tab-pane:not(.hidden) {
        display: block;
    }
</style>
