<x-studio-layout>
    <div class="mb-8">
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 via-red-600 to-red-700 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative px-8 py-16 text-center">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold">
                        Modifica Video
                    </h1>
                </div>
                <p class="text-lg lg:text-xl mb-6 text-red-100">
                    Configura e personalizza il tuo contenuto
                </p>
                <div class="flex items-center justify-center gap-4 text-sm text-red-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Caricato il {{ $video->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ number_format($video->views_count) }} visualizzazioni</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ $video->formatted_duration ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-700 dark:text-red-300">
                        Sono presenti errori:
                    </h3>
                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <form action="{{ route('videos.update', $video) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-heading mr-2 text-red-500"></i>
                            Titolo del Video <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="title" name="title" type="text"
                                value="{{ old('title', $video->title) }}" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors @error('title') border-red-500 focus:ring-red-500 @enderror"
                                placeholder="Inserisci un titolo accattivante per il tuo video">
                            @error('title')
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @else
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-heading text-gray-400"></i>
                                </div>
                            @enderror
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="relative">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-align-left mr-2 text-red-500"></i>
                            Descrizione
                        </label>
                        <textarea id="description" name="description" rows="5"
                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors resize-none @error('description') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Descrivi il tuo video, racconta cosa impareranno gli spettatori...">{{ old('description', $video->description) }}</textarea>
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                            <span
                                id="description-count">{{ strlen(old('description', $video->description ?? '')) }}</span>
                            caratteri
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tags --}}
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tags mr-2 text-red-500"></i>
                            Tags
                        </label>
                        <div class="relative">
                            <input type="text" id="tags" name="tags"
                                value="{{ old('tags', is_array($video->tags) ? implode(', ', $video->tags) : '') }}"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors @error('tags') border-red-500 focus:ring-red-500 @enderror"
                                placeholder="Gaming, Tutorial, Programmazione, Tech (separati da virgola)">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-hashtag text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Aggiungi tag specifici per migliorare la scopribilità del tuo video
                        </p>
                        @error('tags')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Visibility --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            <i class="fas fa-eye mr-2 text-red-500"></i>
                            Visibilità
                        </label>
                        <input type="hidden" name="status" id="status-input"
                            value="{{ old('status', $video->status) }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Public --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="visibility" value="published" class="sr-only"
                                    {{ old('status', $video->status) === 'published' ? 'checked' : '' }}>
                                <div data-value="published"
                                    class="p-4 rounded-lg border-2 transition-all duration-300 {{ old('status', $video->status) === 'published' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-red-300 dark:hover:border-red-400' }}">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                Pubblico
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Chiunque può cercare e vedere questo video
                                            </p>
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <span class="font-medium text-green-600 dark:text-green-400">
                                                    Immediatamente disponibile
                                                </span>
                                            </div>
                                        </div>
                                        @if (old('status', $video->status) === 'published')
                                            <div class="text-red-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>

                            {{-- Private --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="visibility" value="draft" class="sr-only"
                                    {{ old('status', $video->status) === 'draft' ? 'checked' : '' }}>
                                <div data-value="draft"
                                    class="p-4 rounded-lg border-2 transition-all duration-300 {{ old('status', $video->status) === 'draft' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-red-300 dark:hover:border-red-400' }}">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                Non pubblico
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Solo tu puoi vedere questo video
                                            </p>
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">
                                                    Solo per te
                                                </span>
                                            </div>
                                        </div>
                                        @if (old('status', $video->status) === 'draft')
                                            <div class="text-red-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Video Settings --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-cog mr-2 text-red-500"></i>
                            Impostazioni di Interazione
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Configura come gli spettatori possono interagire con questo video
                        </p>

                        <div class="space-y-6">
                            {{-- Comments --}}
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4
                                        class="text-base font-semibold text-gray-900 dark:text-white flex items-center">
                                        <i class="fas fa-comments mr-2 text-red-500"></i>
                                        Commenti
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Consenti agli spettatori di commentare questo video
                                    </p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="comments_enabled" value="1"
                                        {{ old('comments_enabled', $video->comments_enabled ?? true) ? 'checked' : '' }}
                                        onchange="toggleCommentsApproval(this)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            {{-- Comments Approval --}}
                            <div id="comments-approval-section"
                                class="{{ old('comments_enabled', $video->comments_enabled ?? true) ? '' : 'hidden' }}">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white">
                                            Richiedi approvazione per i commenti
                                        </h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            I commenti dovranno essere approvati prima di essere pubblicati
                                        </p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="comments_require_approval" value="1"
                                            {{ old('comments_require_approval', $video->comments_require_approval ?? false) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            {{-- Likes --}}
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4
                                        class="text-base font-semibold text-gray-900 dark:text-white flex items-center">
                                        <i class="fas fa-thumbs-up mr-2 text-red-500"></i>
                                        Like e Dislike
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Consenti agli spettatori di mettere like e dislike
                                    </p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="likes_enabled" value="1"
                                        {{ old('likes_enabled', $video->likes_enabled ?? true) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="flex flex-wrap gap-3">
                                <button type="button" onclick="enableAll()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Abilita Tutto
                                </button>
                                <button type="button" onclick="enableCommentsWithApproval()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                    <i class="fas fa-user-shield mr-2"></i>
                                    Commenti + Approvazione
                                </button>
                                <button type="button" onclick="disableAllInteractions()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                    <i class="fas fa-ban mr-2"></i>
                                    Disabilita Tutto
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('videos.show', $video) }}" target="_blank"
                            class="flex items-center justify-center px-4 py-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Vai al Video
                        </a>

                        <a href="{{ route('channel.edit', Auth::user()->userProfile?->channel_name) }}?tab=content"
                            class="flex items-center justify-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Torna ai Contenuti
                        </a>

                        <button type="button" onclick="openDeleteModal()"
                            class="flex items-center justify-center px-4 py-2 text-red-600 hover:text-red-700 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors font-medium border border-red-200 dark:border-red-800">
                            <i class="fas fa-trash mr-2"></i>
                            Elimina Video
                        </button>

                        <button type="submit"
                            class="flex-1 flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                            <i class="fas fa-save mr-2"></i>
                            Salva modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Video Thumbnail --}}
            @if ($video->thumbnail_path)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                        <i class="fas fa-image mr-2 text-red-500"></i>
                        Thumbnail Video
                    </h4>
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                            alt="Thumbnail {{ $video->title }}" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            {{-- Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-red-500"></i>
                    Statistiche Video
                </h4>
                <div class="space-y-4">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $video->created_at->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Data Upload</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-lg font-bold text-red-600">
                            {{ number_format($video->views_count) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Visualizzazioni</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-lg font-bold text-green-600">
                            {{ number_format($video->likes_count ?? 0) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Mi Piace</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-lg font-bold text-blue-600">
                            {{ $video->formatted_duration ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Durata</div>
                    </div>
                </div>
            </div>

            {{-- Visibility Info --}}
            <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informazioni sulla visibilità
                </h4>
                <div id="public-info" class="{{ $video->status === 'published' ? '' : 'hidden' }}">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Il video sarà visibile sulla tua pagina del canale.
                        </p>
                    </div>
                    <div class="flex items-start gap-2 mt-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Apparirà nei risultati di ricerca.
                        </p>
                    </div>
                </div>
                <div id="private-info" class="{{ $video->status === 'draft' ? '' : 'hidden' }}">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-shield-alt text-blue-500 mt-1"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Il video sarà visibile solo a te.
                        </p>
                    </div>
                    <div class="flex items-start gap-2 mt-2">
                        <i class="fas fa-shield-alt text-blue-500 mt-1"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Non apparirà nei risultati di ricerca.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full mx-4 shadow-2xl">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Conferma Eliminazione
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Azione irreversibile</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    Sei sicuro di voler eliminare questo video? Questa azione non può essere annullata e tutti i dati
                    associati verranno persi definitivamente.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors font-medium">
                        Annulla
                    </button>
                    <form action="{{ route('videos.destroy', $video) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-2"></i>Elimina Video
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter for description
        const descriptionField = document.getElementById('description');
        const descriptionCount = document.getElementById('description-count');

        if (descriptionField && descriptionCount) {
            descriptionField.addEventListener('input', function() {
                descriptionCount.textContent = this.value.length;

                // Color coding based on length
                if (this.value.length > 500) {
                    descriptionCount.className = 'text-xs text-green-500';
                } else if (this.value.length > 200) {
                    descriptionCount.className = 'text-xs text-yellow-500';
                } else {
                    descriptionCount.className = 'text-xs text-gray-400';
                }
            });
        }

        // Delete modal functions
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        // Visibility toggle function
        function setVisibility(visibility) {
            const statusInput = document.getElementById('status-input');
            const publicInfo = document.getElementById('public-info');
            const privateInfo = document.getElementById('private-info');
            const labels = document.querySelectorAll('[data-value]');

            // Update hidden input
            statusInput.value = visibility === 'published' ? 'published' : 'draft';

            // Update visual states
            labels.forEach(label => {
                const labelValue = label.getAttribute('data-value');
                if (labelValue === visibility) {
                    label.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                    label.classList.remove('border-gray-200', 'dark:border-gray-600');
                } else {
                    label.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                    label.classList.add('border-gray-200', 'dark:border-gray-600');
                }
            });

            // Update info panel
            if (visibility === 'published') {
                publicInfo.classList.remove('hidden');
                privateInfo.classList.add('hidden');
            } else {
                privateInfo.classList.remove('hidden');
                publicInfo.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentStatus = '{{ $video->status }}';
            setVisibility(currentStatus);

            // Add click handlers for visibility radio buttons
            const visibilityRadios = document.querySelectorAll('input[name="visibility"]');
            visibilityRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        setVisibility(this.value);
                    }
                });
            });
        });

        // Close modal on outside click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Video interaction settings functions
        function toggleCommentsApproval(checkbox) {
            const approvalSection = document.getElementById('comments-approval-section');
            if (approvalSection) {
                if (checkbox.checked) {
                    approvalSection.classList.remove('hidden');
                } else {
                    approvalSection.classList.add('hidden');
                    // Uncheck approval requirement when comments are disabled
                    const approvalCheckbox = approvalSection.querySelector('input[name="comments_require_approval"]');
                    if (approvalCheckbox) {
                        approvalCheckbox.checked = false;
                    }
                }
            }
        }

        function enableAll() {
            const commentsEnabled = document.querySelector('input[name="comments_enabled"]');
            const likesEnabled = document.querySelector('input[name="likes_enabled"]');
            const commentsRequireApproval = document.querySelector('input[name="comments_require_approval"]');

            if (commentsEnabled) commentsEnabled.checked = true;
            if (likesEnabled) likesEnabled.checked = true;
            if (commentsRequireApproval) commentsRequireApproval.checked = false;

            const approvalSection = document.getElementById('comments-approval-section');
            if (approvalSection) {
                approvalSection.classList.remove('hidden');
            }
        }

        function enableCommentsWithApproval() {
            const commentsEnabled = document.querySelector('input[name="comments_enabled"]');
            const likesEnabled = document.querySelector('input[name="likes_enabled"]');
            const commentsRequireApproval = document.querySelector('input[name="comments_require_approval"]');

            if (commentsEnabled) commentsEnabled.checked = true;
            if (likesEnabled) likesEnabled.checked = true;
            if (commentsRequireApproval) commentsRequireApproval.checked = true;

            const approvalSection = document.getElementById('comments-approval-section');
            if (approvalSection) {
                approvalSection.classList.remove('hidden');
            }
        }

        function disableAllInteractions() {
            const commentsEnabled = document.querySelector('input[name="comments_enabled"]');
            const likesEnabled = document.querySelector('input[name="likes_enabled"]');
            const commentsRequireApproval = document.querySelector('input[name="comments_require_approval"]');

            if (commentsEnabled) commentsEnabled.checked = false;
            if (likesEnabled) likesEnabled.checked = false;
            if (commentsRequireApproval) commentsRequireApproval.checked = false;

            const approvalSection = document.getElementById('comments-approval-section');
            if (approvalSection) {
                approvalSection.classList.add('hidden');
            }
        }
    </script>
</x-studio-layout>
