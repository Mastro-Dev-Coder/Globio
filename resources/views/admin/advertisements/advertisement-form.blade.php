<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    @if ($advertisement)
                        Modifica la pubblicità "{{ $advertisement->name }}"
                    @else
                        Crea una nuova pubblicità per il sito
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.advertisements') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                Torna alla lista
            </a>
        </div>

        <form method="POST"
            action="{{ $advertisement ? route('admin.advertisements.update', $advertisement) : route('admin.advertisements.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($advertisement)
                @method('PUT')
            @endif

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informazioni Base
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Pubblicità <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', ($advertisement ? $advertisement->name : '') ?? '') }}"
                            required placeholder="es: Banner Header Principale"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo Pubblicità <span class="text-red-500">*</span>
                        </label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Seleziona tipo</option>
                            <option value="banner"
                                {{ old('type', ($advertisement ? $advertisement->type : '') ?? '') === 'banner' ? 'selected' : '' }}>
                                Banner (Immagine statica)
                            </option>
                            <option value="adsense"
                                {{ old('type', ($advertisement ? $advertisement->type : '') ?? '') === 'adsense' ? 'selected' : '' }}>
                                Google AdSense
                            </option>
                            <option value="video"
                                {{ old('type', ($advertisement ? $advertisement->type : '') ?? '') === 'video' ? 'selected' : '' }}>
                                Video Advertisement
                            </option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Posizione <span class="text-red-500">*</span>
                        </label>
                        <select name="position" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Seleziona posizione</option>
                            <option value="footer"
                                {{ old('position', ($advertisement ? $advertisement->position : '') ?? '') === 'footer' ? 'selected' : '' }}>
                                Footer (Piè di pagina)
                            </option>
                            <option value="between_videos"
                                {{ old('position', ($advertisement ? $advertisement->position : '') ?? '') === 'between_videos' ? 'selected' : '' }}>
                                Tra i Video
                            </option>
                            <option value="home_video"
                                {{ old('position', ($advertisement ? $advertisement->position : '') ?? '') === 'home_video' ? 'selected' : '' }}>
                                Video Home
                            </option>
                            <option value="video_overlay"
                                {{ old('position', ($advertisement ? $advertisement->position : '') ?? '') === 'video_overlay' ? 'selected' : '' }}>
                                Overlay Video
                            </option>
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priorità (0-100)
                        </label>
                        <input type="number" name="priority" min="0" max="100"
                            value="{{ old('priority', ($advertisement ? $advertisement->priority : 0) ?? 0) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Maggiore è il numero, maggiore sarà la priorità di visualizzazione
                        </p>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', ($advertisement ? $advertisement->is_active : true) ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Pubblicità attiva
                        </label>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-edit mr-2"></i>
                    Contenuto Pubblicitario
                </h3>

                <div class="space-y-6">
                    <!-- Banner/Video Fields -->
                    <div id="banner-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Immagine Banner
                            </label>
                            @if ($advertisement && $advertisement->image_url)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $advertisement->image_url) }}" alt="Banner attuale"
                                        class="max-w-xs h-auto rounded border">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Immagine attuale</p>
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Formati supportati: JPG, PNG, GIF. Dimensione massima: 2MB
                            </p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Link di destinazione
                            </label>
                            <input type="url" name="link_url"
                                value="{{ old('link_url', ($advertisement ? $advertisement->link_url : '') ?? '') }}"
                                placeholder="https://example.com"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('link_url')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Testo alternativo (per accessibilità)
                            </label>
                            <textarea name="content" rows="3" placeholder="Descrizione del banner..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">{{ old('content', ($advertisement ? $advertisement->content : '') ?? '') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- AdSense Fields -->
                    <div id="adsense-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Codice AdSense
                            </label>
                            <textarea name="code" rows="8" placeholder="Incolla qui il codice AdSense..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono text-sm">{{ old('code', ($advertisement ? $advertisement->code : '') ?? '') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Incolla il codice completo fornito da Google AdSense
                            </p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Video Fields -->
                    <div id="video-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Codice Video Advertisement
                            </label>
                            <textarea name="code" rows="8" placeholder="Codice HTML5 video o iframe..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono text-sm">{{ old('code', ($advertisement ? $advertisement->code : '') ?? '') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Codice HTML per video advertisement (es. YouTube, Vimeo, etc.)
                            </p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Durata video (in secondi)
                            </label>
                            <input type="number" name="video_duration" min="5" max="300"
                                value="{{ old('video_duration', $advertisement->content ? json_decode($advertisement->content, true)['duration'] ?? '' : '') }}"
                                placeholder="es: 30"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Durata del video advertisement in secondi
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calendar mr-2"></i>
                    Programmazione
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data di inizio
                        </label>
                        <input type="datetime-local" name="start_date"
                            value="{{ old('start_date', $advertisement && $advertisement->start_date ? $advertisement->start_date->format('Y-m-d\TH:i') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Lascia vuoto per attivazione immediata
                        </p>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data di fine
                        </label>
                        <input type="datetime-local" name="end_date"
                            value="{{ old('end_date', $advertisement->end_date ? $advertisement->end_date->format('Y-m-d\TH:i') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Lascia vuoto per nessuna scadenza
                        </p>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.advertisements') }}"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Annulla
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save w-4 h-4 mr-2"></i>
                    {{ $advertisement ? 'Aggiorna' : 'Crea' }} Pubblicità
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.querySelector('select[name="type"]');
                const bannerFields = document.getElementById('banner-fields');
                const adsenseFields = document.getElementById('adsense-fields');
                const videoFields = document.getElementById('video-fields');

                function updateFields() {
                    const selectedType = typeSelect.value;

                    // Nascondi tutti i campi
                    if (bannerFields) bannerFields.style.display = 'none';
                    if (adsenseFields) adsenseFields.style.display = 'none';
                    if (videoFields) videoFields.style.display = 'none';

                    // Mostra i campi appropriati
                    if (selectedType === 'banner') {
                        if (bannerFields) bannerFields.style.display = 'block';
                    } else if (selectedType === 'adsense') {
                        if (adsenseFields) adsenseFields.style.display = 'block';
                    } else if (selectedType === 'video') {
                        if (videoFields) videoFields.style.display = 'block';
                    }
                }

                // Verifica che tutti gli elementi esistano
                if (typeSelect && bannerFields && adsenseFields && videoFields) {
                    typeSelect.addEventListener('change', updateFields);
                    updateFields(); // Chiamata iniziale
                } else {
                    console.error('Elementi del form non trovati:', {
                        typeSelect: !!typeSelect,
                        bannerFields: !!bannerFields,
                        adsenseFields: !!adsenseFields,
                        videoFields: !!videoFields
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>
