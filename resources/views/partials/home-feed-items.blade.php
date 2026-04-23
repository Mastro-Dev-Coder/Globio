@php
    $videoUrl = fn($video) => route('videos.show', $video);
    $videoThumb = fn($video) => $video->thumbnail_url;
    $videoPreview = function ($video) {
        if (!$video?->video_path) {
            return null;
        }

        return $video->video_file_url;
    };

    $avatar = function ($user) {
        $path = $user?->userProfile?->avatar_url;
        return $path ? asset('storage/' . $path) : null;
    };

    $playlistThumb = function ($playlist) {
        if ($playlist->dynamic_thumbnail_url) {
            return $playlist->dynamic_thumbnail_url;
        }

        if ($playlist->thumbnail_path) {
            if (str_starts_with($playlist->thumbnail_path, 'http://') || str_starts_with($playlist->thumbnail_path, 'https://')) {
                return $playlist->thumbnail_path;
            }

            return asset('storage/' . ltrim($playlist->thumbnail_path, '/'));
        }

        return null;
    };

    $playlistBadge = function ($source) {
        return match ($source) {
            'auto' => 'Mix for you',
            'library' => 'From your library',
            default => 'Recommended',
        };
    };
@endphp

@foreach ($feedItems as $item)
    @if ($item['type'] === 'video' || $item['type'] === 'continue_video')
        @php
            $video = $item['content'];
            $isContinue = $item['type'] === 'continue_video';
            $progress = $isContinue ? (int) ($item['progress'] ?? 0) : 0;
            $completed = $isContinue ? (bool) ($item['completed'] ?? false) : false;
        @endphp
        <article class="group" data-feed-item="video" data-color-card data-preview-card style="--hover-color: rgba(120, 120, 120, 0.12)">
            <a href="{{ $videoUrl($video) }}" class="block rounded-xl px-1.5 py-1.5 transition-colors" data-thumb-link>
                <div class="relative mb-3 overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-800 aspect-video" data-thumb-holder>
                    @if ($videoThumb($video))
                        <img src="{{ $videoThumb($video) }}" alt="{{ $video->title }}"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" data-color-thumb data-preview-image>
                    @else
                        <div class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-video text-2xl"></i>
                        </div>
                    @endif
                    @if ($videoPreview($video))
                        <video
                            src="{{ $videoPreview($video) }}"
                            class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-200"
                            muted
                            loop
                            playsinline
                            preload="none"
                            data-preview-video></video>
                    @endif
                    <span class="absolute bottom-2 right-2 rounded bg-black/85 px-2 py-1 text-xs font-medium text-white">{{ $video->formatted_duration }}</span>
                    @if ($isContinue)
                        <span class="absolute left-2 top-2 rounded bg-black/85 px-2 py-1 text-[11px] font-medium text-white">
                            {{ $completed ? 'Watched' : 'Continue watching' }}
                        </span>
                        <div class="absolute inset-x-0 bottom-0 h-1.5 bg-white/35">
                            <div class="h-full bg-red-600" style="width: {{ $progress }}%"></div>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        @if ($avatar($video->user))
                            <img src="{{ $avatar($video->user) }}" alt="{{ $video->user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-red-600 text-sm font-bold text-white">
                                {{ strtoupper(substr($video->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h3 class="line-clamp-2 text-[15px] font-semibold leading-5 text-gray-900 group-hover:text-red-600 dark:text-white dark:group-hover:text-red-400">{{ $video->title }}</h3>
                        <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">{{ $video->user->userProfile?->channel_name ?: $video->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($video->views_count) }} views • {{ $video->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
        </article>
    @elseif ($item['type'] === 'playlist')
        @php
            $playlist = $item['content'];
            $playlistVideoCount = $playlist->videos->count() ?: ($playlist->video_count ?? 0);
            $layerCount = min(4, max(1, (int) ceil($playlistVideoCount / 4)));
            $playlistPreviewVideos = $playlist->videos->take(3);
        @endphp
        <article class="group" data-feed-item="playlist" data-color-card style="--hover-color: rgba(120, 120, 120, 0.12)">
            <a href="{{ route('playlists.show', $playlist) }}" class="block rounded-xl px-1.5 py-1.5">
                <div class="relative mb-3 h-0 pb-[56.25%]" aria-hidden="true">
                    @if ($layerCount >= 4)
                        <div class="absolute inset-0 translate-y-5 scale-[0.88] rounded-xl bg-gray-300/70 shadow-xl dark:bg-gray-700/65"></div>
                    @endif
                    @if ($layerCount >= 3)
                        <div class="absolute inset-0 translate-y-3.5 scale-[0.92] rounded-xl bg-gray-300/78 shadow-lg dark:bg-gray-700/72"></div>
                    @endif
                    @if ($layerCount >= 2)
                        <div class="absolute inset-0 translate-y-1.5 scale-[0.96] rounded-xl bg-gray-300/88 shadow-md dark:bg-gray-700/82"></div>
                    @endif
                    <div class="absolute inset-0 overflow-hidden rounded-xl bg-gray-200 shadow-sm dark:bg-gray-800">
                    @if ($playlistThumb($playlist))
                        <img src="{{ $playlistThumb($playlist) }}" alt="{{ $playlist->title }}"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" data-color-thumb>
                                @else
                            <div class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-list text-2xl"></i>
                            </div>
                        @endif
                        <span class="absolute left-2 top-2 rounded bg-black/80 px-2 py-1 text-[11px] font-medium text-white">{{ $playlistBadge($playlist->feed_source ?? 'recommended') }}</span>
                        <span class="absolute bottom-2 right-2 rounded bg-black/80 px-2 py-1 text-[11px] font-medium text-white">{{ $playlistVideoCount }} videos</span>

                        @if ($playlistPreviewVideos->count() > 1)
                            <div class="absolute bottom-2 left-2 flex gap-1">
                                @foreach ($playlistPreviewVideos as $previewVideo)
                                    @if ($previewVideo->thumbnail_url)
                                        <img src="{{ $previewVideo->thumbnail_url }}"
                                            alt="{{ $previewVideo->title }}"
                                            class="h-7 w-10 rounded object-cover ring-1 ring-white/70 dark:ring-black/60">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        @if ($avatar($playlist->user))
                            <img src="{{ $avatar($playlist->user) }}" alt="{{ $playlist->user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-red-600 text-sm font-bold text-white">
                                {{ strtoupper(substr($playlist->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h3 class="truncate text-[15px] font-semibold text-gray-900 group-hover:text-red-600 dark:text-white dark:group-hover:text-red-400">{{ $playlist->title }}</h3>
                        <p class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $playlist->user->userProfile?->channel_name ?: $playlist->user->name }}</p>
                        <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $playlist->description ?: 'Smart mix generated from watched and related videos.' }}</p>
                    </div>
                </div>
            </a>
        </article>
    @elseif ($item['type'] === 'reels_carousel')
        @php $carouselId = 'reels-carousel-' . uniqid(); @endphp
        <section class="col-span-full mb-2" data-feed-item="reels-row">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reels</h3>
                <a href="{{ route('reels.index') }}" class="text-sm font-medium text-red-600 hover:text-red-500">Open reels</a>
            </div>

            <div class="relative">
                <button type="button" data-carousel-target="{{ $carouselId }}" data-carousel-dir="prev"
                    class="hidden md:flex absolute left-2 top-1/2 z-10 h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow hover:bg-white dark:bg-gray-800/90 dark:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <button type="button" data-carousel-target="{{ $carouselId }}" data-carousel-dir="next"
                    class="hidden md:flex absolute right-2 top-1/2 z-10 h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow hover:bg-white dark:bg-gray-800/90 dark:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>

                <div id="{{ $carouselId }}" class="flex gap-3 overflow-x-auto pb-1 scroll-smooth snap-x snap-mandatory" data-reels-carousel>
                    @foreach ($item['items'] as $reel)
                        <article class="group w-[48%] min-w-[48%] shrink-0 snap-start md:w-[31%] md:min-w-[31%] lg:w-[24%] lg:min-w-[24%] 2xl:w-[32%] 2xl:min-w-[32%]"
                            data-color-card style="--hover-color: rgba(120, 120, 120, 0.12)">
                            <a href="{{ $videoUrl($reel) }}" class="block rounded-xl px-1.5 py-1.5 transition-colors">
                                <div class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-800 aspect-[9/16]">
                                    @if ($videoThumb($reel))
                                        <img src="{{ $videoThumb($reel) }}" alt="{{ $reel->title }}"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                            data-color-thumb>
                                    @endif
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/45 to-transparent p-3 text-white">
                                        <p class="line-clamp-2 text-sm font-semibold">{{ $reel->title }}</p>
                                        <p class="text-xs text-white/80">{{ number_format($reel->views_count) }} views</p>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endforeach

