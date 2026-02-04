<div>
    <div class="max-w-4xl mx-auto">
        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                <i class="fas fa-cloud-upload-alt mr-2" style="color: var(--primary-color);"></i>
                Carica un nuovo video
            </h2>

            <form id="video-upload-form" class="space-y-6" enctype="multipart/form-data" method="POST">
                @csrf <!-- ATTENZIONE: il token DEVE essere dentro il form -->

                <!-- Video File -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        File Video *
                    </label>

                    <!-- Upload Area: contiene placeholder, video preview, overlay acqua + input sempre presente -->
                    <div id="upload-area" class="block">
                        <div class="flex items-center justify-center w-full">
                            <label id="upload-label"
                                class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors overflow-hidden">

                                <!-- MEDIA WRAPPER: contiene video e placeholder -->
                                <div id="media-wrapper" class="w-full h-full relative">

                                    <!-- Video element (hidden finché non caricato) -->
                                    <video id="video-element" class="w-full h-full object-cover rounded-lg hidden"
                                        controls preload="metadata"></video>

                                    <!-- Placeholder visibile prima della selezione -->
                                    <div id="placeholder"
                                        class="absolute inset-0 flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-semibold">Clicca per caricare</span> o trascina qui
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            MP4, AVI, MOV, WMV, FLV, WebM (MAX.
                                            {{ \App\Models\Setting::getValue('max_video_upload_mb', 500) }}MB)
                                        </p>
                                    </div>

                                    <!-- WATER LAYER: sovrapposizione dell'acqua che sale (inizialmente nascosta) -->
                                    <div id="water-layer" class="absolute inset-0 pointer-events-none hidden">
                                        <!-- riempimento acqua (altezza variabile) -->
                                        <div id="water-fill" class="absolute left-0 bottom-0 w-full" style="height:0%;">
                                            <!-- gradient + onda svg -->
                                            <div class="water-fill-inner absolute inset-0">
                                                <!-- la wave svg come background -->
                                            </div>
                                        </div>

                                        <!-- Percentuale centrata -->
                                        <div id="water-percent"
                                            class="absolute inset-0 flex items-center justify-center text-white text-2xl font-bold select-none">
                                            0%
                                        </div>
                                    </div>
                                </div>

                                <!-- INPUT file: IMPORTANTISSIMO rimane sempre nel DOM -->
                                <input id="video-file" name="video_file" type="file" class="hidden"
                                    accept="video/*" />
                            </label>
                        </div>
                    </div>

                    <!-- Video Preview Info (manteniamo per dettagli) -->
                    <div id="video-info" class="hidden mt-4 space-y-4">
                        <div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <div
                                class="absolute top-2 right-2 bg-black bg-opacity-60 text-white px-2 py-1 rounded text-xs">
                                <span id="file-size">0 MB</span>
                            </div>
                        </div>

                        <!-- File Info -->
                        <div
                            class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-800 dark:text-green-400">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        File selezionato: <span id="file-name">N/A</span>
                                    </p>
                                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                                        Formato: <span id="file-format">N/A</span> |
                                        Dimensione: <span id="file-dimension">0 MB</span> |
                                        Tempo stimato: <span id="processing-time">N/A</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message"
                        class="hidden mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-400">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="error-text"></span>
                        </p>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Miniatura
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <img id="thumbnail-preview" src="" alt="Thumbnail preview"
                                    class="hidden w-full h-full object-cover rounded">
                                <div id="thumbnail-placeholder">
                                    <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold">Carica miniatura</span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        JPG, PNG (consigliato 1280x720)
                                    </p>
                                </div>
                            </div>
                            <input id="thumbnail" name="thumbnail" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>

                <!-- Title e Descrizione in riga -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Titolo *
                        </label>
                        <input id="title" name="title" type="text"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color);"
                            placeholder="Inserisci un titolo accattivante" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color);" placeholder="Descrivi il tuo video..."></textarea>
                    </div>
                </div>

                <!-- Tag e Lingua in riga -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tag (separati da virgola)
                        </label>
                        <input id="tags" name="tags" type="text"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color);" placeholder="gaming, tutorial, vlog">
                    </div>

                    <!-- Language -->
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lingua
                        </label>
                        <select id="language" name="language"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color);">
                            <option value="it">Italiano</option>
                            <option value="en">Inglese</option>
                            <option value="es">Spagnolo</option>
                            <option value="fr">Francese</option>
                            <option value="de">Tedesco</option>
                        </select>
                    </div>
                </div>

                <!-- Tipo Video (Reel o Normale) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo di Video
                    </label>
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i id="video-type-icon" class="fas fa-video mr-3 text-blue-600 dark:text-blue-400"></i>
                                <div>
                                    <p id="video-type-text" class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                        Tipo di video: <span id="detected-type">In attesa...</span>
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        <i class="fas fa-magic mr-1"></i>
                                        Rilevamento automatico basato sulla risoluzione del video
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span id="auto-detect-indicator" class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-full text-xs font-medium">
                                    Auto
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label
                            class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="radio" name="is_reel" value="0" checked
                                class="h-4 w-4 border-gray-300 focus:ring-2"
                                style="color: var(--primary-color); --tw-ring-color: var(--primary-color);">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="fas fa-desktop mr-2"></i>Video Normale
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    Formato orizzontale (16:9, 4:3)
                                </span>
                            </div>
                        </label>

                        <label
                            class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="radio" name="is_reel" value="1"
                                class="h-4 w-4 border-gray-300 focus:ring-2"
                                style="color: var(--primary-color); --tw-ring-color: var(--primary-color);">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="fas fa-mobile-alt mr-2"></i>Reel
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    Formato verticale (9:16, 3:4)
                                </span>
                            </div>
                        </label>
                    </div>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Il tipo di video viene rilevato automaticamente in base alla risoluzione. Puoi modificarlo manualmente se necessario.
                    </p>
                </div>

                <!-- Stato (Visibilità) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stato di pubblicazione
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label
                            class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="radio" name="is_public" value="1" checked
                                class="h-4 w-4 border-gray-300 focus:ring-2"
                                style="color: var(--primary-color); --tw-ring-color: var(--primary-color);">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="fas fa-globe mr-2"></i>Pubblico
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    Tutti possono vedere il video
                                </span>
                            </div>
                        </label>

                        <label
                            class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="radio" name="is_public" value="0"
                                class="h-4 w-4 border-gray-300 focus:ring-2"
                                style="color: var(--primary-color); --tw-ring-color: var(--primary-color);">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="fas fa-lock mr-2"></i>Privato
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    Solo tu puoi vedere il video
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Success Message -->
                <div id="success-message"
                    class="hidden p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="success-text">Video caricato con successo!</span>
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div
                    class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('home') }}"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Annulla
                    </a>
                    <button id="submit-btn" type="submit"
                        class="px-6 py-3 text-white rounded-lg hover:opacity-90 transition-all font-medium shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background: linear-gradient(to right, var(--primary-color), var(--primary-color-dark));">
                        <span id="submit-text">
                            <i class="fas fa-upload mr-2"></i>Carica Video
                        </span>
                        <span id="submit-loading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Caricamento...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('video-upload-form');
            const videoFile = document.getElementById('video-file');
            const uploadArea = document.getElementById('upload-area');
            const uploadLabel = document.getElementById('upload-label');
            const mediaWrapper = document.getElementById('media-wrapper');
            const videoPreviewPanel = document.getElementById('video-info');
            const videoElement = document.getElementById('video-element');
            const placeholder = document.getElementById('placeholder');
            const waterLayer = document.getElementById('water-layer');
            const waterFill = document.getElementById('water-fill');
            const waterPercent = document.getElementById('water-percent');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            videoFile.addEventListener('change', handleVideoFileSelect);

            uploadLabel.addEventListener('dragover', handleDragOver);
            uploadLabel.addEventListener('dragleave', handleDragLeave);
            uploadLabel.addEventListener('drop', handleDrop);

            form.addEventListener('submit', handleFormSubmit);

            function handleVideoFileSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    validateAndPreviewVideo(file);
                }
            }

            function handleDragOver(e) {
                e.preventDefault();
                uploadLabel.classList.add('bg-gray-100', 'dark:bg-gray-600');
            }

            function handleDragLeave(e) {
                e.preventDefault();
                uploadLabel.classList.remove('bg-gray-100', 'dark:bg-gray-600');
            }

            function handleDrop(e) {
                e.preventDefault();
                uploadLabel.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('video/')) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        videoFile.files = dataTransfer.files;
                        validateAndPreviewVideo(file);
                    }
                }
            }

            function validateAndPreviewVideo(file) {
                const allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv', 'video/x-flv',
                    'video/webm'
                ];

                // Ottieni la dimensione massima dal server o usa 500MB come default
                const maxSizeMb = {{ \App\Models\Setting::getValue('max_video_upload_mb', 500) }};
                const maxSize = maxSizeMb * 1024 * 1024;

                const mime = file.type.toLowerCase();

                if (!allowedTypes.some(t => mime.indexOf(t.split('/')[1] || t) !== -1 && (t.includes('/') ? mime ===
                        t : true)) && !mime.startsWith('video/')) {
                    showError('Formato video non supportato. Usa MP4, AVI, MOV, WMV, FLV o WebM.');
                    return;
                }

                if (file.size > maxSize) {
                    showError('Il file è troppo grande. Massimo ' + maxSizeMb + 'MB consentiti.');
                    return;
                }

                const videoUrl = URL.createObjectURL(file);
                videoElement.src = videoUrl;
                videoElement.classList.remove('hidden');
                placeholder.classList.add('hidden');

                // Rileva automaticamente il tipo di video
                detectVideoType(file);

                const fileSize = formatFileSize(file.size);
                document.getElementById('file-size').textContent = fileSize;
                document.getElementById('file-name').textContent = file.name;
                document.getElementById('file-format').textContent = (file.type.split('/')[1] || 'N/A')
                    .toUpperCase();
                document.getElementById('file-dimension').textContent = fileSize;
                document.getElementById('processing-time').textContent = calculateProcessingTime(file.size);

                videoPreviewPanel.classList.remove('hidden');
                hideError();
            }

            // Funzione per rilevare automaticamente il tipo di video
            function detectVideoType(file) {
                const video = document.createElement('video');
                video.preload = 'metadata';
                
                video.onloadedmetadata = function() {
                    const width = video.videoWidth;
                    const height = video.videoHeight;
                    
                    // Determina se è un reel basato sulla risoluzione
                    const isReel = height > width || (width / height) <= 0.8;
                    
                    updateVideoTypeUI(isReel, width, height);
                    
                    // Pulisci l'URL dell'oggetto
                    URL.revokeObjectURL(videoUrl);
                };
                
                video.onerror = function() {
                    // In caso di errore, mantieni il valore di default
                    updateVideoTypeUI(false, 0, 0);
                };
                
                const videoUrl = URL.createObjectURL(file);
                video.src = videoUrl;
            }

            // Funzione per aggiornare l'interfaccia utente del tipo video
            function updateVideoTypeUI(isReel, width, height) {
                const videoTypeText = document.getElementById('detected-type');
                const videoTypeIcon = document.getElementById('video-type-icon');
                const autoDetectIndicator = document.getElementById('auto-detect-indicator');
                
                if (isReel) {
                    videoTypeText.textContent = 'Reel (Verticale)';
                    videoTypeIcon.className = 'fas fa-mobile-alt mr-3 text-purple-600 dark:text-purple-400';
                    autoDetectIndicator.textContent = 'Auto-Reel';
                    autoDetectIndicator.className = 'px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400 rounded-full text-xs font-medium';
                    
                    // Seleziona automaticamente il radio button del reel
                    const reelRadio = document.querySelector('input[name="is_reel"][value="1"]');
                    const normalRadio = document.querySelector('input[name="is_reel"][value="0"]');
                    if (reelRadio && normalRadio) {
                        reelRadio.checked = true;
                        normalRadio.checked = false;
                    }
                } else {
                    videoTypeText.textContent = 'Video Normale (Orizzontale)';
                    videoTypeIcon.className = 'fas fa-desktop mr-3 text-blue-600 dark:text-blue-400';
                    autoDetectIndicator.textContent = 'Auto-Normale';
                    autoDetectIndicator.className = 'px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 rounded-full text-xs font-medium';
                    
                    // Seleziona automaticamente il radio button del video normale
                    const reelRadio = document.querySelector('input[name="is_reel"][value="1"]');
                    const normalRadio = document.querySelector('input[name="is_reel"][value="0"]');
                    if (reelRadio && normalRadio) {
                        normalRadio.checked = true;
                        reelRadio.checked = false;
                    }
                }
                
                // Aggiungi informazioni sulla risoluzione
                if (width > 0 && height > 0) {
                    const resolutionInfo = document.createElement('span');
                    resolutionInfo.className = 'text-xs text-gray-500 dark:text-gray-400 ml-2';
                    resolutionInfo.textContent = `(${width}×${height})`;
                    videoTypeText.appendChild(resolutionInfo);
                }
            }

            function calculateProcessingTime(fileSize) {
                const sizeInMB = fileSize / (1024 * 1024);
                const estimatedMinutes = Math.ceil(sizeInMB / 50);
                if (estimatedMinutes < 60) {
                    return estimatedMinutes + ' minuti';
                } else {
                    const hours = Math.floor(estimatedMinutes / 60);
                    const minutes = estimatedMinutes % 60;
                    return hours + 'h ' + minutes + 'm';
                }
            }

            function formatFileSize(bytes) {
                const units = ['B', 'KB', 'MB', 'GB'];
                let size = bytes;
                let unitIndex = 0;
                while (size > 1024 && unitIndex < units.length - 1) {
                    size /= 1024;
                    unitIndex++;
                }
                return Math.round(size * 100) / 100 + ' ' + units[unitIndex];
            }

            async function handleFormSubmit(e) {
                e.preventDefault();

                const file = videoFile.files[0];
                if (!file) {
                    showError('Seleziona un file video.');
                    return;
                }

                if (!document.getElementById('title').value.trim()) {
                    showError('Il titolo è obbligatorio.');
                    return;
                }

                waterLayer.classList.remove('hidden');

                setWaterProgress(5);

                try {
                    const formData = new FormData(form);

                    let csrfToken = '';
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) csrfToken = meta.getAttribute('content');
                    if (!csrfToken) {
                        const tokenInput = form.querySelector('input[name="_token"]');
                        if (tokenInput) csrfToken = tokenInput.value;
                    }

                    const xhr = new XMLHttpRequest();

                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const progress = Math.round((e.loaded / e.total) * 100);
                            const visual = Math.max(5, Math.min(100, progress));
                            setWaterProgress(visual);
                        }
                    });

                    xhr.addEventListener('load', function() {
                        if (xhr.status === 200) {
                            setWaterProgress(100);

                            setTimeout(() => {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        successMessage.classList.remove('hidden');
                                        document.getElementById('success-text').textContent =
                                            response.message || 'Video caricato con successo!';
                                        setTimeout(() => {
                                            window.location.href = response.redirect ||
                                                '/';
                                        }, 1800);
                                    } else {
                                        showError(response.message ||
                                            'Errore durante l\'upload.');
                                        resetForm();
                                    }
                                } catch (err) {
                                    showError('Risposta non valida dal server.');
                                    resetForm();
                                }
                            }, 500);
                        } else {
                            let response = {};
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (_) {}
                            showError(response.message || 'Errore durante l\'upload. Stato: ' + xhr
                                .status);
                            resetForm();
                        }
                    });

                    xhr.addEventListener('error', function() {
                        showError('Errore di connessione durante l\'upload.');
                        resetForm();
                    });

                    xhr.open('POST', '{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    if (csrfToken) xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.send(formData);

                } catch (error) {
                    showError('Errore: ' + error.message);
                    resetForm();
                }
            }

            function setWaterProgress(percent) {
                waterFill.style.height = percent + '%';
                waterPercent.textContent = Math.round(percent) + '%';

                // Applica colori dinamici al riempimento dell'acqua
                const waterFillInner = document.querySelector('.water-fill-inner');
                if (waterFillInner) {
                    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue(
                        '--primary-color') || '#dc2626';
                    const primaryLight = getComputedStyle(document.documentElement).getPropertyValue(
                        '--primary-color-light') || '#ef4444';
                    const primaryDark = getComputedStyle(document.documentElement).getPropertyValue(
                        '--primary-color-dark') || '#b91c1c';

                    waterFillInner.style.background = `linear-gradient(
                        180deg, 
                        rgba(255, 255, 255, 0.1) 0%, 
                        ${primaryLight.trim()} 50%, 
                        ${primaryDark.trim()} 100%
                    )`;
                }
            }

            function showError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
            }

            function hideError() {
                errorMessage.classList.add('hidden');
            }

            function resetForm() {
                waterLayer.classList.add('hidden');

                setWaterProgress(0);

                form.reset();
                videoElement.src = '';
                videoElement.classList.add('hidden');
                placeholder.classList.remove('hidden');
                videoPreviewPanel.classList.add('hidden');
                successMessage.classList.add('hidden');
            }
        });
    </script>
</div>
