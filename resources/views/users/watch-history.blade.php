<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-history mr-3 text-red-600"></i>{{ __('ui.watch_history') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('ui.history_subtitle') }}
                </p>
            </div>

            @if (isset($history) && $history->count() > 0)
                <form action="{{ route('history.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('{{ __('ui.clear_history_confirm') }}')"
                        class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        {{ __('ui.clear_history') }}
                    </button>
                </form>
            @endif
        </div>

        <!-- History List -->
        @if (isset($history) && $history->count() > 0)
            <div class="space-y-4">
                @foreach ($history as $item)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Thumbnail -->
                            <a href="{{ route('videos.show', $item->video) }}" class="md:w-64 flex-shrink-0">
                                <div
                                    class="relative aspect-video bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden group">
                                    @if ($item->video->thumbnail_url)
                                        <img src="{{ asset('storage/' . $item->video->thumbnail_path) }}"
                                            alt="{{ $item->video->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-video text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    @if ($item->video->duration)
                                        <div
                                            class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-2 py-1 rounded">
                                            {{ gmdate('i:s', $item->video->duration) }}
                                        </div>
                                    @endif

                                    <!-- Progress Bar -->
                                    @if ($item->watch_progress > 0)
                                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-300">
                                            <div class="h-full bg-red-600"
                                                style="width: {{ min(100, ($item->watch_progress / $item->video->duration) * 100) }}%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>

                            <!-- Video Info -->
                            <div class="flex-1">
                                <a href="{{ route('videos.show', $item->video) }}">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-2 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                        {{ $item->video->title }}
                                    </h3>
                                </a>

                                <div class="flex items-center space-x-2 mb-2">
                                    <a href="{{ route('channel.show', $item->video->user->userProfile->channel_name) }}"
                                        class="flex items-center space-x-2 group">
                                        @if ($item->video->user->userProfile && $item->video->user->userProfile->avatar_url)
                                            <img src="{{ asset('storage/' . $item->video->user->userProfile->avatar_url) }}"
                                                alt="{{ $item->video->user->userProfile->channel_name }}"
                                                class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">
                                                    {{ strtoupper(substr($item->video->user->userProfile->channel_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif

                                        <span class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-200">
                                            {{ $item->video->user->userProfile->channel_name }}
                                        </span>
                                    </a>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ number_format($item->video->views_count) }} {{ __('ui.views_count') }}
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ __('ui.watched') }} {{ $item->updated_at->diffForHumans() }}
                                    </span>
                                </div>

                                @if ($item->video->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                                        {{ $item->video->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $history->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-history text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('ui.no_history') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.history_empty') }}
                </p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-home mr-2"></i>
                    {{ __('ui.explore_videos') }}
                </a>
            </div>
        @endif
    </div>
</x-layout>
