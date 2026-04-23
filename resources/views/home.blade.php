<x-layout>
    {!! \App\Helpers\AdvertisementHelper::generateClickTrackingScript() !!}

    <style>
        [data-color-card] a {
            background-color: transparent;
            transition: background-color 220ms ease;
        }

        [data-color-card]:hover a {
            background-color: var(--hover-color, rgba(120, 120, 120, 0.14));
        }

        [data-reels-carousel] {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [data-reels-carousel]::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="w-full px-3 py-4 sm:px-5 lg:px-7">
        <div class="mx-auto w-full max-w-[1650px]">
            <div class="mb-5 flex gap-2 overflow-x-auto pb-1" id="homeFilters">
                @php
                    $chips = [
                        ['label' => 'All', 'value' => 'all'],
                        ['label' => 'Videos', 'value' => 'videos'],
                        ['label' => 'Reels', 'value' => 'reels'],
                        ['label' => 'Music', 'value' => 'music'],
                        ['label' => 'Gaming', 'value' => 'gaming'],
                        ['label' => 'Live', 'value' => 'live'],
                    ];
                @endphp
                @foreach ($chips as $chip)
                    <button
                        data-filter="{{ $chip['value'] }}"
                        class="filter-chip shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition {{ $category === $chip['value'] ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                        {{ $chip['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="mb-6">
                <x-advertisements position="home_video" />
            </div>

            <section>
                <div id="feedItems" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-3">
                    @include('partials.home-feed-items', ['feedItems' => $feedItems])
                </div>

                <div id="loadingSpinner" class="hidden py-8 text-center">
                    <div class="inline-block h-7 w-7 animate-spin rounded-full border-2 border-gray-300 border-t-gray-900 dark:border-gray-700 dark:border-t-white"></div>
                </div>

                <div id="endMessage" class="hidden py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Nessun altro contenuto da caricare.
                </div>
            </section>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isLoading = false;
        let hasMore = @json($hasMore);
        let currentCategory = @json($category);

        function setActiveFilter(category) {
            document.querySelectorAll('.filter-chip').forEach((btn) => {
                const isActive = btn.dataset.filter === category;
                btn.className = isActive ?
                    'filter-chip shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition bg-gray-900 text-white dark:bg-white dark:text-gray-900' :
                    'filter-chip shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700';
            });
        }

        function showLoading(show) {
            const spinner = document.getElementById('loadingSpinner');
            if (!spinner) return;
            spinner.classList.toggle('hidden', !show);
        }

        function toggleEndMessage(show) {
            const end = document.getElementById('endMessage');
            if (!end) return;
            end.classList.toggle('hidden', !show);
        }

        function getDominantColor(img) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            if (!ctx) return null;

            canvas.width = 36;
            canvas.height = 36;
            ctx.drawImage(img, 0, 0, 36, 36);
            const data = ctx.getImageData(0, 0, 36, 36).data;

            let r = 0;
            let g = 0;
            let b = 0;
            let count = 0;

            for (let i = 0; i < data.length; i += 4) {
                r += data[i];
                g += data[i + 1];
                b += data[i + 2];
                count++;
            }

            if (!count) return null;
            return {
                r: Math.round(r / count),
                g: Math.round(g / count),
                b: Math.round(b / count),
            };
        }

        function applyHoverColors(root = document) {
            root.querySelectorAll('[data-color-card]').forEach((card) => {
                const img = card.querySelector('[data-color-thumb]');
                if (!img) return;

                const setColor = () => {
                    const color = getDominantColor(img);
                    if (!color) return;
                    card.style.setProperty('--hover-color', `rgba(${color.r}, ${color.g}, ${color.b}, 0.16)`);
                };

                if (img.complete && img.naturalWidth > 0) {
                    setColor();
                } else {
                    img.addEventListener('load', setColor, {
                        once: true
                    });
                }
            });
        }

        function bindHoverPreviews(root = document) {
            root.querySelectorAll('[data-preview-card]').forEach((card) => {
                if (card.dataset.previewBound === '1') return;
                card.dataset.previewBound = '1';

                const video = card.querySelector('[data-preview-video]');
                const image = card.querySelector('[data-preview-image]');
                if (!video) return;

                const start = () => {
                    video.currentTime = 0;
                    const playPromise = video.play();
                    if (playPromise && typeof playPromise.then === 'function') {
                        playPromise.then(() => {
                            video.classList.remove('opacity-0');
                            video.classList.add('opacity-100');
                            if (image) image.classList.add('opacity-0');
                        }).catch(() => {});
                    }
                };

                const stop = () => {
                    video.pause();
                    video.classList.remove('opacity-100');
                    video.classList.add('opacity-0');
                    if (image) image.classList.remove('opacity-0');
                };

                card.addEventListener('mouseenter', start);
                card.addEventListener('mouseleave', stop);
            });
        }

        async function applyFilter(category) {
            if (isLoading) return;
            isLoading = true;
            showLoading(true);
            toggleEndMessage(false);

            try {
                const response = await fetch(`/home/filter?category=${encodeURIComponent(category)}&sort=latest`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (!data.success) return;

                const container = document.getElementById('feedItems');
                container.innerHTML = data.items_html || '';

                currentCategory = category;
                currentPage = 1;
                hasMore = !!data.has_more;
                setActiveFilter(category);
                applyHoverColors(container);
                bindHoverPreviews(container);
                bindReelCarousels(container);
                toggleEndMessage(!hasMore);
            } catch (error) {
                console.error('Filter error:', error);
            } finally {
                isLoading = false;
                showLoading(false);
            }
        }

        async function loadMore() {
            if (isLoading || !hasMore) return;

            isLoading = true;
            showLoading(true);

            try {
                const nextPage = currentPage + 1;
                const response = await fetch(`/home/load-more?page=${nextPage}&category=${encodeURIComponent(currentCategory)}&sort=latest`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (!data.success) return;

                const container = document.getElementById('feedItems');
                if (data.items_html && data.items_html.trim() !== '') {
                    container.insertAdjacentHTML('beforeend', data.items_html);
                    applyHoverColors(container);
                    bindHoverPreviews(container);
                    bindReelCarousels(container);
                }

                currentPage = nextPage;
                hasMore = !!data.has_more;
                toggleEndMessage(!hasMore);
            } catch (error) {
                console.error('Load more error:', error);
            } finally {
                isLoading = false;
                showLoading(false);
            }
        }

        function onScroll() {
            if (isLoading || !hasMore) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const viewport = window.innerHeight;
            const full = document.documentElement.scrollHeight;

            if (scrollTop + viewport >= full - 700) {
                loadMore();
            }
        }

        function bindReelCarousels(root = document) {
            root.querySelectorAll('[data-carousel-target]').forEach((button) => {
                if (button.dataset.bound === '1') return;
                button.dataset.bound = '1';

                button.addEventListener('click', () => {
                    const targetId = button.getAttribute('data-carousel-target');
                    const direction = button.getAttribute('data-carousel-dir');
                    const container = document.getElementById(targetId);
                    if (!container) return;

                    const amount = Math.round(container.clientWidth * 0.92);
                    container.scrollBy({
                        left: direction === 'next' ? amount : -amount,
                        behavior: 'smooth'
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyHoverColors(document);
            bindHoverPreviews(document);
            bindReelCarousels(document);
            setActiveFilter(currentCategory);
            window.addEventListener('scroll', onScroll);

            document.querySelectorAll('.filter-chip').forEach((btn) => {
                btn.addEventListener('click', () => applyFilter(btn.dataset.filter));
            });
        });
    </script>
</x-layout>

