<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-video mr-3 text-red-600"></i>I miei video
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Gestisci i tuoi video caricati
            </p>
        </div>

        <!-- Upload Button -->
        <div class="mb-6">
            <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Carica nuovo video
            </a>
        </div>

        <!-- Videos List -->
        @if ($videos->count() > 0)
            <div class="space-y-4">
                @foreach ($videos as $video)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Thumbnail -->
                            <div class="md:w-64 flex-shrink-0">
                                <div
                                    class="relative aspect-video bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    @if ($video->thumbnail_path)
                                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                            alt="{{ $video->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-video text-4xl text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Video Info -->
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ $video->title }}
                                </h3>

                                @if ($video->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                        {{ $video->description }}
                                    </p>
                                @endif

                                <div
                                    class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ number_format($video->views_count ?? 0) }} visualizzazioni
                                    </span>
                                    <span>
                                        <i class="fas fa-thumbs-up mr-1"></i>
                                        {{ number_format($video->likes_count ?? 0) }} like
                                    </span>
                                    <span>
                                        <i class="fas fa-comment mr-1"></i>
                                        {{ number_format($video->comments_count ?? 0) }} commenti
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        @if($video->published_at)
                                                                    {{ $video->published_at->format('d/m/Y') }}
                                                                @else
                                                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">Ancora in elaborazione</span>
                                                                @endif
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('videos.show', $video) }}"
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                                        <i class="fas fa-eye mr-2"></i>Visualizza
                                    </a>
                                    <a href="{{ route('videos.edit', $video) }}"
                                        class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors text-sm">
                                        <i class="fas fa-edit mr-2"></i>Modifica
                                    </a>
                                    <button
                                        onclick="if(confirm('Sei sicuro di voler eliminare questo video?')) { document.getElementById('delete-form-{{ $video->id }}').submit(); }"
                                        class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors text-sm">
                                        <i class="fas fa-trash mr-2"></i>Elimina
                                    </button>
                                    <form id="delete-form-{{ $video->id }}"
                                        action="{{ route('videos.destroy', $video) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-video text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Nessun video caricato
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Inizia a condividere i tuoi contenuti con la community
                </p>
                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Carica il tuo primo video
                </a>
            </div>
        @endif
    </div>
</x-layout>
