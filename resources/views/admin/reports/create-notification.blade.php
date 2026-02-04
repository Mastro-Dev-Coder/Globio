<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.reports') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Invia Segnalazione/Avviso a Creator</h1>
            </div>

            <div class="rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-700">Nuovo Messaggio per Creator</h2>
                    <p class="text-sm text-gray-500 mt-1">Invia avvisi, warning o comunicazioni ai creator della
                        piattaforma
                    </p>
                </div>

                <form action="{{ route('admin.reports.send-creator-notification') }}" method="POST" class="p-6">
                    @csrf

                    <!-- Template Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Messaggio</label>
                        <select name="template_type" id="template_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Seleziona un template (opzionale) --</option>
                            <option value="warning">⚠️ Avviso di Problema</option>
                            <option value="info">ℹ️ Informazione Generale</option>
                            <option value="policy_update">📋 Aggiornamento Politiche</option>
                            <option value="feature_announcement">🚀 Nuove Funzionalità</option>
                            <option value="community_guidelines">📖 Promemoria Linee Guida</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Seleziona un template per precompilare il messaggio</p>
                    </div>

                    <!-- Creator Selection -->
                    <div class="mb-6">
                        <label for="creator_id" class="block text-sm font-medium text-gray-700 mb-2">Seleziona
                            Creator</label>
                        <select name="creator_id" id="creator_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Scegli un creator --</option>
                            @foreach ($creators as $creator)
                                <option value="{{ $creator->id }}">{{ $creator->name }} (@{{ $creator - > userProfile - > username ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('creator_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titolo</label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Es: Avviso Importante">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Messaggio</label>
                        <textarea name="message" id="message" required rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Scrivi il tuo messaggio per il creator..."></textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Il messaggio sarà visibile nelle notifiche del creator e
                            nella
                            sezione Segnalazioni del suo canale.</p>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Anteprima Messaggio</h3>
                        <div id="message_preview" class="text-sm text-gray-600">
                            <p class="italic text-gray-400">Seleziona un template o scrivi un messaggio per vedere
                                l'anteprima...</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('admin.reports') }}"
                            class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Annulla
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Invia Segnalazione
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('template_type');
            const titleInput = document.getElementById('title');
            const messageTextarea = document.getElementById('message');
            const previewDiv = document.getElementById('message_preview');

            const templates = {
                warning: {
                    title: 'Avviso Importante',
                    message: 'Ciao {creator_name}, ti scriviamo per informarti di un problema riscontrato con alcuni dei tuoi contenuti. Ti preghiamo di rivedere le nostre linee guida della community e di apportare le modifiche necessarie entro 24 ore. Se hai domande, contatta il nostro team di supporto.'
                },
                info: {
                    title: 'Informazione Generale',
                    message: 'Ciao {creator_name}, vogliamo informarti di importanti aggiornamenti alla piattaforma Globio. Ti invitiamo a consultare la sezione novità nel tuo profilo per maggiori dettagli su queste nuove funzionalità.'
                },
                policy_update: {
                    title: 'Aggiornamento Politiche',
                    message: 'Ciao {creator_name}, ti scriviamo per informarti di un importante aggiornamento alle nostre politiche della community. Ti preghiamo di leggere attentamente le nuove linee guida e di conformarti a esse. La mancata conformità potrebbeenze sul comportare consegu tuo account.'
                },
                feature_announcement: {
                    title: 'Nuove Funzionalità',
                    message: 'Ciao {creator_name}, siamo entusiasti di annunciare il lancio di nuove funzionalità sulla piattaforma Globio! Queste nuove caratteristiche ti aiuteranno a creare contenuti ancora migliori e a connetterti con il tuo pubblico. Scopri tutte le novità nella sezione annunci.'
                },
                community_guidelines: {
                    title: 'Promemoria Linee Guida',
                    message: 'Ciao {creator_name}, ti ricordiamo l\'importanza di seguire le nostre linee guida della community per mantenere un ambiente sicuro e accogliente per tutti i creator. Ti preghiamo di rivedere periodicamente queste linee guida sul nostro sito.'
                }
            };

            templateSelect.addEventListener('change', function() {
                const templateKey = this.value;

                if (templateKey && templates[templateKey]) {
                    titleInput.value = templates[templateKey].title;
                    messageTextarea.value = templates[templateKey].message;
                    updatePreview(templates[templateKey].message);
                } else {
                    titleInput.value = '';
                    messageTextarea.value = '';
                    updatePreview('');
                }
            });

            messageTextarea.addEventListener('input', function() {
                updatePreview(this.value);
            });

            function updatePreview(message) {
                if (!message) {
                    previewDiv.innerHTML =
                        '<p class="italic text-gray-400">Seleziona un template o scrivi un messaggio per vedere l\'anteprima...</p>';
                } else {
                    const creatorName = document.getElementById('creator_id')?.selectedOptions[0]?.text?.split('(')[
                        0]?.trim() || '{creator_name}';
                    const platformName = 'Globio';

                    let previewMessage = message
                        .replace('{creator_name}', creatorName)
                        .replace('{platform_name}', platformName);

                    previewDiv.innerHTML = `<p>${previewMessage.replace(/\n/g, '<br>')}</p>`;
                }
            }

            // Update preview when creator is selected
            document.getElementById('creator_id')?.addEventListener('change', function() {
                if (messageTextarea.value) {
                    updatePreview(messageTextarea.value);
                }
            });
        });
    </script>
</x-admin-layout>
