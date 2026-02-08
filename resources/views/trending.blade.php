<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-fire mr-3 text-red-600"></i>{{ __('ui.trending_page') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.popular_last_7_days') }}
            </p>
        </div>

        <!-- Videos Grid -->
        @if($videos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($videos as $video)
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
                                
                                <!-- Duration Badge -->
                                @if($video->duration)
                                    <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-2 py-1 rounded">
                                        {{ gmdate('i:s', $video->duration) }}
                                    </div>
                                @endif

                                <!-- Trending Badge -->
                                <div class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-fire mr-1"></i>
                                    Trending
                                </div>
                            </div>

                            <!-- Video Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">
                                    {{ $video->title }}
                                </h3>
                                
                                <!-- Channel Info -->
                                <div class="flex items-center space-x-2 mb-2">
                                    @if($video->user->userProfile && $video->user->userProfile->avatar_url)
                                        <img 
                                            src="{{ $video->user->userProfile->avatar_url }}" 
                                            alt="{{ $video->user->name }}"
                                            class="w-6 h-6 rounded-full object-cover"
                                        >
                                    @else
                                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">
                                                {{ strtoupper(substr($video->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $video->user->name }}
                                    </span>
                                </div>

                                <!-- Stats -->
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ number_format($video->views_count) }}
                                    </span>
                                    <span>
                                        <i class="fas fa-thumbs-up mr-1"></i>
                                        {{ number_format($video->likes_count) }}
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

            <!-- Pagination -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-fire text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('ui.no_trending_videos') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.no_trending_now') }}
                </p>
                <a 
                    href="{{ route('home') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                >
                    <i class="fas fa-home mr-2"></i>
                    {{ __('ui.go_home') }}
                </a>
            </div>
        @endif
    </div>
</x-layout>
