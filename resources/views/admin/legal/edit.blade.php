<x-admin-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <!-- Icona dinamica basata sul tipo di pagina -->
                        @if ($legalPage->slug === 'contatti')
                            <div
                                class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                        @elseif($legalPage->slug === 'privacy')
                            <div
                                class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                        @else
                            <div
                                class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-purple-600 dark:text-purple-400 text-lg"></i>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                Modifica {{ $legalPage->title }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Modifica il contenuto della pagina {{ strtolower($legalPage->title) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button type="button" id="preview-btn" onclick="togglePreview()"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        <i class="fas fa-eye mr-2" id="preview-icon"></i>
                        <span id="preview-text">Anteprima</span>
                    </button>
                    <a href="{{ route('admin.legal.index') }}"
                        class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna all'elenco
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.legal.update', $legalPage->slug) }}" method="POST" id="legal-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Editor Section -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 shadow ring-1 ring-gray-900/10 dark:ring-white/10 rounded-lg overflow-hidden">
                        <!-- Toolbar -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Editor
                                        HTML</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" id="char-count">0
                                        caratteri</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="insertHTML('h2')"
                                        class="px-2 py-1 text-xs bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded hover:bg-gray-50 dark:hover:bg-gray-500">
                                        H2
                                    </button>
                                    <button type="button" onclick="insertHTML('p')"
                                        class="px-2 py-1 text-xs bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded hover:bg-gray-50 dark:hover:bg-gray-500">
                                        P
                                    </button>
                                    <button type="button" onclick="insertHTML('ul')"
                                        class="px-2 py-1 text-xs bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded hover:bg-gray-50 dark:hover:bg-gray-500">
                                        UL
                                    </button>
                                    <button type="button" onclick="insertHTML('li')"
                                        class="px-2 py-1 text-xs bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded hover:bg-gray-50 dark:hover:bg-gray-500">
                                        LI
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Title Field -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <label for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Titolo della Pagina
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $legalPage->title) }}"
                                class="block p-2 w-full rounded-md outline-none border-gray-300 dark:border-gray-600 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                placeholder="Inserisci il titolo della pagina" required>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content Editor -->
                        <div class="px-6 py-4">
                            <label for="content"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Contenuto HTML
                            </label>
                            <textarea name="content" id="content" rows="20"
                                class="block p-2 w-full rounded-md outline-none border-gray-300 dark:border-gray-600 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:text-white font-mono text-sm editor-textarea"
                                placeholder="Inserisci il contenuto HTML della pagina..." required>{{ old('content', $legalPage->content) }}</textarea>
                            <div
                                class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>Usa tag HTML validi per la formattazione</span>
                                <span id="word-count">0 parole</span>
                            </div>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-3">
                                    <button type="button" onclick="saveDraft()"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500">
                                        <i class="fas fa-save mr-2"></i>
                                        Bozza
                                    </button>
                                    <button type="button" onclick="validateHTML()"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Valida HTML
                                    </button>
                                </div>
                                <button type="submit" id="save-btn"
                                    class="inline-flex items-center rounded-md bg-red-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Salva Modifiche
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Page Info Card -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow ring-1 ring-gray-900/10 dark:ring-white/10 rounded-lg overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Informazioni Pagina</h3>
                        </div>
                        <div class="px-4 py-3 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Slug:</span>
                                <span class="font-mono text-gray-900 dark:text-white">{{ $legalPage->slug }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Creata:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $legalPage->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Modificata:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $legalPage->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Caratteri:</span>
                                <span class="text-gray-900 dark:text-white"
                                    id="sidebar-char-count">{{ strlen(strip_tags($legalPage->content)) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Templates -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow ring-1 ring-gray-900/10 dark:ring-white/10 rounded-lg overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Template Rapidi</h3>
                        </div>
                        <div class="px-4 py-3 space-y-2">
                            <button type="button" onclick="insertTemplate('section')"
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Nuova Sezione
                            </button>
                            <button type="button" onclick="insertTemplate('list')"
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Lista Punti
                            </button>
                            <button type="button" onclick="insertTemplate('highlight')"
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Testo Evidenziato
                            </button>
                            <button type="button" onclick="insertTemplate('contact-info')"
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Info Contatto
                            </button>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">Suggerimenti</h4>
                                <div class="mt-2 text-sm text-blue-800 dark:text-blue-200">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Usa tag HTML semantici</li>
                                        <li>Testa l'anteprima prima di salvare</li>
                                        <li>Mantieni un linguaggio professionale</li>
                                        <li>Salva frequentemente le modifiche</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closePreview()"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Anteprima: <span id="preview-title">{{ $legalPage->title }}</span>
                    </h3>
                    <button onclick="closePreview()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-4 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <div id="preview-content" class="prose prose-gray dark:prose-invert max-w-none">
                        {!! $legalPage->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character and word counter
        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('char-count');
        const wordCount = document.getElementById('word-count');
        const sidebarCharCount = document.getElementById('sidebar-char-count');

        function updateCounters() {
            const content = contentTextarea.value;
            const chars = content.length;
            const words = content.trim().split(/\s+/).filter(word => word.length > 0).length;

            charCount.textContent = chars + ' caratteri';
            wordCount.textContent = words + ' parole';
            sidebarCharCount.textContent = chars;
        }

        contentTextarea.addEventListener('input', updateCounters);
        updateCounters();

        // Insert HTML helper function
        function insertHTML(tag) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            const newText = `<${tag}>` + selectedText + `</${tag}>`;

            textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + tag.length + 2, start + tag.length + 2 + selectedText.length);
            updateCounters();
        }

        // Template insertion
        function insertTemplate(type) {
            const textarea = document.getElementById('content');
            let template = '';

            switch (type) {
                case 'section':
                    template = `<section class="mb-8">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Titolo Sezione</h2>
    <p class="text-gray-700 dark:text-gray-300">Contenuto della sezione...</p>
</section>`;
                    break;
                case 'list':
                    template = `<ul class="list-disc pl-6 space-y-2">
    <li class="text-gray-700 dark:text-gray-300">Primo punto</li>
    <li class="text-gray-700 dark:text-gray-300">Secondo punto</li>
    <li class="text-gray-700 dark:text-gray-300">Terzo punto</li>
</ul>`;
                    break;
                case 'highlight':
                    template = `<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <p class="text-blue-800 dark:text-blue-200">Testo evidenziato importante...</p>
</div>`;
                    break;
                case 'contact-info':
                    template = `<div class="grid md:grid-cols-2 gap-6">
    <div class="text-center">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Email</h3>
        <p class="text-gray-600 dark:text-gray-400">info@globio.com</p>
    </div>
    <div class="text-center">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Telefono</h3>
        <p class="text-gray-600 dark:text-gray-400">+39 06 1234 5678</p>
    </div>
</div>`;
                    break;
            }

            const cursorPos = textarea.selectionStart;
            textarea.value = textarea.value.substring(0, cursorPos) + template + textarea.value.substring(cursorPos);
            textarea.focus();
            updateCounters();
        }

        // Preview functionality
        function togglePreview() {
            const modal = document.getElementById('preview-modal');
            const content = document.getElementById('content').value;
            const previewContent = document.getElementById('preview-content');
            const title = document.getElementById('title').value;

            previewContent.innerHTML = content;
            document.getElementById('preview-title').textContent = title;
            modal.classList.toggle('hidden');
        }

        function closePreview() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        // Draft saving
        function saveDraft() {
            const formData = new FormData(document.getElementById('legal-form'));
            formData.append('draft', '1');

            fetch('{{ route('admin.legal.update', $legalPage->slug) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Bozza salvata!');
                    }
                });
        }

        // HTML Validation
        function validateHTML() {
            const content = document.getElementById('content').value;
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');

            if (doc.body.querySelector('parsererror')) {
                alert('Errore nel formato HTML. Verifica la sintassi.');
            } else {
                alert('HTML valido! ✓');
            }
        }

        // Auto-save draft every 30 seconds
        setInterval(() => {
            const content = document.getElementById('content').value;
            const title = document.getElementById('title').value;
            if (content && title) {
                localStorage.setItem('legal_draft_{{ $legalPage->slug }}', JSON.stringify({
                    title: title,
                    content: content,
                    timestamp: new Date().toISOString()
                }));
            }
        }, 30000);

        // Load draft from localStorage
        window.addEventListener('load', () => {
            const draft = localStorage.getItem('legal_draft_{{ $legalPage->slug }}');
            if (draft) {
                const data = JSON.parse(draft);
                const timestamp = new Date(data.timestamp);
                const now = new Date();
                const diffMinutes = Math.floor((now - timestamp) / (1000 * 60));

                if (diffMinutes < 60 && (data.content !== '{{ $legalPage->content }}' || data.title !==
                        '{{ $legalPage->title }}')) {
                    if (confirm(
                            `È stata trovata una bozza salvata automaticamente ${diffMinutes} minuti fa. Vuoi ripristinarla?`
                            )) {
                        document.getElementById('title').value = data.title;
                        document.getElementById('content').value = data.content;
                        updateCounters();
                    }
                }
            }
        });
    </script>

    <style>
        .editor-textarea {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            line-height: 1.5;
            tab-size: 2;
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4,
        .prose h5,
        .prose h6 {
            color: inherit;
        }
    </style>
</x-admin-layout>
