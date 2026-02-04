<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-heart mr-3 text-red-600"></i>Iscrizioni
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Canali a cui sei iscritto
            </p>
        </div>

        <!-- Subscriptions List -->
        @if(isset($subscriptions) && $subscriptions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($subscriptions as $channel)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <!-- Avatar -->
                            @if($channel->userProfile && $channel->userProfile->avatar_url)
                                <img 
                                    src="{{ $channel->userProfile->avatar_url }}" 
                                    alt="{{ $channel->name }}"
                                    class="w-16 h-16 rounded-full object-cover"
                                >
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold">
                                        {{ strtoupper(substr($channel->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $channel->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $channel->subscribers()->count() }} iscritti
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a 
                                href="{{ route('channel.show', $channel) }}"
                                class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm text-center"
                            >
                                <i class="fas fa-user mr-2"></i>Canale
                            </a>
                            <button 
                                class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors text-sm"
                            >
                                <i class="fas fa-bell-slash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Latest Videos from Subscriptions -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Ultimi video
                </h2>

                @if(isset($latestVideos) && $latestVideos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($latestVideos as $video)
                            <a href="{{ route('videos.show', $video) }}" class="group">
                                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                                    <!-- Thumbnail -->
                                    <div class="relative aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        @if($video->thumbnail_url)
                                            <img 
                                                src="{{ $video->thumbnail_url }}" 
                                                alt="{{ $video->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-video text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                        
                                        @if($video->duration)
                                            <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-2 py-1 rounded">
                                                {{ gmdate('i:s', $video->duration) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Video Info -->
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">
                                            {{ $video->title }}
                                        </h3>
                                        
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $video->user->name }}
                                            </span>
                                        </div>

                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span>
                                                <i class="fas fa-eye mr-1"></i>
                                                {{ number_format($video->views_count) }}
                                            </span>
                                            <span>
                                                {{ $video->published_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-600 dark:text-gray-400 py-8">
                        Nessun nuovo video dai canali a cui sei iscritto
                    </p>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-heart text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Nessuna iscrizione
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Iscriviti ai canali per vedere i loro video qui
                </p>
                <a 
                    href="{{ route('explore') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                >
                    <i class="fas fa-compass mr-2"></i>
                    Esplora canali
                </a>
            </div>
        @endif
    </div>
</x-layout>
