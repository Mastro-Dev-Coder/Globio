<x-admin-layout>
    <div class="space-y-6">
        <!-- Approval Status Alert -->
        @if(!\App\Models\Setting::getValue('require_approval', false))
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Approvazione Admin Disabilitata
                        </h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                            L'approvazione da parte dell'amministratore è attualmente disabilitata. 
                            I video vengono pubblicati automaticamente.
                            <a href="{{ route('admin.settings') }}" class="underline hover:text-blue-800 dark:hover:text-blue-200">
                                Modifica impostazioni
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <i class="fas fa-video text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Video Totali</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $videos->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pubblicati</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Video::published()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Elaborazione</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Video::where('status', 'processing')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Respinti</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Video::where('status', 'rejected')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.videos-management') }}" class="flex items-center space-x-4">
                <input type="text" name="search" placeholder="Cerca video..." value="{{ $search ?? '' }}"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Tutti gli stati</option>
                    <option value="processing" {{ ($status ?? '') === 'processing' ? 'selected' : '' }}>In Elaborazione
                    </option>
                    <option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Pubblicati
                    </option>
                    <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Respinti</option>
                    <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Bozze</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Cerca
                </button>
            </form>
        </div>

        <!-- Videos Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lista Video</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @if(\App\Models\Setting::getValue('require_approval', false))
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAllVideos" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                </th>
                            @endif
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Video
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Autore
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stato
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Visualizzazioni
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Durata
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Caricato
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($videos as $video)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                @if(\App\Models\Setting::getValue('require_approval', false))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="selected_videos[]" value="{{ $video->id }}" 
                                               class="video-checkbox rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded overflow-hidden flex-shrink-0">
                                            @if ($video->thumbnail_path)
                                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">
                                                {{ $video->title }}
                                            </div>
                                            @if ($video->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                                                    {{ Str::limit($video->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($video->user->userprofile->avatar_url)
                                            <img src="{{ asset('storage/' . $video->user->userprofile->avatar_url) }}"
                                                class="w-10 h-10 rounded-full" alt="{{ $video->user->name }}">
                                        @else
                                            <span class="text-white text-sm font-medium">
                                                {{ strtoupper(substr($video->user->name, 0, 1)) }}
                                            </span>
                                        @endif
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $video->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $video->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($video->status === 'published') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                        @elseif($video->status === 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                        @elseif($video->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        @if ($video->status === 'processing')
                                            <i class="fas fa-spinner fa-spin mr-1"></i>
                                        @elseif($video->status === 'published')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @elseif($video->status === 'rejected')
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @endif
                                        {{ ucfirst($video->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($video->views_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if ($video->duration)
                                        {{ gmdate('H:i:s', $video->duration) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $video->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if (\App\Models\Setting::getValue('require_approval', false) && $video->status === 'processing')
                                            <form method="POST" action="{{ route('admin.videos.moderate', $video) }}"
                                                class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                    title="Approva">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('admin.videos.moderate', $video) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="reason"
                                                    value="Video respinto dall'amministratore">
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                    title="Rifiuta">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($video->status === 'published')
                                            <a href="{{ route('videos.show', $video) }}" target="_blank"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                                title="Visualizza">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('admin.videos.delete', $video) }}"
                                            class="inline"
                                            onsubmit="return confirm('Sei sicuro di voler eliminare questo video?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ \App\Models\Setting::getValue('require_approval', false) ? '8' : '7' }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-video text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Nessun video trovato</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(\App\Models\Setting::getValue('require_approval', false))
                <!-- Bulk Actions Bar -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-700 hidden" id="bulkActionsBar">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700 dark:text-gray-300" id="selectedCount">
                                0 video selezionati
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="bulkApproveVideos()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check mr-2"></i>
                                Approva Selezionati
                            </button>
                            <button type="button" onclick="bulkRejectVideos()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times mr-2"></i>
                                Rifiuta Selezionati
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($videos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $videos->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        // Gestione selezione multipla (solo se l'approvazione è abilitata)
        @if(\App\Models\Setting::getValue('require_approval', false))
            document.getElementById('selectAllVideos').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.video-checkbox');
                const bulkBar = document.getElementById('bulkActionsBar');
                const selectedCount = document.getElementById('selectedCount');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                updateBulkActions();
            });

            // Gestione checkbox individuali
            document.querySelectorAll('.video-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });
        @endif

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.video-checkbox:checked');
            const bulkBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');

            if (checkboxes.length > 0) {
                bulkBar.classList.remove('hidden');
                selectedCount.textContent = checkboxes.length + ' video selezionati';
            } else {
                bulkBar.classList.add('hidden');
            }

            // Aggiorna anche il checkbox "Selecta Tutto"
            const selectAllCheckbox = document.getElementById('selectAllVideos');
            const totalCheckboxes = document.querySelectorAll('.video-checkbox').length;
            selectAllCheckbox.checked = checkboxes.length === totalCheckboxes;
            selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < totalCheckboxes;
        }

        function getSelectedVideoIds() {
            const checkboxes = document.querySelectorAll('.video-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        async function bulkApproveVideos() {
            const videoIds = getSelectedVideoIds();
            if (videoIds.length === 0) {
                alert('Seleziona almeno un video da approvare.');
                return;
            }

            if (!confirm(`Sei sicuro di voler approvare ${videoIds.length} video?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.videos.bulk-approve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        video_ids: videoIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Errore: ' + (data.message || 'Errore sconosciuto'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Errore di connessione. Riprova.');
            }
        }

        async function bulkRejectVideos() {
            const videoIds = getSelectedVideoIds();
            if (videoIds.length === 0) {
                alert('Seleziona almeno un video da rifiutare.');
                return;
            }

            const reason = prompt('Motivo del rifiuto (opzionale):', 'Video respinti in gruppo dall\'amministratore');
            if (reason === null) {
                return; // Annullato dall'utente
            }

            if (!confirm(`Sei sicuro di voler rifiutare ${videoIds.length} video?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.videos.bulk-reject") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        video_ids: videoIds,
                        reason: reason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Errore: ' + (data.message || 'Errore sconosciuto'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Errore di connessione. Riprova.');
            }
        }
    </script>
</x-admin-layout>
