<x-layout>
    <div id="pageContent" class="min-h-screen bg-gray-950 transition-all duration-500">
        <div class="w-full mx-5 mt-6">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                    <!-- Main Content Column -->
                    <div class="xl:col-span-8 space-y-6">
                        <!-- Video Player Section -->
                        <div id="videoWrapper" class="relative bg-black rounded-2xl overflow-hidden shadow-2xl">
                            <!-- Ambient Glow Canvas -->
                            <canvas id="ambientCanvas" class="absolute -inset-10 -z-10 pointer-events-none"
                                style="filter: blur(180px) saturate(3) brightness(1.5); transform: scale(1.2); opacity:0; transition: opacity 0.5s;"></canvas>

                            <!-- Processing Video Player -->
                            <x-video-player :video="$video" class="relative z-10" />
                        </div>

                        <!-- Video Title & Actions -->
                        <div class="space-y-4">
                            <h1 class="text-2xl lg:text-3xl font-bold text-white leading-tight tracking-tight">
                                {{ $video->title }}
                            </h1>

                            <!-- Video Stats & Actions Row -->
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Video Stats -->
                                <div class="flex items-center gap-4 text-sm text-gray-400">
                                    <span class="font-medium text-gray-300">
                                        {{ number_format($video->views_count) }} visualizzazioni
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                                        </svg>
                                        {{ $video->created_at->format('d M Y') }}
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <button
                                        onclick="navigator.clipboard.writeText('{{ route('videos.show', $video) }}').then(()=>{ const toast=document.getElementById('copyToast'); toast.classList.remove('hidden'); setTimeout(()=>toast.classList.add('hidden'),2000); })"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/80 hover:bg-gray-700/80 rounded-full transition-all duration-200 backdrop-blur-sm border border-gray-700/50 hover:border-gray-600 group cursor-pointer">
                                        <i class="fas fa-share"></i>
                                        <span class="text-gray-300 text-sm font-medium">Condividi</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Channel Info Card -->
                            <div class="bg-gray-900/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 p-6">
                                <div class="flex items-start gap-4">
                                    <!-- Avatar -->
                                    @if ($video->user->userProfile?->avatar_url)
                                        <img src="{{ $video->user->userProfile->avatar_url }}"
                                            alt="{{ $video->user->name }}"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-gray-700">
                                    @else
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center border-2 border-gray-700">
                                            <span
                                                class="text-white font-bold text-lg">{{ strtoupper(substr($video->user->name, 0, 1)) }}</span>
                                        </div>
                                    @endif

                                    <!-- Channel Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-bold text-white">
                                                    <a href="{{ route('channel.show', $video->user) }}"
                                                        class="hover:text-red-400 transition-colors duration-200">
                                                        {{ $video->user->userProfile?->channel_name ?: $video->user->name }}
                                                    </a>
                                                </h3>
                                                <p class="text-gray-400 text-sm font-medium">
                                                    {{ number_format($video->user->subscribers()->count()) }}
                                                    iscritti
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        @if ($video->description)
                                            <div class="mt-4">
                                                <div id="description"
                                                    class="text-gray-300 text-sm leading-relaxed whitespace-pre-line overflow-hidden"
                                                    style="max-height: 6rem;">
                                                    {{ $video->description }}
                                                </div>
                                                <button id="showMoreBtn"
                                                    class="text-blue-400 hover:text-blue-300 text-sm font-medium mt-2 transition-colors duration-200">
                                                    Mostra di più
                                                </button>
                                            </div>
                                        @endif

                                        <!-- Tags -->
                                        @if ($video->tags && count($video->tags) > 0)
                                            <div class="flex flex-wrap gap-2 mt-4">
                                                @foreach ($video->tags as $tag)
                                                    <span
                                                        class="px-3 py-1.5 bg-gray-800/80 hover:bg-gray-700/80 rounded-full text-gray-300 text-sm font-medium transition-all duration-200 cursor-pointer border border-gray-700/50 hover:border-gray-600">
                                                        #{{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Processing/Rejection Notice -->
                            @if ($video->status === 'rejected')
                                <div class="bg-red-900/30 backdrop-blur-sm rounded-2xl border border-red-700/50 p-6">
                                    <div class="flex items-start gap-4">
                                        <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                            <path
                                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"
                                                fill="currentColor" />
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="text-red-400 font-semibold text-lg mb-2">Video Rifiutato</h4>
                                            <p class="text-red-300/80 text-sm leading-relaxed mb-3">
                                                Questo video è stato rifiutato durante il processo di elaborazione.
                                                @if ($video->moderation_reason)
                                                    <strong>Motivo:</strong> {{ $video->moderation_reason }}
                                                @endif
                                            </p>
                                            <p class="text-red-300/60 text-xs leading-relaxed">
                                                Puoi tentare di riprocessare il video per risolvere i problemi
                                                identificati.
                                            </p>
                                            <div class="mt-4 flex items-center gap-2">
                                                <button
                                                    onclick="if(confirm('Sei sicuro di voler riprocessare questo video? Il processo di elaborazione verrà riavviato.')) { document.getElementById('reprocess-form').submit(); }"
                                                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors text-sm font-medium">
                                                    <i class="fas fa-redo mr-2"></i>Riprocessa Video
                                                </button>
                                                <form id="reprocess-form"
                                                    action="{{ route('videos.reprocess', $video) }}" method="POST"
                                                    class="hidden">
                                                    @csrf
                                                </form>
                                            </div>
                                            <div class="mt-4 flex items-center gap-2 text-xs text-red-400">
                                                <span class="px-2 py-1 bg-red-500/20 rounded-full">
                                                    Stato: Rifiutato
                                                </span>
                                                <span class="px-2 py-1 bg-blue-500/20 rounded-full">
                                                    ID: #{{ $video->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="bg-yellow-900/30 backdrop-blur-sm rounded-2xl border border-yellow-700/50 p-6">
                                    <div class="flex items-start gap-4">
                                        <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-yellow-400 font-semibold text-lg mb-2">Video in Elaborazione
                                            </h4>
                                            <p class="text-yellow-300/80 text-sm leading-relaxed">
                                                Questo video è attualmente in fase di elaborazione. Sarà disponibile
                                                pubblicamente una volta completato il processo di transcoding e
                                                moderazione.
                                                @auth
                                                    @if (Auth::id() === $video->user_id)
                                                        Riceverai una notifica quando sarà pronto.
                                                    @endif
                                                @endauth
                                            </p>
                                            <div class="mt-4 flex items-center gap-2 text-xs text-yellow-400">
                                                <span class="px-2 py-1 bg-yellow-500/20 rounded-full">
                                                    Stato: {{ ucfirst($video->status) }}
                                                </span>
                                                <span class="px-2 py-1 bg-blue-500/20 rounded-full">
                                                    ID: #{{ $video->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="xl:col-span-4 space-y-6">
                        <div class="relative top-6">
                            <div class="bg-gray-900/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 p-6">
                                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                    Video correlati
                                </h3>
                                <div class="space-y-4">
                                    @php
                                        $relatedVideos = \App\Models\Video::published()
                                            ->where('id', '!=', $video->id)
                                            ->limit(6)
                                            ->get();
                                    @endphp
                                    @foreach ($relatedVideos as $relatedVideo)
                                        <a href="{{ route('videos.show', $relatedVideo) }}" class="group block">
                                            <div class="flex gap-3">
                                                <!-- Thumbnail -->
                                                <div
                                                    class="flex-shrink-0 relative w-40 h-24 bg-gray-800 rounded-xl overflow-hidden border border-gray-700/50">
                                                    @if ($relatedVideo->thumbnail_path)
                                                        <img src="{{ asset('storage/' . $relatedVideo->thumbnail_path) }}"
                                                            alt="{{ $relatedVideo->title }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    @endif
                                                    <div
                                                        class="absolute bottom-2 right-2 bg-black/90 text-white text-xs px-2 py-1 rounded-md font-semibold backdrop-blur-sm">
                                                        {{ $relatedVideo->formatted_duration }}
                                                    </div>
                                                </div>

                                                <!-- Video Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h4
                                                        class="text-sm font-semibold text-white line-clamp-2 group-hover:text-red-400 transition-colors duration-200 leading-tight mb-2">
                                                        {{ $relatedVideo->title }}
                                                    </h4>
                                                    <p
                                                        class="text-xs text-gray-400 mb-1 hover:text-gray-300 transition-colors font-medium">
                                                        {{ $relatedVideo->user->userProfile?->channel_name ?: $relatedVideo->user->name }}
                                                    </p>
                                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                                        <span>{{ number_format($relatedVideo->views_count) }}
                                                            visualizzazioni</span>
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

            <!-- Toast Notification -->
            <div id="copyToast"
                class="hidden fixed bottom-6 right-6 bg-gray-900/95 backdrop-blur-xl border border-gray-700/50 text-white px-6 py-4 rounded-xl shadow-2xl z-50 transition-all duration-300">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                    <span class="font-medium">Link copiato negli appunti!</span>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Refresh automatico ogni 30 secondi per video in elaborazione
            setInterval(function() {
                fetch(window.location.href, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Cache-Control': 'no-cache'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Se il contenuto è cambiato, ricarica la pagina
                        if (html.includes('video-player-status') && !html.includes(
                                'Elaborazione video in corso')) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.log('Refresh check failed:', error));
            }, 30000);
        });
    </script>
</x-layout>
