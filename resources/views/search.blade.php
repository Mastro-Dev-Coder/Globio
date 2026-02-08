<x-layout>
    <div class="max-w-7xl mx-auto">
        @php
            $totalResults = $videoResults->total() + $reelResults->total() + $userResults->total();
        @endphp
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">
                <i class="fas fa-search mr-4 text-red-600"></i>{{ __('ui.search_results_for') }} "<span
                    class="text-red-600">{{ $query }}</span>"
            </h1>
            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-400">
                <p class="text-lg">
                    {{ $totalResults }} {{ $totalResults === 1 ? __('ui.result_found') : __('ui.results_found') }}
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
                    {{ __('ui.sort_videos_reels') }}:
                </label>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" name="sort" value="relevance"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'relevance'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-search text-xs"></i>
                        {{ __('ui.relevance') }}
                    </button>
                    <button type="submit" name="sort" value="upload_date"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'upload_date'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-calendar text-xs"></i>
                        {{ __('ui.upload_date') }}
                    </button>
                    <button type="submit" name="sort" value="view_count"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'view_count'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-eye text-xs"></i>
                        {{ __('ui.view_count') }}
                    </button>
                    <button type="submit" name="sort" value="rating"
                        class="px-5 py-2.5 rounded-full border-2 transition-all duration-300 font-medium text-sm flex items-center gap-2
                        {{ $sortBy === 'rating'
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white border-red-600 shadow-lg transform hover:scale-105'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:transform hover:scale-105' }}">
                        <i class="fas fa-star text-xs"></i>
                        {{ __('ui.rating') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if ($totalResults > 0)
            @if ($videoResults->count() > 0)
                <div class="space-y-6 mb-8">
                    @foreach ($videoResults as $video)
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
                                                <i class="fas fa-circle text-xs mr-1"></i>{{ strtoupper(__('ui.live')) }}
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
                                                {{ number_format($video->views_count) }} {{ __('ui.views_count') }}
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
                                                        title="{{ __('ui.channel_verified') }}">
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
                @if ($videoResults->hasPages())
                    <div class="mt-8">
                        {{ $videoResults->appends(request()->except('videos_page'))->links() }}
                    </div>
                @endif
            @endif

            @if ($reelResults->count() > 0)
                <div class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-film mr-2 text-purple-600"></i>{{ __('ui.reels') }}
                        </h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trans_choice('ui.results_count', $reelResults->total(), ['count' => $reelResults->total()]) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach ($reelResults as $reel)
                            <a href="{{ route('reels.show', $reel) }}"
                                class="flex gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-lg transition-all">
                                <div
                                    class="w-32 h-20 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                    @if ($reel->thumbnail_path)
                                        <img src="{{ asset('storage/' . $reel->thumbnail_path) }}"
                                            alt="{{ $reel->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-film text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $reel->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ number_format($reel->views_count) }} {{ __('ui.views_count') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                        {{ $reel->user->userProfile?->channel_name ?? $reel->user->name }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($reelResults->hasPages())
                        <div class="mt-8">
                            {{ $reelResults->appends(request()->except('reels_page'))->links() }}
                        </div>
                    @endif
                </div>
            @endif

            @if ($userResults->count() > 0)
                <div class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-users mr-2 text-blue-600"></i>{{ __('ui.creators_users') }}
                        </h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trans_choice('ui.results_count', $userResults->total(), ['count' => $userResults->total()]) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($userResults as $user)
                            @php
                                $profile = $user->userProfile;
                                $channelSlug = $profile?->channel_name ?? ($profile?->username ?? $user->id);
                            @endphp
                            <a href="{{ route('channel.show', $channelSlug) }}"
                                class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-lg transition-all">
                                @if ($profile && $profile->avatar_url)
                                    <img src="{{ asset('storage/' . $profile->avatar_url) }}"
                                        alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                        <span class="text-white font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $profile?->channel_name ?? $user->name }}
                                    </p>
                                    @if ($profile && $profile->username)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ '@' . $profile->username }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ number_format($profile?->subscribers_count ?? 0) }} {{ __('ui.subscribers_count') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($userResults->hasPages())
                        <div class="mt-8">
                            {{ $userResults->appends(request()->except('users_page'))->links() }}
                        </div>
                    @endif
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div
                class="text-center py-20 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    {{ __('ui.no_results_found') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-lg leading-relaxed max-w-md mx-auto">
                    {{ __('ui.try_different_keywords') }}, {{ __('ui.check_spelling') }}.
                    <span class="text-sm mt-3 block opacity-75">
                        <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                        {{ __('ui.suggestion') }}: {{ __('ui.use_specific_keywords') }}
                    </span>
                </p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    {{ __('ui.back_to_home') }}
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
