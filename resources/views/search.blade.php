<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">
                <i class="fas fa-search mr-4 text-red-600"></i>Risultati per "<span
                    class="text-red-600">{{ $query }}</span>"
            </h1>
            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-400">
                <p class="text-lg">
                    {{ $results->total() }} {{ $results->total() === 1 ? 'risultato trovato' : 'risultati trovati' }}
                </p>
                <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
                <p class="text-sm">
                    <i class="fas fa-clock mr-1"></i>
                    {{ now()->diffForHumans() }}
                </p>
            </div>
        </div>

        <!-- Sort Options -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('search') }}" class="flex flex-wrap items-center gap-6">
                <input type="hidden" name="q" value="{{ $query }}">

                <label class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class="fas fa-sort mr-2 text-red-600"></i>
                    Ordina risultati:
                </label>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" name="sort" value="relevance"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'relevance'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-search text-xs"></i>
                        Rilevanza
                    </button>
                    <button type="submit" name="sort" value="upload_date"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'upload_date'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-calendar text-xs"></i>
                        Data di caricamento
                    </button>
                    <button type="submit" name="sort" value="view_count"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'view_count'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-eye text-xs"></i>
                        Visualizzazioni
                    </button>
                    <button type="submit" name="sort" value="rating"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'rating'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-star text-xs"></i>
                        Valutazione
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if ($results->count() > 0)
            <div class="space-y-6 mb-8">
                @foreach ($results as $video)
                    <div class="group" data-video-id="{{ $video->id }}"
                        data-category="{{ $video->category ?? 'general' }}">
                        <a href="{{ route('videos.show', $video) }}" class="block">
                            <div
                                class="flex gap-5 p-5 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl transition-all duration-400 hover:transform hover:-translate-y-1 hover:shadow-2xl hover:shadow-red-500/10 dark:hover:shadow-red-500/5 hover:border-red-300 dark:hover:border-red-600/50">
                                <!-- Thumbnail -->
                                <div
                                    class="relative w-96 min-w-96 h-56 rounded-xl overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex-shrink-0 shadow-lg">
                                    @if ($video->thumbnail_path)
                                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                            alt="{{ $video->title }}"
                                            class="w-full h-full object-cover transition-transform duration-400 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-video text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    <!-- Overlay gradient -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    </div>

                                    <!-- Badge HD/4K -->
                                    @if ($video->quality && in_array($video->quality, ['1080p', '4K']))
                                        <div
                                            class="absolute top-3 left-3 bg-emerald-500/90 backdrop-blur-sm text-white px-2 py-1 rounded text-xs font-semibold uppercase tracking-wide">
                                            {{ $video->quality }}
                                        </div>
                                    @endif

                                    <!-- Badge Live -->
                                    @if ($video->is_live)
                                        <div
                                            class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm text-white px-2 py-1 rounded text-xs font-semibold uppercase tracking-wide animate-pulse">
                                            <i class="fas fa-circle text-xs mr-1"></i>LIVE
                                        </div>
                                    @endif

                                    <!-- Duration -->
                                    @if ($video->duration && !$video->is_live)
                                        <div
                                            class="absolute bottom-3 right-3 bg-black/90 backdrop-blur-sm text-white px-2 py-1 rounded text-xs font-semibold">
                                            {{ gmdate('i:s', $video->duration) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Video Info -->
                                <div class="flex-1 min-w-0">
                                    <h3
                                        class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-3 line-clamp-2 group-hover:text-red-600 transition-colors duration-200">
                                        {{ $video->title }}
                                    </h3>

                                    <!-- Meta -->
                                    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
                                        <span
                                            class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-eye text-xs"></i>
                                            {{ number_format($video->views_count) }} visualizzazioni
                                        </span>
                                        <span
                                            class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-thumbs-up text-xs"></i>
                                            {{ number_format($video->likes_count) }}
                                        </span>
                                        <span
                                            class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-calendar text-xs"></i>
                                            {{ $video->published_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Channel -->
                                    <div class="flex items-center gap-3 mb-4">
                                        @if ($video->user->userProfile && $video->user->userProfile->avatar_url)
                                            <img src="{{ asset('storage/' . $video->user->userProfile->avatar_url) }}"
                                                alt="{{ $video->user->name }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-transparent bg-gradient-to-r from-white to-gray-100 group-hover:border-red-500 transition-all duration-300 group-hover:scale-110">
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-2 border-transparent group-hover:border-red-500 transition-all duration-300 group-hover:scale-110">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ strtoupper(substr($video->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <span
                                            class="text-gray-700 dark:text-gray-300 font-medium hover:text-red-600 cursor-pointer transition-colors flex items-center gap-1">
                                            {{ $video->user->name }}
                                            @if ($video->user->userProfile && $video->user->userProfile->is_verified)
                                                <span
                                                    class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs"
                                                    title="Canale verificato">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Description -->
                                    @if ($video->description)
                                        <p
                                            class="text-gray-600 dark:text-gray-400 text-base line-clamp-2 leading-relaxed">
                                            {{ $video->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $results->appends(['q' => $query, 'sort' => $sortBy])->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div
                class="text-center py-20 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Nessun risultato trovato
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-lg leading-relaxed max-w-md mx-auto">
                    Prova con parole chiave diverse, più generiche o controlla l'ortografia.
                    <span class="text-sm mt-3 block opacity-75">
                        <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                        Suggerimento: Usa parole chiave più specifiche o termini correlati
                    </span>
                </p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Torna alla Home
                </a>
            </div>
        @endif
    </div>

    <!-- Custom styles for line clamping -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-layout>
