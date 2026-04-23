<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-clock mr-3 text-red-600"></i>{{ __('ui.watch_later') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.watch_later_subtitle') }}
            </p>
        </div>

        <!-- Watch Later Videos Grid -->
        @if (isset($watchLaterVideos) && $watchLaterVideos->count() > 0)
            <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
                @foreach ($watchLaterVideos as $video)
                    <div class="group relative">
                        <x-video :video="$video" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            {{-- <div class="mt-8">
                {{ $watchLaterVideos->links() }}
            </div> --}}
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-clock text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('ui.no_saved_videos') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.save_videos') }}
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
