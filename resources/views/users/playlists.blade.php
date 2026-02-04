<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-2"></i>
                    <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-2"></i>
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-list mr-3 text-red-600"></i>Le mie playlist
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Organizza i tuoi video preferiti in playlist
            </p>
        </div>

        <!-- Create Playlist Button -->
        <div class="mb-8">
            <button onclick="openCreatePlaylistModal()"
                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Crea nuova playlist
            </button>
        </div>

        <!-- Playlists Grid -->
        @if (isset($playlists) && $playlists->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="playlistsGrid">
                @foreach ($playlists as $playlist)
                    <div class="playlist-item bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group"
                        data-playlist-id="{{ $playlist->id }}">
                        <!-- Playlist Thumbnail -->
                        <div class="relative aspect-video bg-gray-200 dark:bg-gray-700">
                            @if ($playlist->dynamic_thumbnail_url)
                                <img src="{{ $playlist->dynamic_thumbnail_url }}" alt="{{ $playlist->title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    @if ($playlist->videos->count() === 0)
                                        <i class="fas fa-list text-4xl text-gray-400"></i>
                                    @else
                                        <i class="fas fa-play text-4xl text-gray-400"></i>
                                    @endif
                                </div>
                            @endif

                            <div
                                class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="text-white text-center">
                                    @if ($playlist->videos->count() === 0)
                                        <i class="fas fa-list text-5xl mb-2"></i>
                                        <p class="text-sm">Playlist vuota</p>
                                    @else
                                        <a href="{{ route('playlists.show', $playlist->id) }}" class="block">
                                            <i class="fas fa-play-circle text-5xl mb-2"></i>
                                            <p class="text-sm">{{ $playlist->videos->count() }} video</p>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Playlist Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                <a href="{{ route('playlists.show', $playlist->id) }}"
                                    class="hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                    {{ $playlist->title }}
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

                                <div class="flex gap-2">
                                    <button
                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                        onclick="editPlaylist({{ $playlist->id }})" title="Modifica playlist">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                        onclick="deletePlaylist({{ $playlist->id }}, '{{ $playlist->title }}')"
                                        title="Elimina playlist">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-list text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Nessuna playlist
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Crea playlist per organizzare i tuoi video preferiti
                </p>
                <button onclick="openCreatePlaylistModal()"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Crea la tua prima playlist
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Playlist Modal -->
    <div id="editPlaylistModal" class="fixed inset-0 bg-gray-900/70 bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-edit mr-2 text-red-600"></i>
                        Modifica playlist
                    </h3>
                    <button onclick="closeEditPlaylistModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="#" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editPlaylistId" name="playlist_id">

                    <div class="mb-4">
                        <label for="editPlaylistTitle"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Titolo playlist *
                        </label>
                        <input type="text" id="editPlaylistTitle" name="title" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="mb-6">
                        <label for="editPlaylistDescription"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione (opzionale)
                        </label>
                        <textarea id="editPlaylistDescription" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditPlaylistModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-close"></i>
                            Annulla
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-edit"></i>
                            Salva modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePlaylistModal" class="fixed inset-0 bg-gray-900/70 bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                        Conferma eliminazione
                    </h3>
                    <button onclick="closeDeletePlaylistModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-gray-600 dark:text-gray-400">
                        Sei sicuro di voler eliminare questa playlist? Questa azione non può essere annullata.
                    </p>
                    <p id="deletePlaylistName" class="font-semibold text-gray-900 dark:text-white mt-2">
                        <!-- Nome playlist caricato via JavaScript -->
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeletePlaylistModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-close"></i>
                        Annulla
                    </button>
                    <button type="button" onclick="confirmDeletePlaylist()"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash"></i>
                        Elimina
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Playlist Modal -->
    <div id="createPlaylistModal" class="fixed inset-0 bg-gray-900/70 bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-plus mr-2 text-red-600"></i>
                        Crea nuova playlist
                    </h3>
                    <button onclick="closeCreatePlaylistModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('playlists.store') }}" method="POST" id="createForm">
                    @csrf
                    <div class="mb-4">
                        <label for="createPlaylistTitle"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Titolo playlist *
                        </label>
                        <input type="text" id="createPlaylistTitle" name="title" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Inserisci il titolo della playlist">
                    </div>

                    <div class="mb-6">
                        <label for="createPlaylistDescription"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione (opzionale)
                        </label>
                        <textarea id="createPlaylistDescription" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Aggiungi una descrizione alla playlist"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeCreatePlaylistModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-close"></i>
                            Annulla
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-save"></i>
                            Crea playlist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

<script>
    // Create Playlist Modal Functions
    function openCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.remove('hidden');
    }

    function closeCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.add('hidden');
        document.getElementById('createForm').reset();
    }

    // Cancel edit
    function cancelEdit() {
        document.getElementById('editSection').classList.add('hidden');
        document.getElementById('editForm').reset();
    }

    // Edit playlist
    function editPlaylist(playlistId) {
        // Fetch playlist data using the existing playlists-data endpoint
        fetch(`/playlists-data?id=${playlistId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.playlists.length > 0) {
                    showEditPlaylistModal(data.playlists[0]);
                } else {
                    alert('Playlist non trovata');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore nel caricamento della playlist');
            });
    }

    // Delete playlist
    function deletePlaylist(playlistId, playlistTitle) {
        showDeletePlaylistModal(playlistId, playlistTitle);
    }

    // Edit Playlist Modal Functions
    function showEditPlaylistModal(playlist) {
        document.getElementById('editPlaylistId').value = playlist.id;
        document.getElementById('editPlaylistTitle').value = playlist.title;
        document.getElementById('editPlaylistDescription').value = playlist.description || '';
        document.getElementById('editForm').action = `/playlists/${playlist.id}`;
        document.getElementById('editPlaylistModal').classList.remove('hidden');
    }

    function closeEditPlaylistModal() {
        document.getElementById('editPlaylistModal').classList.add('hidden');
        document.getElementById('editForm').reset();
    }

    // Delete Playlist Modal Functions
    let playlistToDelete = null;

    function showDeletePlaylistModal(playlistId, playlistTitle) {
        playlistToDelete = playlistId;
        document.getElementById('deletePlaylistName').textContent = `"${playlistTitle}"`;
        document.getElementById('deletePlaylistModal').classList.remove('hidden');
    }

    function closeDeletePlaylistModal() {
        document.getElementById('deletePlaylistModal').classList.add('hidden');
        playlistToDelete = null;
    }

    function confirmDeletePlaylist() {
        if (playlistToDelete) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/playlists/${playlistToDelete}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = csrfToken;

            form.appendChild(methodField);
            form.appendChild(csrfField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modals when clicking outside
    document.getElementById('editPlaylistModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditPlaylistModal();
        }
    });

    document.getElementById('deletePlaylistModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeletePlaylistModal();
        }
    });

    document.getElementById('createPlaylistModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreatePlaylistModal();
        }
    });
</script>
