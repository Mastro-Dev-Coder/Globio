<x-admin-layout>
    @php
        $title = 'Impostazioni FFmpeg - Amministrazione';

        $breadcrumbs = [];
        if (isset($breadcrumbs) && is_array($breadcrumbs)) {
            foreach ($breadcrumbs as $breadcrumb) {
                $breadcrumbs[] = $breadcrumb;
            }
        } else {
            $breadcrumbs = [
                ['name' => 'Amministrazione', 'url' => route('admin.dashboard')],
                ['name' => 'Impostazioni FFmpeg', 'url' => '#'],
            ];
        }

        $pageHeader = [
            'title' => 'Impostazioni FFmpeg e Video Processing',
            'subtitle' => 'Configurazione completa per il processamento video e transcodifica',
            'actions' => '<div class="flex items-center space-x-3">
                <button type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    onclick="testFFmpeg()">
                    <i class="fas fa-check-circle mr-2"></i>
                    Test FFmpeg
                </button>
                <button type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    onclick="migrateSettings()">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Migra da .env
                </button>
            </div>',
        ];
    @endphp

    <!-- Main Content -->
    <div class="space-y-6">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">Sono presenti errori:</h3>
                        <ul class="mt-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    @php
                        $activeTab = request()->get('tab', 'ffmpeg');
                    @endphp

                    <button onclick="switchTab('ffmpeg')" id="tab-ffmpeg"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'ffmpeg' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-video mr-2"></i>
                        FFmpeg
                    </button>

                    <button onclick="switchTab('qualities')" id="tab-qualities"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'qualities' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-medal mr-2"></i>
                        Qualità Video
                    </button>

                    <button onclick="switchTab('thumbnails')" id="tab-thumbnails"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'thumbnails' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-image mr-2"></i>
                        Thumbnail
                    </button>

                    <button onclick="switchTab('transcoding')" id="tab-transcoding"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'transcoding' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-cogs mr-2"></i>
                        Transcodifica
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab FFmpeg -->
                <div id="content-ffmpeg" class="tab-content {{ $activeTab === 'ffmpeg' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configurazione FFmpeg</h3>
                            <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span>Sistema Online</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.ffmpeg') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- FFmpeg Settings -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Configurazione Base</h4>

                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            FFmpeg Abilitato
                                        </label>
                                        <input type="checkbox" name="ffmpeg_enabled" value="1"
                                            {{ $settings['ffmpeg_enabled'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    </div>

                                    <div>
                                        <label for="ffmpeg_path"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Percorso FFmpeg
                                        </label>
                                        <input type="text" name="ffmpeg_path" value="{{ $settings['ffmpeg_path'] }}"
                                            placeholder="/usr/bin/ffmpeg"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Percorso completo al
                                            binario FFmpeg</p>
                                    </div>

                                    <div>
                                        <label for="ffprobe_path"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Percorso FFprobe
                                        </label>
                                        <input type="text" name="ffprobe_path"
                                            value="{{ $settings['ffprobe_path'] }}" placeholder="/usr/bin/ffprobe"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Percorso completo al
                                            binario FFprobe</p>
                                    </div>
                                </div>

                                <!-- Performance Settings -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Performance</h4>

                                    <div>
                                        <label for="ffmpeg_timeout"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Timeout (secondi)
                                        </label>
                                        <input type="number" name="ffmpeg_timeout"
                                            value="{{ $settings['ffmpeg_timeout'] }}" min="60" max="7200"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Timeout per operazioni
                                            FFmpeg (60-7200 secondi)</p>
                                    </div>

                                    <div>
                                        <label for="ffmpeg_threads"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Thread FFmpeg
                                        </label>
                                        <input type="number" name="ffmpeg_threads"
                                            value="{{ $settings['ffmpeg_threads'] }}" min="1" max="16"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Numero di thread per
                                            FFmpeg (1-16)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Impostazioni FFmpeg
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab Qualità Video -->
                <div id="content-qualities" class="tab-content {{ $activeTab === 'qualities' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Impostazioni Qualità Video
                            </h3>
                        </div>

                        <form action="{{ route('admin.settings.qualities') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Qualità Video</h4>

                                    <div>
                                        <label for="video_qualities"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Qualità Disponibili
                                        </label>
                                        <input type="text" name="video_qualities"
                                            value="{{ $settings['video_qualities'] }}"
                                            placeholder="720p,1080p,original"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Qualità separate da
                                            virgola</p>
                                    </div>

                                    <div>
                                        <label for="default_video_quality"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Qualità di Default
                                        </label>
                                        <select name="default_video_quality"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                            @foreach (explode(',', $settings['video_qualities']) as $quality)
                                                <option value="{{ trim($quality) }}"
                                                    {{ $settings['default_video_quality'] == trim($quality) ? 'selected' : '' }}>
                                                    {{ trim($quality) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Limiti di Sistema</h4>

                                    <div>
                                        <label for="max_video_duration"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Durata Massima (secondi)
                                        </label>
                                        <input type="number" name="max_video_duration"
                                            value="{{ $settings['max_video_duration'] }}" min="60"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Durata massima video
                                            consentita</p>
                                    </div>

                                    <div>
                                        <label for="max_file_size"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Dimensione Massima (KB)
                                        </label>
                                        <input type="number" name="max_file_size"
                                            value="{{ $settings['max_file_size'] }}" min="1024"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dimensione massima
                                            file video (in KB)</p>
                                    </div>

                                    <div>
                                        <label for="max_video_upload_mb"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Dimensione Massima Upload (MB)
                                        </label>
                                        <input type="number" name="max_video_upload_mb"
                                            value="{{ $settings['max_video_upload_mb'] }}" min="10" max="5000"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dimensione massima
                                            file video per l'upload (in MB)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Impostazioni Qualità
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab Thumbnail -->
                <div id="content-thumbnails" class="tab-content {{ $activeTab === 'thumbnails' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Impostazioni Thumbnail</h3>
                        </div>

                        <form action="{{ route('admin.settings.thumbnails') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label for="thumbnail_quality"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Qualità JPEG
                                    </label>
                                    <input type="number" name="thumbnail_quality"
                                        value="{{ $settings['thumbnail_quality'] }}" min="50" max="100"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Qualità immagine thumbnail
                                        (50-100)</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="thumbnail_width"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Larghezza (px)
                                    </label>
                                    <input type="number" name="thumbnail_width"
                                        value="{{ $settings['thumbnail_width'] }}" min="320" max="1920"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Larghezza thumbnail in pixel
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label for="thumbnail_height"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Altezza (px)
                                    </label>
                                    <input type="number" name="thumbnail_height"
                                        value="{{ $settings['thumbnail_height'] }}" min="240" max="1080"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Altezza thumbnail in pixel</p>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Impostazioni Thumbnail
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab Transcodifica -->
                <div id="content-transcoding" class="tab-content {{ $activeTab === 'transcoding' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Impostazioni Transcodifica
                            </h3>
                        </div>

                        <form action="{{ route('admin.settings.transcoding') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="enable_transcoding" value="1"
                                            {{ $settings['enable_transcoding'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                Abilita Transcodifica Automatica
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Transcodifica
                                                automaticamente i video in diverse qualità</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="auto_publish" value="1"
                                            {{ $settings['auto_publish'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                Pubblicazione Automatica
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Pubblica
                                                automaticamente i video dopo la transcodifica</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="delete_original_after_transcoding"
                                            value="1"
                                            {{ $settings['delete_original_after_transcoding'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                Elimina File Originale
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Elimina il file
                                                originale dopo la transcodifica per risparmiare spazio</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Impostazioni Transcodifica
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

<!-- Modal per risultati test -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Risultato Test FFmpeg</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testModalBody">
                <!-- Contenuto dinamico -->
            </div>
        </div>
    </div>
</div>

<script>
    // Funzioni globali per le tab
    function switchTab(tab) {
        try {
            // Nascondi tutti i tab content
            const allContents = document.querySelectorAll('.tab-content');
            allContents.forEach(content => {
                content.classList.add('hidden');
                content.style.display = 'none';
            });

            // Rimuovi stile active da tutti i tab buttons
            const allButtons = document.querySelectorAll('.tab-button');
            allButtons.forEach(button => {
                button.classList.remove('border-red-500', 'text-red-600', 'dark:text-red-400');
                button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Mostra il tab selezionato
            const targetContent = document.getElementById(`content-${tab}`);
            const targetButton = document.getElementById(`tab-${tab}`);

            if (targetContent && targetButton) {
                targetContent.classList.remove('hidden');
                targetContent.style.display = 'block';

                targetButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                targetButton.classList.add('border-red-500', 'text-red-600', 'dark:text-red-400');

                // Aggiorna URL senza ricaricare
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.pushState({
                    tab: tab
                }, '', url);
            } else {
                console.warn(`Tab elements not found for: ${tab}`);
            }
        } catch (error) {
            console.error('Error switching tab:', error);
        }
    }

    function testFFmpeg() {
        const modal = new bootstrap.Modal(document.getElementById('testModal'));
        const modalBody = document.getElementById('testModalBody');

        modalBody.innerHTML =
            '<div class="text-center p-4"><div class="spinner-border" role="status"></div><p class="mt-2">Testando FFmpeg...</p></div>';
        modal.show();

        fetch('{{ route('admin.settings.test') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                let html = '<div class="p-4">';
                if (data.ffmpeg.available && data.ffprobe.available) {
                    html +=
                        '<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-lg mb-4">';
                    html += '<h6><i class="fas fa-check-circle"></i> FFmpeg Funziona Correttamente</h6>';
                    html += '</div>';
                } else {
                    html +=
                        '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg mb-4">';
                    html += '<h6><i class="fas fa-exclamation-triangle"></i> Problemi con FFmpeg</h6>';
                    html += '</div>';
                }

                html += '<div class="grid grid-cols-2 gap-4">';
                html += '<div><strong>FFmpeg:</strong><br>';
                html +=
                    `Disponibile: ${data.ffmpeg.available ? '<span class="text-green-600">Sì</span>' : '<span class="text-red-600">No</span>'}<br>`;
                html += `Percorso: ${data.ffmpeg.path}<br>`;
                if (data.ffmpeg.version) {
                    html += `Versione: ${data.ffmpeg.version}`;
                }
                html += '</div>';

                html += '<div><strong>FFprobe:</strong><br>';
                html +=
                    `Disponibile: ${data.ffprobe.available ? '<span class="text-green-600">Sì</span>' : '<span class="text-red-600">No</span>'}<br>`;
                html += `Percorso: ${data.ffprobe.path}<br>`;
                if (data.ffprobe.version) {
                    html += `Versione: ${data.ffprobe.version}`;
                }
                html += '</div></div>';

                html += '</div>';
                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML =
                    '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg"><h6>Errore durante il test</h6><p>' +
                    error.message + '</p></div>';
            });
    }

    function migrateSettings() {
        if (confirm('Migrare le impostazioni da .env al database? Le impostazioni esistenti verranno sovrascritte.')) {
            fetch('{{ route('admin.settings.migrate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Errore: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Errore durante la migrazione: ' + error.message);
                });
        }
    }

    // Inizializzazione delle tab al caricamento della pagina
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza la tab attiva in base all'URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'ffmpeg';
        switchTab(activeTab);
    });
</script>
