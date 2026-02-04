<div class="group cursor-pointer relative p-2 rounded-xl w-full max-w-sm sm:max-w-md" data-color-wrapper-trending>
    <a href="{{ route('videos.show', $video) }}" class="video-link" data-video-id="{{ $video->id }}"
        data-video-title="{{ $video->title }}">
        <div class="relative overflow-hidden w-full rounded-xl bg-gray-100 dark:bg-gray-800 aspect-[9/16] mb-3 transition-all duration-300"
            data-thumbnail-trending style="--hover-bg: rgba(255, 0, 0, 0.35);">
            @if ($video->thumbnail_url)
                <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div
                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
                </div>
            @endif

            <!-- Duration Badge -->
            <div
                class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2.5 py-1.5 rounded-lg font-medium border border-white/10">
                {{ $video->formatted_duration ?? gmdate('i:s', $video->duration) }}
            </div>

            <!-- Hover Play Button -->
            <div
                class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                <div
                    class="w-16 h-16 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 border border-white/30 shadow-2xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </div>
            </div>

            <!-- Professional Action Buttons -->
            @auth
                <div
                    class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                    <!-- Watch Later Button -->
                    <livewire:video-watch-later :video="$video" :compact="true" />

                    <!-- Add to Playlist Button -->
                    <button
                        class="w-9 h-9 bg-gray-900/70 text-white rounded-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl cursor-pointer"
                        title="Aggiungi a Playlist"
                        onclick="event.preventDefault(); showAddToPlaylistModal({{ $video->id }})">
                        <i class="fas fa-folder-plus text-sm"></i>
                    </button>
                </div>
            @endauth
        </div>

        <div class="space-y-3">
            <div class="flex flex-col space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex space-x-2">
                    @if ($video->user->userProfile?->avatar_url)
                        <img src="{{ asset('storage/' . $video->user->userProfile->avatar_url) }}"
                            class="w-10 h-10 rounded-full object-cover"
                            alt="{{ $video->user->userProfile?->channel_name }}">
                    @endif

                    <div class="flex flex-col flex-1 min-w-0">
                        <h3
                            class="font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors duration-200 leading-tight">
                            {{ $video->title }}
                        </h3>
                        <a href="{{ route('channel.show', $video->user->userProfile?->channel_name) }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors font-medium text-sm truncate">
                            {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-eye"></i>
                        <span>{{ number_format($video->views_count) }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $video->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </a>
</div>

<script>
    // Gestione click sui video per watch later automatico
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.video-link').forEach(function(videoLink) {
            videoLink.addEventListener('click', function(e) {
                const videoId = this.getAttribute('data-video-id');
                const videoTitle = this.getAttribute('data-video-title');
                
                if (!window.Laravel || !window.Laravel.user) {
                    return;
                }

                checkAndHandleWatchLater(videoId, videoTitle, this, e);
            });
        });
    });

    /**
     * Verifica lo stato watch later e gestisce la rimozione automatica
     */
    function checkAndHandleWatchLater(videoId, videoTitle, linkElement, clickEvent) {
        fetch(`/api/watch-later/check?video_id=${videoId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_in_watch_later) {
                clickEvent.preventDefault();
                
                removeFromWatchLaterAutomatic(videoId, videoTitle, linkElement);
            }
        })
        .catch(error => {
            console.error('Errore nel controllo watch later:', error);
        });
    }

    /**
     * Rimuove automaticamente il video dai watch later
     */
    function removeFromWatchLaterAutomatic(videoId, videoTitle, linkElement) {
        fetch(`/api/watch-later/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                video_id: videoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateAllWatchLaterComponents(videoId, false);
                
                showToast(`"${videoTitle}" rimosso da Guarda più tardi`, 'info');
                
                setTimeout(() => {
                    window.location.href = linkElement.href;
                }, 1500);
            } else {
                showToast('Errore nel rimuovere il video da Guarda più tardi', 'warning');
                window.location.href = linkElement.href;
            }
        })
        .catch(error => {
            console.error('Errore nella rimozione automatica:', error);
            showToast('Errore nella rimozione automatica', 'error');
            window.location.href = linkElement.href;
        });
    }

    /**
     * Aggiorna tutti i componenti VideoWatchLater per un video specifico
     */
    function updateAllWatchLaterComponents(videoId, isInWatchLater) {
        // Dispara evento Livewire per aggiornare tutti i componenti
        if (window.Livewire) {
            Livewire.dispatch('watchLaterStatusChanged', {
                videoId: parseInt(videoId),
                isInWatchLater: isInWatchLater
            });
        }
    }

    // Colored hover effect for trending videos
    function initializeColoredHoverTrending() {
        document.querySelectorAll("[data-thumbnail-trending]").forEach(card => {
            const wrapper = card.closest("[data-color-wrapper-trending]");
            const img = card.querySelector("img");

            if (!img || !wrapper) return;

            img.addEventListener("load", () => {
                const color = getDominantColorTrending(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            });

            // If image is already loaded
            if (img.complete && img.naturalHeight !== 0) {
                const color = getDominantColorTrending(img);
                const rgb =
                    `rgba(${Math.round(color.r)}, ${Math.round(color.g)}, ${Math.round(color.b)}, 0.35)`;
                wrapper.style.setProperty("--hover-bg", rgb);
            }
        });

        function getDominantColorTrending(image) {
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
    document.addEventListener("DOMContentLoaded", initializeColoredHoverTrending);
</script>

<style>
    [data-color-wrapper-trending] {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    [data-color-wrapper-trending]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--hover-bg, rgba(239, 68, 68, 0.15));
        opacity: 0;
        transition: opacity 0.4s ease;
        border-radius: inherit;
        z-index: 1;
    }

    [data-color-wrapper-trending]:hover::before {
        opacity: 1;
    }

    [data-color-wrapper-trending]:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    [data-color-wrapper-trending] > * {
        position: relative;
        z-index: 2;
    }

    [data-thumbnail-trending] {
        transition: all 0.4s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    [data-color-wrapper-trending]:hover [data-thumbnail-trending] {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: scale(1.05);
    }
</style>
