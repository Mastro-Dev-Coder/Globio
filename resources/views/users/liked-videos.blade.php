<x-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-thumbs-up mr-3 text-red-600"></i>Contenuti che mi piacciono
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.liked_videos_subtitle') }}
            </p>
        </div>

        <!-- Liked Videos Grid -->
        @if ($likedVideos->count() > 0)
            <div
                class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
                @foreach ($likedVideos as $like)
                    @if ($like->likeable)
                        <x-video :video="$like->likeable" />
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $likedVideos->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <i class="fas fa-thumbs-up text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('ui.no_liked_content') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.liked_content_empty') }}
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

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("[data-thumbnail]").forEach(card => {
            const wrapper = card.closest("[data-color-wrapper]");
            const img = card.querySelector("img");

            if (!img || !wrapper) return;

            img.addEventListener("load", () => {
                const color = getDominantColor(img);
                const rgb = `rgba(${color.r}, ${color.g}, ${color.b}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            });
        });

        function getDominantColor(image) {
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
    });
</script>
