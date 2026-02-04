<div class="group cursor-pointer relative p-1 sm:p-2 rounded-xl w-full max-w-full" data-color-wrapper-playlist>
    <a href="{{ route('playlists.show', $playlist->id) }}">
        <div class="relative overflow-hidden w-full rounded-xl bg-gray-100 dark:bg-gray-800 aspect-video mb-3 transition-all duration-300"
            data-thumbnail-playlist style="--hover-bg: rgba(255, 0, 0, 0.35);">
            <!-- Badge per playlist dell'utente -->
            @if (isset($source) && $source === 'user')
                <div class="absolute top-3 left-3 z-10">
                    <span
                        class="bg-blue-600 text-white text-xs px-2.5 py-1 rounded-lg font-medium shadow-lg flex items-center gap-1">
                        <i class="fas fa-folder-open"></i>
                        Le tue playlist
                    </span>
                </div>
            @endif
            <!-- Playlist Thumbnail -->
            @if ($playlist->videos->count() > 0)
                @php
                    $firstVideo = $playlist->videos->first();
                @endphp
                @if ($firstVideo->thumbnail_path)
                    <img src="{{ asset('storage/' . $firstVideo->thumbnail_path) }}" alt="{{ $playlist->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div
                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-2 bg-white/30 dark:bg-black/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-folder text-2xl text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Playlist</p>
                        </div>
                    </div>
                @endif
            @else
                <div
                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                    <div class="text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-white/30 dark:bg-black/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-folder text-2xl text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Playlist</p>
                    </div>
                </div>
            @endif

            <!-- Video Count Badge -->
            @php $videoCount = $playlist->videos->count() ?: ($playlist->video_count ?? 0); @endphp
            @if ($videoCount > 0)
            <div
                class="absolute bottom-3 left-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2.5 py-1.5 rounded-lg font-medium border border-white/10">
                <i class="fas fa-video mr-1"></i>{{ $videoCount }} {{ $videoCount == 1 ? 'video' : 'video' }}
            </div>
            @endif

            <!-- Views Count Badge -->
            <div
                class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2.5 py-1.5 rounded-lg font-medium border border-white/10">
                <i class="fas fa-eye mr-1"></i>{{ number_format($playlist->views_count) }}
            </div>

            <!-- Hover Play Button -->
            <div
                class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                <div
                    class="w-16 h-16 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 border border-white/30 shadow-2xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </div>
            </div>
        </div>

        <div class="space-y-2 sm:space-y-3">
            <div class="flex flex-col space-y-1 sm:space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex space-x-1 sm:space-x-2">
                    @if ($playlist->user->userProfile->avatar_url)
                        <img src="{{ asset('storage/' . $playlist->user->userProfile->avatar_url) }}"
                            alt="{{ $playlist->user->userProfile->channel_name }}"
                            class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-lg font-medium">
                                {{ strtoupper(substr($playlist->user->userProfile->channel_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <h3
                            class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors duration-200">
                            <span class="block truncate">{{ $playlist->title }}</span>
                        </h3>
                        <a href="{{ route('channel.show', $playlist->user->userProfile?->channel_name) }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors font-medium text-xs sm:text-sm">
                            {{ $playlist->user->userProfile?->channel_name ?: $playlist->user->name }}
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                    <span>{{ $playlist->created_at->diffForHumans() }}</span>
                </div>
                @if ($playlist->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ $playlist->description }}
                    </p>
                @endif
            </div>
        </div>
    </a>
</div>

<script>
    // Colored hover effect for playlists
    function initializeColoredHoverPlaylist() {
        document.querySelectorAll("[data-thumbnail-playlist]").forEach(card => {
            const wrapper = card.closest("[data-color-wrapper-playlist]");
            const img = card.querySelector("img");

            if (!img || !wrapper) return;

            img.addEventListener("load", () => {
                const color = getDominantColorPlaylist(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            });

            // If image is already loaded
            if (img.complete && img.naturalHeight !== 0) {
                const color = getDominantColorPlaylist(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            }
        });

        function getDominantColorPlaylist(image) {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            canvas.width = 50;
            canvas.height = 50;
            ctx.drawImage(image, 0, 0, 50, 50);

            const data = ctx.getImageData(0, 0, 50, 50).data;

            let r = 0,
                g = 0,
                b = 0,
                count = 0;
            for (let i = 0; i < data.length; i += 4) {
                r += data[i];
                g += data[i + 1];
                b += data[i + 2];
                count++;
            }

            return {
                r: r / count,
                g: g / count,
                b: b / count
            };
        }
    }

    // Initialize colored hover effect when DOM is ready
    document.addEventListener("DOMContentLoaded", initializeColoredHoverPlaylist);
</script>
