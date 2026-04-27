@php
    $maxUploadMb = \App\Models\Setting::getValue('max_video_upload_mb', 500);
    $playlistPayload = $playlistOptions->map(fn ($playlist) => [
        'id' => $playlist->id,
        'title' => $playlist->title,
        'description' => $playlist->description,
        'is_public' => (bool) $playlist->is_public,
        'videos_count' => (int) $playlist->videos_count,
    ])->values();
    $videoPayload = $suggestedCandidates->map(fn ($video) => [
        'id' => $video->id,
        'title' => $video->title,
        'slug' => $video->video_url,
        'thumbnail_url' => $video->thumbnail_url,
        'is_reel' => (bool) $video->is_reel,
        'status' => $video->status,
        'published_at' => optional($video->published_at)?->format('d/m/Y H:i'),
    ])->values();
@endphp

<div id="upload-wizard-root" class="p-6 sm:p-8">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-red-500">Studio Wizard</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Carica video o reel in 4 step</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Il caricamento riprende anche dopo refresh o riapertura della pagina. Si annulla solo dal pulsante dedicato.
                </p>
            </div>
            <div id="resume-pill" class="hidden rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                Sessione ripristinata automaticamente
            </div>
        </div>

        <div class="mb-8 grid gap-3 md:grid-cols-4" id="wizard-steps">
            @foreach ([
                1 => ['title' => 'Contenuto', 'desc' => 'File e upload'],
                2 => ['title' => 'Dettagli', 'desc' => 'Titolo e miniatura'],
                3 => ['title' => 'Elementi', 'desc' => 'Playlist e card'],
                4 => ['title' => 'Visibilita', 'desc' => 'Pubblica o programma'],
            ] as $step => $item)
                <button
                    type="button"
                    data-step-jump="{{ $step }}"
                    class="wizard-step flex items-start gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition dark:border-gray-700 dark:bg-gray-900/60">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $step }}</span>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $item['title'] }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $item['desc'] }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        <div class="rounded-[28px] border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 id="step-title" class="text-lg font-semibold text-gray-900 dark:text-white"></h3>
                        <p id="step-description" class="mt-1 text-sm text-gray-500 dark:text-gray-400"></p>
                    </div>
                    <div class="min-w-[220px]">
                        <div class="mb-2 flex items-center justify-between text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <span>Progresso upload</span>
                            <span id="global-progress-label">0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div id="global-progress-bar" class="h-full rounded-full bg-gradient-to-r from-red-500 via-orange-500 to-amber-400 transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div id="wizard-alert" class="hidden mb-6 rounded-2xl border px-4 py-3 text-sm"></div>

                <section data-step-panel="1" class="wizard-panel space-y-6">
                    <div class="grid gap-6 lg:grid-cols-[1.4fr,0.8fr]">
                        <div>
                            <label for="wizard-video-file" class="group relative flex min-h-[320px] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-[26px] border-2 border-dashed border-gray-300 bg-gradient-to-br from-gray-50 via-white to-gray-100 p-8 transition hover:border-red-400 hover:bg-red-50/30 dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 dark:hover:border-red-500 dark:hover:bg-red-900/10">
                                <input id="wizard-video-file" type="file" accept="video/*" class="hidden">
                                <div id="video-drop-empty" class="text-center">
                                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-red-100 text-red-500 shadow-inner dark:bg-red-900/20">
                                        <i class="fas fa-clapperboard text-3xl"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white">Trascina qui il video o il reel</h4>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">MP4, AVI, MOV, WMV, FLV, WebM fino a {{ $maxUploadMb }} MB</p>
                                    <span class="mt-6 inline-flex rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white dark:bg-white dark:text-gray-900">Seleziona file</span>
                                </div>
                                <video id="video-preview" class="hidden h-full max-h-[340px] w-full rounded-[22px] bg-black object-contain" controls preload="metadata"></video>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/60">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Stato trasferimento</span>
                                    <span id="upload-status-pill" class="rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">In attesa</span>
                                </div>
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div id="upload-progress-fill" class="h-full rounded-full bg-gradient-to-r from-red-500 via-orange-500 to-amber-400 transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span id="upload-bytes-text">0 MB / 0 MB</span>
                                    <span id="upload-step-text">Nessun caricamento</span>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Dettagli file</h4>
                                <dl class="mt-4 space-y-3 text-sm">
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-gray-400">Nome</dt>
                                        <dd id="video-file-name" class="truncate text-right font-medium text-gray-900 dark:text-white">Nessun file</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-gray-400">Dimensione</dt>
                                        <dd id="video-file-size" class="font-medium text-gray-900 dark:text-white">-</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-gray-400">Durata stimata processo</dt>
                                        <dd id="video-process-time" class="font-medium text-gray-900 dark:text-white">-</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-gray-400">Tipo rilevato</dt>
                                        <dd id="video-kind-pill" class="font-medium text-gray-900 dark:text-white">Video</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-200">
                                Puoi chiudere il modal o aggiornare la pagina: al ritorno il wizard riprendera da dove era arrivato.
                            </div>
                        </div>
                    </div>
                </section>

                <section data-step-panel="2" class="wizard-panel hidden space-y-6">
                    <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                        <div class="space-y-5">
                            <div>
                                <label for="wizard-title" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Titolo</label>
                                <input id="wizard-title" type="text" maxlength="255" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none ring-0 transition focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Inserisci un titolo forte e chiaro">
                            </div>
                            <div>
                                <label for="wizard-description" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Descrizione</label>
                                <textarea id="wizard-description" rows="6" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none ring-0 transition focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Descrivi contenuto, call to action e link utili"></textarea>
                            </div>
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label for="wizard-tags" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Tag</label>
                                    <input id="wizard-tags" type="text" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="gaming, tutorial, vlog">
                                </div>
                                <div>
                                    <label for="wizard-language" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Lingua</label>
                                    <select id="wizard-language" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        @foreach ($supportedLanguages as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="rounded-2xl border border-gray-300 p-4 transition hover:border-red-300 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <input id="wizard-kind-video" type="radio" name="wizard-kind" value="video" class="mt-1">
                                        <div>
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Video classico</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">Layout orizzontale, ideale per player completo.</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="rounded-2xl border border-gray-300 p-4 transition hover:border-red-300 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <input id="wizard-kind-reel" type="radio" name="wizard-kind" value="reel" class="mt-1">
                                        <div>
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Reel / Short</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">Formato rapido verticale per feed reels.</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Miniatura</h4>
                                <label for="wizard-thumbnail" class="mt-4 flex min-h-[220px] cursor-pointer flex-col items-center justify-center rounded-[22px] border-2 border-dashed border-gray-300 bg-white p-6 text-center transition hover:border-red-400 dark:border-gray-700 dark:bg-gray-900">
                                    <input id="wizard-thumbnail" type="file" accept="image/*" class="hidden">
                                    <img id="thumbnail-preview" class="hidden max-h-[220px] w-full rounded-2xl object-cover" alt="Thumbnail preview">
                                    <div id="thumbnail-empty">
                                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-100 text-red-500 dark:bg-red-900/20">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Aggiungi una miniatura personalizzata</p>
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Consigliato 1280x720, JPG o PNG</p>
                                    </div>
                                </label>
                            </div>

                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Anteprima rapida</h4>
                                <div class="mt-4 overflow-hidden rounded-3xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="aspect-video bg-gray-200 dark:bg-gray-700">
                                        <img id="preview-card-image" class="hidden h-full w-full object-cover" alt="">
                                        <div id="preview-card-fallback" class="flex h-full items-center justify-center text-gray-400">
                                            <i class="fas fa-film text-3xl"></i>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <p id="preview-card-title" class="line-clamp-2 text-sm font-semibold text-gray-900 dark:text-white">Titolo del contenuto</p>
                                        <p id="preview-card-meta" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Studio wizard preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section data-step-panel="3" class="wizard-panel hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[0.9fr,1.1fr]">
                        <div class="space-y-5">
                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Aggiungi a playlist</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Selezione multipla</span>
                                </div>
                                <div id="playlist-list" class="mt-4 space-y-3"></div>
                            </div>

                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Interazione video</h4>
                                <div class="mt-4 space-y-3">
                                    <label class="flex items-center justify-between rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Commenti attivi</span>
                                        <input id="wizard-comments-enabled" type="checkbox" class="h-5 w-5 rounded" checked>
                                    </label>
                                    <label class="flex items-center justify-between rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Like attivi</span>
                                        <input id="wizard-likes-enabled" type="checkbox" class="h-5 w-5 rounded" checked>
                                    </label>
                                    <label class="flex items-center justify-between rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Approva commenti prima della pubblicazione</span>
                                        <input id="wizard-comments-approval" type="checkbox" class="h-5 w-5 rounded">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Card suggerite nel video</h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Seleziona fino a 3 contenuti da consigliare come card o suggerimenti.</p>
                                </div>
                                <span id="cards-count" class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">0/3</span>
                            </div>
                            <div id="cards-list" class="mt-4 grid gap-3 md:grid-cols-2"></div>
                        </div>
                    </div>
                </section>

                <section data-step-panel="4" class="wizard-panel hidden space-y-6">
                    <div class="grid gap-6 lg:grid-cols-[0.9fr,1.1fr]">
                        <div class="space-y-5">
                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Visibilita</h4>
                                <div class="mt-4 space-y-3">
                                    <label class="block rounded-2xl border border-gray-300 p-4 dark:border-gray-700">
                                        <div class="flex items-start gap-3">
                                            <input id="visibility-public" type="radio" name="visibility" value="public" class="mt-1">
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Pubblico</span>
                                                <span class="block text-xs text-gray-500 dark:text-gray-400">Va online appena il processamento termina.</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="block rounded-2xl border border-gray-300 p-4 dark:border-gray-700">
                                        <div class="flex items-start gap-3">
                                            <input id="visibility-private" type="radio" name="visibility" value="private" class="mt-1">
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Privato</span>
                                                <span class="block text-xs text-gray-500 dark:text-gray-400">Resta in bozza privata dopo il processamento.</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="block rounded-2xl border border-gray-300 p-4 dark:border-gray-700">
                                        <div class="flex items-start gap-3">
                                            <input id="visibility-scheduled" type="radio" name="visibility" value="scheduled" class="mt-1">
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Programma pubblicazione</span>
                                                <span class="block text-xs text-gray-500 dark:text-gray-400">Diventa pubblico nella data e ora scelte.</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div id="schedule-box" class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-900/20">
                                    <label for="wizard-scheduled-for" class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Data e ora di pubblicazione</label>
                                    <input id="wizard-scheduled-for" type="datetime-local" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                </div>
                            </div>

                            <div class="rounded-3xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900/70">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Stato finale</h4>
                                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i><span>Upload resumable a chunk con ripresa automatica</span></li>
                                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i><span>Playlist e card suggerite salvate insieme al contenuto</span></li>
                                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i><span>Pubblicazione immediata, privata o programmata</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="rounded-[28px] border border-gray-200 bg-gradient-to-br from-gray-50 via-white to-red-50 p-6 dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-red-950/20">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Riepilogo upload</h4>
                                <span id="review-kind" class="rounded-full bg-gray-900 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-gray-900">Video</span>
                            </div>
                            <div class="mt-5 space-y-4 text-sm">
                                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-gray-800 dark:bg-gray-900/80">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Titolo</p>
                                    <p id="review-title" class="mt-1 font-semibold text-gray-900 dark:text-white">-</p>
                                </div>
                                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-gray-800 dark:bg-gray-900/80">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Playlist</p>
                                    <p id="review-playlists" class="mt-1 font-semibold text-gray-900 dark:text-white">Nessuna</p>
                                </div>
                                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-gray-800 dark:bg-gray-900/80">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Card suggerite</p>
                                    <p id="review-cards" class="mt-1 font-semibold text-gray-900 dark:text-white">Nessuna</p>
                                </div>
                                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-gray-800 dark:bg-gray-900/80">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Visibilita</p>
                                    <p id="review-visibility" class="mt-1 font-semibold text-gray-900 dark:text-white">Pubblico</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                <div class="flex flex-wrap gap-3">
                    <button id="wizard-cancel-upload" type="button" class="rounded-full border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:hover:bg-red-900/20">
                        Annulla caricamento
                    </button>
                    <button id="wizard-save-draft" type="button" class="rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Salva bozza locale
                    </button>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button id="wizard-prev" type="button" class="rounded-full border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Indietro
                    </button>
                    <button id="wizard-next" type="button" class="rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 dark:bg-white dark:text-gray-900">
                        Avanti
                    </button>
                    <button id="wizard-submit" type="button" class="hidden rounded-full bg-gradient-to-r from-red-600 via-orange-500 to-amber-400 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:opacity-90">
                        Completa caricamento
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const playlistOptions = @json($playlistPayload);
        const suggestedCandidates = @json($videoPayload);
        const routes = {
            create: @json(route('upload-wizard.sessions.create')),
            showBase: @json(url('/upload-wizard/sessions')),
        };
        const stateKey = 'globio-upload-wizard-state-v1';
        const dbName = 'globio-upload-wizard-db';
        const storeName = 'files';
        const maxCards = 3;

        const stepConfig = {
            1: { title: 'Step 1. Carica il contenuto', description: 'Seleziona il video o il reel e avvia il trasferimento resumable.' },
            2: { title: 'Step 2. Cura i dettagli', description: 'Titolo, descrizione, tag, lingua e miniatura.' },
            3: { title: 'Step 3. Collega elementi', description: 'Aggiungi playlist e scegli le card suggerite.' },
            4: { title: 'Step 4. Visibilita e pubblicazione', description: 'Pubblica subito, in privato oppure a data programmata.' },
        };

        const state = {
            currentStep: 1,
            sessionToken: null,
            uploadStatus: 'idle',
            progressPercent: 0,
            uploadedBytes: 0,
            uploadedChunks: [],
            totalChunks: 0,
            chunkSize: 1024 * 1024 * 2,
            autoDetectedReel: false,
            videoFile: null,
            thumbnailFile: null,
            meta: {
                title: '',
                description: '',
                tags: '',
                language: 'it',
                is_reel: false,
                playlist_ids: [],
                suggested_video_ids: [],
                comments_enabled: true,
                likes_enabled: true,
                comments_require_approval: false,
                visibility: 'public',
                scheduled_for: '',
            },
        };

        const els = {
            root: document.getElementById('upload-wizard-root'),
            alert: document.getElementById('wizard-alert'),
            title: document.getElementById('step-title'),
            description: document.getElementById('step-description'),
            panels: [...document.querySelectorAll('.wizard-panel')],
            stepButtons: [...document.querySelectorAll('.wizard-step')],
            stepJumps: [...document.querySelectorAll('[data-step-jump]')],
            prev: document.getElementById('wizard-prev'),
            next: document.getElementById('wizard-next'),
            submit: document.getElementById('wizard-submit'),
            saveDraft: document.getElementById('wizard-save-draft'),
            cancelUpload: document.getElementById('wizard-cancel-upload'),
            resumePill: document.getElementById('resume-pill'),
            globalProgressBar: document.getElementById('global-progress-bar'),
            globalProgressLabel: document.getElementById('global-progress-label'),
            uploadProgressFill: document.getElementById('upload-progress-fill'),
            uploadStatusPill: document.getElementById('upload-status-pill'),
            uploadBytesText: document.getElementById('upload-bytes-text'),
            uploadStepText: document.getElementById('upload-step-text'),
            fileInput: document.getElementById('wizard-video-file'),
            thumbnailInput: document.getElementById('wizard-thumbnail'),
            videoPreview: document.getElementById('video-preview'),
            videoDropEmpty: document.getElementById('video-drop-empty'),
            fileName: document.getElementById('video-file-name'),
            fileSize: document.getElementById('video-file-size'),
            processTime: document.getElementById('video-process-time'),
            kindPill: document.getElementById('video-kind-pill'),
            titleInput: document.getElementById('wizard-title'),
            descriptionInput: document.getElementById('wizard-description'),
            tagsInput: document.getElementById('wizard-tags'),
            languageInput: document.getElementById('wizard-language'),
            kindVideo: document.getElementById('wizard-kind-video'),
            kindReel: document.getElementById('wizard-kind-reel'),
            thumbnailPreview: document.getElementById('thumbnail-preview'),
            thumbnailEmpty: document.getElementById('thumbnail-empty'),
            previewCardImage: document.getElementById('preview-card-image'),
            previewCardFallback: document.getElementById('preview-card-fallback'),
            previewCardTitle: document.getElementById('preview-card-title'),
            previewCardMeta: document.getElementById('preview-card-meta'),
            playlistList: document.getElementById('playlist-list'),
            cardsList: document.getElementById('cards-list'),
            cardsCount: document.getElementById('cards-count'),
            commentsEnabled: document.getElementById('wizard-comments-enabled'),
            likesEnabled: document.getElementById('wizard-likes-enabled'),
            commentsApproval: document.getElementById('wizard-comments-approval'),
            visibilityPublic: document.getElementById('visibility-public'),
            visibilityPrivate: document.getElementById('visibility-private'),
            visibilityScheduled: document.getElementById('visibility-scheduled'),
            scheduleBox: document.getElementById('schedule-box'),
            scheduledForInput: document.getElementById('wizard-scheduled-for'),
            reviewKind: document.getElementById('review-kind'),
            reviewTitle: document.getElementById('review-title'),
            reviewPlaylists: document.getElementById('review-playlists'),
            reviewCards: document.getElementById('review-cards'),
            reviewVisibility: document.getElementById('review-visibility'),
        };

        let uploadAbortController = null;

        init();

        async function init() {
            renderPlaylistList();
            renderCardsList();
            bindEvents();
            await restoreDraft();
            renderStep();
            syncAllFields();
            updateProgressUI();
            updateReview();
        }

        function bindEvents() {
            els.fileInput.addEventListener('change', async (event) => {
                const file = event.target.files?.[0];
                if (!file) return;
                try {
                    await handleVideoFile(file);
                } catch (error) {
                    showAlert(error.message || 'Errore durante la preparazione del file.', 'error');
                }
            });

            els.thumbnailInput.addEventListener('change', async (event) => {
                const file = event.target.files?.[0] || null;
                state.thumbnailFile = file;
                await saveFileToDb('thumbnailFile', file);
                updateThumbnailPreview();
                persistDraft();
            });

            els.titleInput.addEventListener('input', () => updateMeta('title', els.titleInput.value));
            els.descriptionInput.addEventListener('input', () => updateMeta('description', els.descriptionInput.value));
            els.tagsInput.addEventListener('input', () => updateMeta('tags', els.tagsInput.value));
            els.languageInput.addEventListener('change', () => updateMeta('language', els.languageInput.value));
            els.kindVideo.addEventListener('change', () => updateMeta('is_reel', false));
            els.kindReel.addEventListener('change', () => updateMeta('is_reel', true));
            els.commentsEnabled.addEventListener('change', () => updateMeta('comments_enabled', els.commentsEnabled.checked));
            els.likesEnabled.addEventListener('change', () => updateMeta('likes_enabled', els.likesEnabled.checked));
            els.commentsApproval.addEventListener('change', () => updateMeta('comments_require_approval', els.commentsApproval.checked));
            els.visibilityPublic.addEventListener('change', () => updateVisibility('public'));
            els.visibilityPrivate.addEventListener('change', () => updateVisibility('private'));
            els.visibilityScheduled.addEventListener('change', () => updateVisibility('scheduled'));
            els.scheduledForInput.addEventListener('change', () => updateMeta('scheduled_for', els.scheduledForInput.value));

            els.prev.addEventListener('click', () => changeStep(state.currentStep - 1));
            els.next.addEventListener('click', async () => {
                if (await validateCurrentStep()) {
                    changeStep(state.currentStep + 1);
                }
            });
            els.submit.addEventListener('click', finalizeUpload);
            els.saveDraft.addEventListener('click', () => {
                persistDraft();
                showAlert('Bozza locale salvata.', 'success');
            });
            els.cancelUpload.addEventListener('click', cancelUpload);

            els.stepJumps.forEach((button) => {
                button.addEventListener('click', async () => {
                    const targetStep = Number(button.dataset.stepJump);
                    if (targetStep <= state.currentStep) {
                        changeStep(targetStep);
                        return;
                    }
                    if (await validateCurrentStep()) {
                        changeStep(targetStep);
                    }
                });
            });
        }

        async function handleVideoFile(file) {
            state.videoFile = file;
            await saveFileToDb('videoFile', file);
            state.sessionToken = null;
            state.uploadStatus = 'preparing';
            state.progressPercent = 0;
            state.uploadedBytes = 0;
            state.uploadedChunks = [];
            state.totalChunks = Math.max(1, Math.ceil(file.size / state.chunkSize));

            if (!state.meta.title) {
                updateMeta('title', file.name.replace(/\.[^.]+$/, ''));
            }

            updateVideoInfo();
            await detectVideoKind(file);
            persistDraft();
            await createUploadSession(file);
            await resumeUpload();
        }

        async function createUploadSession(file) {
            const response = await fetch(routes.create, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    file_name: file.name,
                    file_size: file.size,
                    mime_type: file.type,
                    chunk_size: state.chunkSize,
                    total_chunks: state.totalChunks,
                }),
            });

            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.message || 'Impossibile creare la sessione di upload.');
            }

            hydrateSession(payload.session);
            persistDraft();
        }

        async function resumeUpload() {
            if (!state.sessionToken || !state.videoFile) {
                return;
            }

            uploadAbortController = new AbortController();
            state.uploadStatus = 'uploading';
            updateProgressUI();

            for (let index = 0; index < state.totalChunks; index++) {
                if (state.uploadedChunks.includes(index)) {
                    continue;
                }

                const start = index * state.chunkSize;
                const end = Math.min(state.videoFile.size, start + state.chunkSize);
                const chunk = state.videoFile.slice(start, end);
                const formData = new FormData();
                formData.append('chunk_index', String(index));
                formData.append('chunk', chunk, `${state.videoFile.name}.part${index}`);

                const response = await fetch(`${routes.showBase}/${state.sessionToken}/chunk`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                    signal: uploadAbortController.signal,
                });

                const payload = await response.json();
                if (!response.ok) {
                    throw new Error(payload.message || 'Errore durante il caricamento chunk.');
                }

                hydrateSession(payload.session);
                persistDraft();
                updateProgressUI();
            }

            state.uploadStatus = 'uploaded';
            updateProgressUI();
            showAlert('File caricato. Ora puoi completare impostazioni e pubblicazione.', 'success');
        }

        async function restoreDraft() {
            const raw = localStorage.getItem(stateKey);
            if (!raw) return;

            try {
                const draft = JSON.parse(raw);
                Object.assign(state.meta, draft.meta || {});
                state.currentStep = draft.currentStep || 1;
                state.sessionToken = draft.sessionToken || null;
                state.progressPercent = draft.progressPercent || 0;
                state.uploadStatus = draft.uploadStatus || 'idle';
                state.chunkSize = draft.chunkSize || state.chunkSize;

                state.videoFile = await readFileFromDb('videoFile');
                state.thumbnailFile = await readFileFromDb('thumbnailFile');

                if (state.sessionToken) {
                    const response = await fetch(`${routes.showBase}/${state.sessionToken}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (response.ok) {
                        const payload = await response.json();
                        hydrateSession(payload.session);
                        if (state.videoFile && ['pending', 'uploading', 'uploaded'].includes(state.uploadStatus)) {
                            els.resumePill.classList.remove('hidden');
                            if (state.uploadStatus !== 'uploaded') {
                                try {
                                    await resumeUpload();
                                } catch (error) {
                                    showAlert(error.message || 'Impossibile riprendere il caricamento.', 'error');
                                }
                            }
                        }
                    }
                }
            } catch (error) {
                console.error(error);
            }
        }

        async function finalizeUpload() {
            if (!(await validateCurrentStep()) || !(await validateAll())) {
                return;
            }

            if (!state.sessionToken) {
                showAlert('Sessione upload non trovata.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('title', state.meta.title);
            formData.append('description', state.meta.description || '');
            formData.append('tags', state.meta.tags || '');
            formData.append('language', state.meta.language);
            formData.append('visibility', state.meta.visibility);
            formData.append('is_reel', state.meta.is_reel ? '1' : '0');
            formData.append('comments_enabled', state.meta.comments_enabled ? '1' : '0');
            formData.append('likes_enabled', state.meta.likes_enabled ? '1' : '0');
            formData.append('comments_require_approval', state.meta.comments_require_approval ? '1' : '0');

            if (state.meta.scheduled_for) {
                formData.append('scheduled_for', state.meta.scheduled_for);
            }

            state.meta.playlist_ids.forEach((id) => formData.append('playlist_ids[]', id));
            state.meta.suggested_video_ids.forEach((id) => formData.append('suggested_video_ids[]', id));

            if (state.thumbnailFile) {
                formData.append('thumbnail', state.thumbnailFile, state.thumbnailFile.name || 'thumbnail.jpg');
            }

            els.submit.disabled = true;
            els.submit.textContent = 'Finalizzazione...';

            try {
                const response = await fetch(`${routes.showBase}/${state.sessionToken}/finalize`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                const payload = await response.json();
                if (!response.ok) {
                    throw new Error(payload.message || 'Errore durante la finalizzazione.');
                }

                await clearDraft();
                showAlert('Upload completato. Reindirizzamento in corso...', 'success');
                setTimeout(() => {
                    window.location.href = payload.video.redirect_url;
                }, 1000);
            } catch (error) {
                showAlert(error.message, 'error');
            } finally {
                els.submit.disabled = false;
                els.submit.textContent = 'Completa caricamento';
            }
        }

        async function cancelUpload() {
            if (!confirm('Vuoi davvero annullare il caricamento? Questa azione cancella file, progresso e bozza locale.')) {
                return;
            }

            uploadAbortController?.abort();

            if (state.sessionToken) {
                await fetch(`${routes.showBase}/${state.sessionToken}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
            }

            await clearDraft();
            resetState();
            renderStep();
            updateProgressUI();
            showAlert('Caricamento annullato.', 'success');
        }

        function resetState() {
            state.currentStep = 1;
            state.sessionToken = null;
            state.uploadStatus = 'idle';
            state.progressPercent = 0;
            state.uploadedBytes = 0;
            state.uploadedChunks = [];
            state.totalChunks = 0;
            state.videoFile = null;
            state.thumbnailFile = null;
            els.resumePill.classList.add('hidden');
            state.meta = {
                title: '',
                description: '',
                tags: '',
                language: 'it',
                is_reel: false,
                playlist_ids: [],
                suggested_video_ids: [],
                comments_enabled: true,
                likes_enabled: true,
                comments_require_approval: false,
                visibility: 'public',
                scheduled_for: '',
            };
            syncAllFields();
        }

        function hydrateSession(session) {
            state.sessionToken = session.token;
            state.uploadStatus = session.status;
            state.uploadedBytes = session.uploaded_bytes;
            state.uploadedChunks = session.uploaded_chunks || [];
            state.totalChunks = session.total_chunks || 0;
            state.progressPercent = session.progress_percent || 0;
        }

        function renderStep() {
            const config = stepConfig[state.currentStep];
            els.title.textContent = config.title;
            els.description.textContent = config.description;
            els.panels.forEach((panel) => {
                panel.classList.toggle('hidden', Number(panel.dataset.stepPanel) !== state.currentStep);
            });
            els.stepButtons.forEach((button, index) => {
                const step = index + 1;
                button.classList.toggle('border-red-500', step === state.currentStep);
                button.classList.toggle('bg-red-50', step === state.currentStep);
                button.classList.toggle('dark:bg-red-950/20', step === state.currentStep);
            });
            els.prev.classList.toggle('invisible', state.currentStep === 1);
            els.next.classList.toggle('hidden', state.currentStep === 4);
            els.submit.classList.toggle('hidden', state.currentStep !== 4);
            updateReview();
        }

        function changeStep(step) {
            state.currentStep = Math.min(4, Math.max(1, step));
            persistDraft();
            renderStep();
        }

        function updateMeta(key, value) {
            state.meta[key] = value;
            persistDraft();
            updatePreview();
            updateReview();
            updateVisibilityUI();
        }

        function updateVisibility(value) {
            updateMeta('visibility', value);
        }

        function updateVisibilityUI() {
            els.scheduleBox.classList.toggle('hidden', state.meta.visibility !== 'scheduled');
        }

        function updateVideoInfo() {
            if (!state.videoFile) return;
            els.fileName.textContent = state.videoFile.name;
            els.fileSize.textContent = formatBytes(state.videoFile.size);
            els.processTime.textContent = estimateProcessingTime(state.videoFile.size);
            els.videoDropEmpty.classList.add('hidden');
            els.videoPreview.classList.remove('hidden');
            els.videoPreview.src = URL.createObjectURL(state.videoFile);
            updatePreview();
        }

        function updateThumbnailPreview() {
            if (state.thumbnailFile) {
                els.thumbnailPreview.src = URL.createObjectURL(state.thumbnailFile);
                els.thumbnailPreview.classList.remove('hidden');
                els.thumbnailEmpty.classList.add('hidden');
                els.previewCardImage.src = els.thumbnailPreview.src;
                els.previewCardImage.classList.remove('hidden');
                els.previewCardFallback.classList.add('hidden');
            } else {
                els.thumbnailPreview.classList.add('hidden');
                els.thumbnailEmpty.classList.remove('hidden');
                els.previewCardImage.classList.add('hidden');
                els.previewCardFallback.classList.remove('hidden');
            }
        }

        function updatePreview() {
            els.previewCardTitle.textContent = state.meta.title || 'Titolo del contenuto';
            els.previewCardMeta.textContent = state.meta.is_reel ? 'Reel pronto per feed verticale' : 'Video lungo in stile YouTube';
            els.kindPill.textContent = state.meta.is_reel ? 'Reel verticale' : 'Video classico';
            els.reviewKind.textContent = state.meta.is_reel ? 'Reel' : 'Video';
        }

        function updateReview() {
            const selectedPlaylists = playlistOptions.filter((playlist) => state.meta.playlist_ids.includes(playlist.id)).map((playlist) => playlist.title);
            const selectedCards = suggestedCandidates.filter((video) => state.meta.suggested_video_ids.includes(video.id)).map((video) => video.title);

            els.reviewTitle.textContent = state.meta.title || '-';
            els.reviewPlaylists.textContent = selectedPlaylists.length ? selectedPlaylists.join(', ') : 'Nessuna';
            els.reviewCards.textContent = selectedCards.length ? selectedCards.join(', ') : 'Nessuna';
            els.reviewVisibility.textContent = state.meta.visibility === 'scheduled'
                ? `Programmato: ${state.meta.scheduled_for || 'data non impostata'}`
                : (state.meta.visibility === 'private' ? 'Privato' : 'Pubblico');
        }

        function updateProgressUI() {
            const progress = Math.max(0, Math.min(100, state.progressPercent || 0));
            const statusMap = {
                idle: 'In attesa',
                preparing: 'Preparazione',
                pending: 'Sessione creata',
                uploading: 'Caricamento',
                uploaded: 'Upload completato',
                completed: 'Completato',
            };
            els.globalProgressBar.style.width = `${progress}%`;
            els.uploadProgressFill.style.width = `${progress}%`;
            els.globalProgressLabel.textContent = `${progress}%`;
            els.uploadStatusPill.textContent = statusMap[state.uploadStatus] || state.uploadStatus;
            els.uploadBytesText.textContent = `${formatBytes(state.uploadedBytes)} / ${state.videoFile ? formatBytes(state.videoFile.size) : '0 MB'}`;
            els.uploadStepText.textContent = state.totalChunks ? `${state.uploadedChunks.length}/${state.totalChunks} chunk` : 'Nessun caricamento';
        }

        function renderPlaylistList() {
            if (!playlistOptions.length) {
                els.playlistList.innerHTML = `<div class="rounded-2xl border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nessuna playlist disponibile. Potrai aggiungerlo piu tardi.</div>`;
                return;
            }

            els.playlistList.innerHTML = playlistOptions.map((playlist) => `
                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 px-4 py-3 transition hover:border-red-300 dark:border-gray-700">
                    <input type="checkbox" data-playlist-id="${playlist.id}" class="mt-1">
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">${escapeHtml(playlist.title)}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">${playlist.videos_count} video${playlist.videos_count === 1 ? '' : 's'}${playlist.description ? ` • ${escapeHtml(playlist.description)}` : ''}</span>
                    </span>
                </label>
            `).join('');

            els.playlistList.querySelectorAll('[data-playlist-id]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const id = Number(checkbox.dataset.playlistId);
                    if (checkbox.checked) {
                        state.meta.playlist_ids = [...new Set([...state.meta.playlist_ids, id])];
                    } else {
                        state.meta.playlist_ids = state.meta.playlist_ids.filter((value) => value !== id);
                    }
                    persistDraft();
                    updateReview();
                });
            });
        }

        function renderCardsList() {
            els.cardsList.innerHTML = suggestedCandidates.map((video) => `
                <label class="group overflow-hidden rounded-3xl border border-gray-200 bg-white transition hover:border-red-300 dark:border-gray-700 dark:bg-gray-900/70">
                    <div class="flex gap-4 p-4">
                        <input type="checkbox" data-card-id="${video.id}" class="mt-1">
                        <div class="h-20 w-32 shrink-0 overflow-hidden rounded-2xl bg-gray-200 dark:bg-gray-800">
                            ${video.thumbnail_url ? `<img src="${video.thumbnail_url}" alt="" class="h-full w-full object-cover">` : `<div class="flex h-full items-center justify-center text-gray-400"><i class="fas fa-photo-film"></i></div>`}
                        </div>
                        <div class="min-w-0">
                            <span class="block line-clamp-2 text-sm font-semibold text-gray-900 dark:text-white">${escapeHtml(video.title)}</span>
                            <span class="mt-2 block text-xs text-gray-500 dark:text-gray-400">${video.is_reel ? 'Reel' : 'Video'} • ${video.status}${video.published_at ? ` • ${video.published_at}` : ''}</span>
                        </div>
                    </div>
                </label>
            `).join('');

            els.cardsList.querySelectorAll('[data-card-id]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const id = Number(checkbox.dataset.cardId);
                    if (checkbox.checked) {
                        if (state.meta.suggested_video_ids.length >= maxCards) {
                            checkbox.checked = false;
                            showAlert(`Puoi scegliere al massimo ${maxCards} card suggerite.`, 'error');
                            return;
                        }
                        state.meta.suggested_video_ids = [...new Set([...state.meta.suggested_video_ids, id])];
                    } else {
                        state.meta.suggested_video_ids = state.meta.suggested_video_ids.filter((value) => value !== id);
                    }
                    els.cardsCount.textContent = `${state.meta.suggested_video_ids.length}/${maxCards}`;
                    persistDraft();
                    updateReview();
                });
            });
        }

        function syncAllFields() {
            els.titleInput.value = state.meta.title;
            els.descriptionInput.value = state.meta.description;
            els.tagsInput.value = state.meta.tags;
            els.languageInput.value = state.meta.language;
            els.kindVideo.checked = !state.meta.is_reel;
            els.kindReel.checked = !!state.meta.is_reel;
            els.commentsEnabled.checked = !!state.meta.comments_enabled;
            els.likesEnabled.checked = !!state.meta.likes_enabled;
            els.commentsApproval.checked = !!state.meta.comments_require_approval;
            els.visibilityPublic.checked = state.meta.visibility === 'public';
            els.visibilityPrivate.checked = state.meta.visibility === 'private';
            els.visibilityScheduled.checked = state.meta.visibility === 'scheduled';
            els.scheduledForInput.value = state.meta.scheduled_for;

            els.playlistList.querySelectorAll('[data-playlist-id]').forEach((checkbox) => {
                checkbox.checked = state.meta.playlist_ids.includes(Number(checkbox.dataset.playlistId));
            });
            els.cardsList.querySelectorAll('[data-card-id]').forEach((checkbox) => {
                checkbox.checked = state.meta.suggested_video_ids.includes(Number(checkbox.dataset.cardId));
            });
            els.cardsCount.textContent = `${state.meta.suggested_video_ids.length}/${maxCards}`;

            if (state.videoFile) {
                updateVideoInfo();
            }
            updateThumbnailPreview();
            updateVisibilityUI();
            updatePreview();
        }

        async function detectVideoKind(file) {
            const objectUrl = URL.createObjectURL(file);
            const video = document.createElement('video');
            video.preload = 'metadata';
            video.src = objectUrl;

            await new Promise((resolve) => {
                video.onloadedmetadata = resolve;
                video.onerror = resolve;
            });

            const isReel = video.videoHeight > video.videoWidth || (video.videoWidth / Math.max(video.videoHeight, 1)) <= 0.8;
            state.autoDetectedReel = isReel;
            updateMeta('is_reel', isReel);
            URL.revokeObjectURL(objectUrl);
        }

        async function validateCurrentStep() {
            if (state.currentStep === 1) {
                if (!state.videoFile) {
                    showAlert('Seleziona prima un file video o reel.', 'error');
                    return false;
                }
                if (!['uploaded', 'completed'].includes(state.uploadStatus)) {
                    showAlert('Attendi il completamento del caricamento prima di proseguire.', 'error');
                    return false;
                }
            }

            if (state.currentStep === 2) {
                if (!state.meta.title.trim()) {
                    showAlert('Inserisci un titolo prima di continuare.', 'error');
                    return false;
                }
            }

            if (state.currentStep === 4 && state.meta.visibility === 'scheduled' && !state.meta.scheduled_for) {
                showAlert('Imposta data e ora di pubblicazione.', 'error');
                return false;
            }

            return true;
        }

        async function validateAll() {
            if (!state.videoFile || !state.meta.title.trim()) {
                showAlert('Completa file e titolo prima della finalizzazione.', 'error');
                return false;
            }
            if (state.meta.visibility === 'scheduled' && !state.meta.scheduled_for) {
                showAlert('La programmazione richiede una data valida.', 'error');
                return false;
            }
            return true;
        }

        function persistDraft() {
            localStorage.setItem(stateKey, JSON.stringify({
                currentStep: state.currentStep,
                sessionToken: state.sessionToken,
                progressPercent: state.progressPercent,
                uploadStatus: state.uploadStatus,
                chunkSize: state.chunkSize,
                meta: state.meta,
            }));
        }

        async function clearDraft() {
            localStorage.removeItem(stateKey);
            await saveFileToDb('videoFile', null);
            await saveFileToDb('thumbnailFile', null);
        }

        function showAlert(message, type = 'info') {
            const styles = {
                success: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200',
                error: 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-200',
                info: 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-200',
            };
            els.alert.className = `mb-6 rounded-2xl border px-4 py-3 text-sm ${styles[type] || styles.info}`;
            els.alert.textContent = message;
            els.alert.classList.remove('hidden');
        }

        function formatBytes(bytes) {
            if (!bytes) return '0 MB';
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unit = 0;
            while (size >= 1024 && unit < units.length - 1) {
                size /= 1024;
                unit++;
            }
            return `${Math.round(size * 100) / 100} ${units[unit]}`;
        }

        function estimateProcessingTime(bytes) {
            const minutes = Math.max(1, Math.ceil((bytes / (1024 * 1024)) / 50));
            return minutes < 60 ? `${minutes} min` : `${Math.floor(minutes / 60)}h ${minutes % 60}m`;
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function openDb() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(dbName, 1);
                request.onupgradeneeded = () => {
                    request.result.createObjectStore(storeName);
                };
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async function saveFileToDb(key, file) {
            const db = await openDb();
            const tx = db.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            if (file) {
                store.put(file, key);
            } else {
                store.delete(key);
            }
            await new Promise((resolve, reject) => {
                tx.oncomplete = resolve;
                tx.onerror = () => reject(tx.error);
            });
            db.close();
        }

        async function readFileFromDb(key) {
            const db = await openDb();
            const tx = db.transaction(storeName, 'readonly');
            const store = tx.objectStore(storeName);
            const request = store.get(key);
            const result = await new Promise((resolve, reject) => {
                request.onsuccess = () => resolve(request.result || null);
                request.onerror = () => reject(request.error);
            });
            db.close();
            return result;
        }
    });
</script>
