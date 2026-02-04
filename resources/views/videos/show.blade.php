<x-layout>
    {!! \App\Helpers\AdvertisementHelper::generateClickTrackingScript() !!}
    <div id="pageContent" class="min-h-screen transition-all duration-500">
        <!-- MiniPlayer Component -->
        <livewire:mini-player />
        <div class="w-full">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div id="mainContainer">
                    <div id="normalLayout" class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                        <div class="xl:col-span-8 space-y-4">
                            <!-- Video Player -->
                            <div id="videoWrapper" class="relative rounded-xl">
                                @php $firstRelatedVideo = $relatedVideos->first() @endphp
                                <x-video-player-with-ads :video="$video" :next-video="$nextVideo ?? $firstRelatedVideo"
                                    class="w-full aspect-video" />

                                <!-- Video Overlay Advertisement -->
                                <div class="absolute inset-0 pointer-events-none">
                                    <div class="relative w-full h-full">
                                        <x-advertisements position="video_overlay" />
                                    </div>
                                </div>
                            </div>

                            <!-- Video Metadata -->
                            <div class="space-y-4 px-1">
                                <!-- Video Title -->
                                <h1 class="text-xl font-medium text-white leading-7">
                                    {{ $video->title }}
                                </h1>

                                <!-- Video Stats & Actions -->
                                <div
                                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pt-2 border-b border-gray-800 pb-4">
                                    <!-- Video Stats -->
                                    <div class="flex items-center gap-4 text-sm text-gray-400">
                                        <span class="font-medium text-gray-300">
                                            {{ number_format($video->views_count) }} visualizzazioni
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            {{ $video->created_at->format('d M Y') }}
                                        </span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2">
                                        <livewire:video-like-dislike :video="$video" />

                                        <livewire:video-watch-later :video="$video" />

                                        <button
                                            onclick="navigator.clipboard.writeText('{{ route('videos.show', $video) }}').then(()=>{ showToast('Link copiato negli appunti!') })"
                                            class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500 backdrop-blur-sm cursor-pointer">
                                            <i class="fas fa-share text-gray-300 group-hover:text-white"></i>
                                            <span
                                                class="text-gray-300 text-sm font-medium group-hover:text-white">Condividi</span>
                                        </button>

                                        <a href="{{ route('videos.download', ['video' => $video->video_url]) }}"
                                            download="{{ $video->video_path }}"
                                            class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500 backdrop-blur-sm cursor-pointer">
                                            <i class="fas fa-download text-gray-300 group-hover:text-white"></i>
                                            <span
                                                class="text-gray-300 text-sm font-medium group-hover:text-white">Scarica</span>
                                        </a>

                                        <button
                                            onclick="openReportModal('video', {{ $video->id }}, '{{ addslashes($video->title) }}')"
                                            class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500 backdrop-blur-sm cursor-pointer">
                                            <i class="fas fa-flag text-gray-300 group-hover:text-red-400"></i>
                                            <span
                                                class="text-gray-300 text-sm font-medium group-hover:text-red-400">Segnala</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Channel Info -->
                                <div class="flex items-start justify-between py-4 border-b border-gray-800">
                                    <div class="flex items-start gap-3 flex-1">
                                        <!-- Avatar -->
                                        <a href="{{ route('channel.show', $video->user->userProfile?->channel_name) }}"
                                            class="flex-shrink-0">
                                            @if ($video->user->userProfile?->avatar_url)
                                                <img src="{{ asset('storage/' . $video->user->userProfile->avatar_url) }}"
                                                    alt="{{ $video->user->name }}"
                                                    class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                                    <span
                                                        class="text-white font-bold text-sm">{{ strtoupper(substr($video->user->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </a>

                                        <!-- Channel Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-sm font-semibold text-white">
                                                    <a href="{{ route('channel.show', $video->user->userProfile?->username) }}"
                                                        class="hover:text-white transition-colors duration-200">
                                                        {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                                                    </a>
                                                </h3>
                                                <p class="text-gray-400 text-xs">
                                                    {{ number_format($video->user->subscribers()->count()) }} iscritti
                                                </p>
                                            </div>

                                            <!-- Description -->
                                            @if ($video->description)
                                                <div class="mt-2">
                                                    <div id="description"
                                                        class="text-gray-300 text-sm leading-relaxed whitespace-pre-line overflow-hidden"
                                                        style="max-height: 3rem;">
                                                        {{ $video->description }}
                                                    </div>
                                                    <button id="showMoreBtn"
                                                        class="text-gray-400 hover:text-gray-300 text-sm font-medium mt-1 transition-colors duration-200">
                                                        Mostra di più
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Subscribe Button -->
                                    <div class="flex-shrink-0 ml-4">
                                        <livewire:video-subscribe :video="$video" />
                                    </div>
                                </div>

                                <!-- Tags -->
                                @if ($video->tags && count($video->tags) > 0)
                                    <div class="flex flex-wrap gap-2 py-4 border-b border-gray-800">
                                        @foreach ($video->tags as $tag)
                                            <a href="#"
                                                class="px-3 py-1 bg-gray-800 hover:bg-gray-700 rounded-full text-blue-400 text-sm font-medium transition-all duration-200 cursor-pointer">
                                                #{{ $tag }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Comments Section -->
                                <div class="pt-4">
                                    <livewire:video-comments :video="$video" />
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar - Related Videos -->
                        <div class="xl:col-span-4 space-y-3">
                            <div class="space-y-3">
                                @foreach ($relatedVideos as $index => $relatedVideo)
                                    @if ($index === 3 && $index < count($relatedVideos))
                                        <!-- Advertisement between videos -->
                                        <div class="mb-6">
                                            <x-advertisements position="between_videos" />
                                        </div>
                                    @endif

                                    <a href="{{ route('videos.show', $relatedVideo) }}" class="group block">
                                        <div class="flex gap-3">
                                            <!-- Thumbnail -->
                                            <div
                                                class="flex-shrink-0 relative w-40 h-24 bg-gray-900 rounded-xl overflow-hidden">
                                                @if ($relatedVideo->thumbnail_path)
                                                    <img src="{{ asset('storage/' . $relatedVideo->thumbnail_path) }}"
                                                        alt="{{ $relatedVideo->title }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @endif
                                                <div
                                                    class="absolute bottom-1 right-1 bg-black/90 text-white text-xs px-1.5 py-0.5 rounded font-semibold">
                                                    {{ $relatedVideo->formatted_duration }}
                                                </div>
                                            </div>

                                            <!-- Video Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-sm font-medium text-white line-clamp-2 group-hover:text-white/90 transition-colors duration-200 leading-tight mb-1">
                                                    {{ $relatedVideo->title }}
                                                </h4>
                                                <p
                                                    class="text-xs text-gray-400 mb-1 hover:text-gray-300 transition-colors">
                                                    {{ $relatedVideo->user->userProfile?->channel_name ?: $relatedVideo->user->name }}
                                                </p>
                                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                                    <span>{{ number_format($relatedVideo->views_count) }}
                                                        visualizzazioni</span>
                                                    <span>•</span>
                                                    <span>{{ $relatedVideo->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div id="cinemaLayout" class="hidden flex-col">
                        <div id="cinemaVideoContainer" class="w-screen bg-black rounded-none"
                            style="margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">
                        </div>

                        <div class="max-w-7xl mx-auto w-full px-4 mt-6">
                            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                                <div class="xl:col-span-8 space-y-4">
                                    <div class="space-y-4 px-1">
                                        <h1 class="text-xl font-medium text-white leading-7">
                                            {{ $video->title }}
                                        </h1>

                                        <div
                                            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pt-2 border-b border-gray-800 pb-4">
                                            <div class="flex items-center gap-4 text-sm text-gray-400">
                                                <span class="font-medium text-gray-300">
                                                    {{ number_format($video->views_count) }} visualizzazioni
                                                </span>
                                                <span>•</span>
                                                <span class="flex items-center gap-1">
                                                    {{ $video->created_at->format('d M Y') }}
                                                </span>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex items-center gap-2">
                                                <livewire:video-like-dislike :video="$video" />

                                                <livewire:video-watch-later :video="$video" />

                                                @if (auth()->check() && auth()->id() === $video->user_id)
                                                    <a href="{{ route('channel.edit', $video->user->userProfile?->channel_name) }}?tab=analytics"
                                                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-full transition-all duration-200 group">
                                                        <i class="fas fa-chart-line text-white"></i>
                                                        <span class="text-white text-sm font-medium">Analytics</span>
                                                    </a>
                                                @endif

                                                <button
                                                    onclick="navigator.clipboard.writeText('{{ route('videos.show', $video) }}').then(()=>{ showToast('Link copiato negli appunti!') })"
                                                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/10 rounded-full transition-all duration-200 group cursor-pointer">
                                                    <i class="fas fa-share text-gray-300 group-hover:text-white"></i>
                                                    <span
                                                        class="text-gray-300 text-sm font-medium group-hover:text-white">Condividi</span>
                                                </button>

                                                <a href="{{ route('videos.download', ['video' => $video->video_url]) }}"
                                                    download="{{ $video->video_path }}"
                                                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/10 rounded-full transition-all duration-200 group cursor-pointer">
                                                    <i class="fas fa-download text-gray-300 group-hover:text-white"></i>
                                                    <span
                                                        class="text-gray-300 text-sm font-medium group-hover:text-white">Scarica</span>
                                                </a>

                                                <button
                                                    onclick="openReportModal('video', {{ $video->id }}, '{{ addslashes($video->title) }}')"
                                                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/10 rounded-full transition-all duration-200 group cursor-pointer">
                                                    <i class="fas fa-flag text-gray-300 group-hover:text-red-400"></i>
                                                    <span
                                                        class="text-gray-300 text-sm font-medium group-hover:text-red-400">Segnala</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Channel Info -->
                                        <div class="flex items-start justify-between py-4 border-b border-gray-800">
                                            <div class="flex items-start gap-3 flex-1">
                                                <a href="{{ route('channel.show', $video->user) }}"
                                                    class="flex-shrink-0">
                                                    @if ($video->user->userProfile?->avatar_url)
                                                        <img src="{{ asset('storage/' . $video->user->userProfile->avatar_url) }}"
                                                            alt="{{ $video->user->name }}"
                                                            class="w-10 h-10 rounded-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                                            <span
                                                                class="text-white font-bold text-sm">{{ strtoupper(substr($video->user->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </a>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <h3 class="text-sm font-semibold text-white">
                                                            <a href="{{ route('channel.show', $video->user) }}"
                                                                class="hover:text-white transition-colors duration-200">
                                                                {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                                                            </a>
                                                        </h3>
                                                        <p class="text-gray-400 text-xs">
                                                            {{ number_format($video->user->subscribers()->count()) }}
                                                            iscritti
                                                        </p>
                                                    </div>

                                                    @if ($video->description)
                                                        <div class="mt-2">
                                                            <div
                                                                class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                                                                {{ $video->description }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Subscribe Button -->
                                            <div class="flex-shrink-0 ml-4">
                                                <livewire:video-subscribe :video="$video" />
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        @if ($video->tags && count($video->tags) > 0)
                                            <div class="flex flex-wrap gap-2 py-4 border-b border-gray-800">
                                                @foreach ($video->tags as $tag)
                                                    <a href="#"
                                                        class="px-3 py-1 bg-gray-800 hover:bg-gray-700 rounded-full text-blue-400 text-sm font-medium transition-all duration-200 cursor-pointer">
                                                        #{{ $tag }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Comments Section -->
                                        <div class="pt-4">
                                            <livewire:video-comments :video="$video" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Sidebar - Related Videos -->
                                <div class="xl:col-span-4 space-y-3">
                                    <div class="space-y-3">
                                        @foreach ($relatedVideos as $index => $relatedVideo)
                                            @if ($index === 3 && $index < count($relatedVideos))
                                                <!-- Advertisement between videos -->
                                                <div class="mb-6">
                                                    <x-advertisements position="between_videos" />
                                                </div>
                                            @endif

                                            <a href="{{ route('videos.show', $relatedVideo) }}" class="group block">
                                                <div class="flex gap-3">
                                                    <div
                                                        class="flex-shrink-0 relative w-40 h-24 bg-gray-900 rounded-xl overflow-hidden">
                                                        @if ($relatedVideo->thumbnail_path)
                                                            <img src="{{ asset('storage/' . $relatedVideo->thumbnail_path) }}"
                                                                alt="{{ $relatedVideo->title }}"
                                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                        @endif
                                                        <div
                                                            class="absolute bottom-1 right-1 bg-black/90 text-white text-xs px-1.5 py-0.5 rounded font-semibold">
                                                            {{ $relatedVideo->formatted_duration }}
                                                        </div>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <h4
                                                            class="text-sm font-medium text-white line-clamp-2 group-hover:text-white/90 transition-colors duration-200 leading-tight mb-1">
                                                            {{ $relatedVideo->title }}
                                                        </h4>
                                                        <p
                                                            class="text-xs text-gray-400 mb-1 hover:text-gray-300 transition-colors">
                                                            {{ $relatedVideo->user->userProfile?->channel_name ?: $relatedVideo->user->name }}
                                                        </p>
                                                        <div class="flex items-center gap-1 text-xs text-gray-400">
                                                            <span>{{ number_format($relatedVideo->views_count) }}
                                                                visualizzazioni</span>
                                                            <span>•</span>
                                                            <span>{{ $relatedVideo->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast"
            class="hidden fixed bottom-4 right-4 bg-gray-900/95 backdrop-blur-xl border border-gray-700 text-white px-4 py-3 rounded-lg shadow-2xl z-50 transition-all duration-300">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
                <span class="text-sm font-medium" id="toast-message"></span>
            </div>
        </div>


    </div>

    <script>
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        function openReportModal(type, id, title) {
            // Check if user is authenticated
            @auth
                // Dispatch event to Livewire component
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('openReportModal', { type, id, title });
                } else {
                    // Fallback: dispatch custom event
                    window.dispatchEvent(new CustomEvent('open-report-modal', {
                        detail: { type, id, title }
                    }));
                }
            @else
                showToast('Devi essere autenticato per segnalare contenuti');
                window.location.href = '{{ route('login') }}';
            @endauth
        }

        document.addEventListener('DOMContentLoaded', function() {
            const normalLayout = document.getElementById('normalLayout');
            const cinemaLayout = document.getElementById('cinemaLayout');
            const videoWrapper = document.getElementById('videoWrapper');
            const cinemaVideoContainer = document.getElementById('cinemaVideoContainer');

            document.addEventListener('cinemaModeToggled', function(e) {

                if (e.detail.cinemaMode) {
                    cinemaVideoContainer.appendChild(videoWrapper);
                    videoWrapper.classList.remove('rounded-xl');
                    videoWrapper.classList.add('rounded-none');

                    normalLayout.classList.add('hidden');
                    cinemaLayout.classList.remove('hidden');
                    document.body.classList.add('cinema-mode');
                } else {
                    const originalContainer = document.querySelector('#normalLayout .xl\\:col-span-8');
                    if (originalContainer) {
                        originalContainer.insertBefore(videoWrapper, originalContainer.firstChild);
                    }
                    videoWrapper.classList.remove('rounded-none');
                    videoWrapper.classList.add('rounded-xl');

                    normalLayout.classList.remove('hidden');
                    cinemaLayout.classList.add('hidden');
                    document.body.classList.remove('cinema-mode');
                }
            });

            const description = document.getElementById('description');
            const showMoreBtn = document.getElementById('showMoreBtn');

            if (description && showMoreBtn) {
                const fullHeight = description.scrollHeight;
                if (fullHeight > 48) {
                    showMoreBtn.style.display = 'block';
                    showMoreBtn.addEventListener('click', function() {
                        if (description.style.maxHeight === '3rem') {
                            description.style.maxHeight = 'none';
                            showMoreBtn.textContent = 'Mostra meno';
                        } else {
                            description.style.maxHeight = '3rem';
                            showMoreBtn.textContent = 'Mostra di più';
                        }
                    });
                } else {
                    showMoreBtn.style.display = 'none';
                }
            }

            // Handle comment highlighting from URL hash
            function highlightComment() {
                const hash = window.location.hash;
                if (hash && hash.startsWith('#comment-')) {
                    const commentId = hash.substring(9); // Remove '#comment-'
                    const commentElement = document.getElementById('comment-' + commentId);
                    if (commentElement) {
                        // Scroll to the comment
                        commentElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Highlight the comment
                        commentElement.style.transition = 'background-color 0.3s ease, box-shadow 0.3s ease';
                        commentElement.style.backgroundColor = 'rgba(239, 68, 68, 0.2)'; // Light red background
                        commentElement.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.5)'; // Red border
                        commentElement.style.borderRadius = '8px';

                        // Remove highlight after 3 seconds
                        setTimeout(function() {
                            commentElement.style.backgroundColor = '';
                            commentElement.style.boxShadow = '';
                            commentElement.style.borderRadius = '';
                        }, 3000);

                        // Clear the hash from URL to avoid re-triggering
                        history.replaceState(null, null, window.location.pathname + window.location.search);
                    } else {
                        // If comment not found, try again in 500ms (for dynamic loading)
                        setTimeout(highlightComment, 500);
                    }
                }
            }

            // Run on page load
            highlightComment();

            // Also run when Livewire updates (for dynamic content)
            document.addEventListener('livewire:updated', highlightComment);

            // Semplificato: il miniplayer gestisce tutto automaticamente
            // Non serve logica aggiuntiva qui

        });
    </script>

    <!-- Report Modal Component -->
    <livewire:report-modal />
</x-layout>
