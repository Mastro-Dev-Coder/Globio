<div class="group cursor-pointer relative">
    <a href="{{ route('videos.show', $video) }}">
        <div class="relative overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 aspect-video mb-4">
            @if ($video->thumbnail_url)
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                </div>
            @endif

            <!-- Professional Duration Badge -->
            <div class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2.5 py-1.5 rounded-lg font-medium border border-white/10">
                {{ $video->formatted_duration }}
            </div>

            <!-- Modern Hover Play Button -->
            <div class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                <div class="w-16 h-16 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 border border-white/20">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex space-x-3">
            <!-- Modern User Avatar -->
            <div class="flex-shrink-0">
                @if ($video->user->userProfile?->avatar_url)
                    <img src="{{ $video->user->userProfile->avatar_url }}" alt="{{ $video->user->name }}"
                        class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                @else
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800">
                        <span class="text-white text-sm font-medium">
                            {{ strtoupper(substr($video->user->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors duration-200 mb-1">
                    {{ $video->title }}
                </h3>
                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-1">
                    <a href="{{ route('channel.show', $video->user) }}"
                        class="hover:text-gray-900 dark:hover:text-white transition-colors font-medium">
                        {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                    </a>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ number_format($video->views_count) }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $video->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </a>

    <!-- Professional Action Buttons -->
    @auth
        <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
            <!-- Modern Watch Later Button -->
            <button 
                class="w-9 h-9 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl flex items-center justify-center hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl"
                title="Aggiungi a Guarda più tardi"
                onclick="event.preventDefault(); addToWatchLater({{ $video->id }}, this)"
            >
                <i class="fas fa-bookmark text-sm"></i>
            </button>

            <!-- Modern Add to Playlist Button -->
            <button 
                class="w-9 h-9 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl"
                title="Aggiungi a Playlist"
                onclick="event.preventDefault(); showAddToPlaylistModal({{ $video->id }})"
            >
                <i class="fas fa-folder-plus text-sm"></i>
            </button>
        </div>
    @endauth
</div>
