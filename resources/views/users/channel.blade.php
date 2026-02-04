<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Channel Cover -->
        <div class="relative h-48 md:h-64 lg:h-80 overflow-hidden">
            @auth
                @if (auth()->id() === $UserProfile->user_id)
                    @php
                        $editUrl = route('channel.edit', [
                            'channel_name' => $UserProfile->channel_name,
                            'tab' => 'customization',
                        ]);
                    @endphp
                    <a href="{{ $editUrl }}" class="absolute inset-0 z-20"></a>
                @endif
            @endauth

            @if ($UserProfile && $UserProfile->banner_url)
                <img src="{{ Storage::url($UserProfile->banner_url) }}" alt="Cover"
                    class="w-full h-full object-cover rounded-xl">
            @else
                <div
                    class="w-full h-full bg-gradient-to-br from-red-500 via-red-600 to-red-700 rounded-xl overflow-hidden">
                </div>
            @endif

            <!-- Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent rounded-xl overflow-hidden">
            </div>

            <!-- Edit Banner Button (Only for channel owner) -->
            @auth
                @if (auth()->id() === $UserProfile->user_id)
                    <a href="{{ $editUrl }}"
                        class="absolute bottom-4 right-4 px-4 py-2 bg-black/70 hover:bg-black/80 text-white rounded-lg flex items-center gap-2 backdrop-blur-sm transition-all transform hover:scale-105 group z-30">
                        <i class="fas fa-edit text-sm"></i>
                        <span class="font-medium text-sm">Modifica</span>
                    </a>
                @endif
            @endauth
        </div>

        <div class="max-w-7xl mx-auto px-4 mt-12 sm:px-6 lg:px-8">
            <!-- Channel Header -->
            <div class="relative -mt-16 md:-mt-20 lg:-mt-24">
                <div class="flex flex-col lg:flex-row items-start lg:items-end gap-6 pb-6">

                    <!-- Avatar -->
                    <div class="flex-shrink-0 relative">
                        <div
                            class="w-24 h-24 md:w-32 md:h-32 lg:w-40 lg:h-40 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-2xl bg-gray-200 dark:bg-gray-700">
                            @if ($UserProfile && $UserProfile->avatar_url)
                                <img src="{{ Storage::url($UserProfile->avatar_url) }}"
                                    alt="{{ $UserProfile->user->name ?? 'Utente' }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                                    <span class="text-3xl md:text-5xl lg:text-6xl font-bold text-white">
                                        {{ strtoupper(substr($UserProfile->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Verification Badge -->
                        @if ($UserProfile && $UserProfile->is_verified)
                            <div
                                class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center border-4 border-white dark:border-gray-800">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        @endif

                        <!-- Camera Icon for Avatar (Only for channel owner) -->
                        @auth
                            @if (auth()->id() === $UserProfile->user_id)
                                @php
                                    $editUrl = route('channel.edit', [
                                        'channel_name' => $UserProfile->channel_name,
                                        'tab' => 'customization',
                                    ]);
                                @endphp
                                <a href="{{ $editUrl }}"
                                    class="absolute inset-0 flex items-center justify-center bg-black/70 opacity-0 hover:opacity-100 transition-all duration-300 rounded-full group">
                                    <div
                                        class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-all">
                                        <i class="fas fa-camera text-gray-700 text-lg"></i>
                                    </div>
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Channel Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1
                                        class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                                        {{ $UserProfile && $UserProfile->channel_name ? $UserProfile->channel_name : $UserProfile->user->name ?? 'Utente' }}
                                    </h1>
                                    @if ($UserProfile && $UserProfile->is_verified)
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                    @endif
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-video"></i>
                                        {{ $stats['videos_count'] }} video
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-users"></i>
                                        {{ number_format($stats['subscribers_count']) }} iscritti
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-eye"></i>
                                        {{ number_format($stats['total_views']) }} visualizzazioni
                                    </span>
                                </div>

                                @if ($UserProfile && $UserProfile->channel_description)
                                    <p class="text-gray-700 dark:text-gray-300 max-w-3xl">
                                        {{ Str::limit($UserProfile->channel_description, 200) }}
                                    </p>
                                @endif

                                <!-- Social Links -->
                                @if ($UserProfile && $UserProfile->social_links)
                                    <div class="flex items-center gap-3 mt-4">
                                        @foreach ($UserProfile->social_links as $platform => $url)
                                            @if ($url)
                                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                                    class="w-8 h-8 bg-gray-200 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-full flex items-center justify-center transition-colors">
                                                    @if ($platform == 'twitter')
                                                        <i class="fab fa-twitter text-blue-400"></i>
                                                    @elseif($platform == 'instagram')
                                                        <i class="fab fa-instagram text-pink-500"></i>
                                                    @elseif($platform == 'youtube')
                                                        <i class="fab fa-youtube text-red-600"></i>
                                                    @elseif($platform == 'tiktok')
                                                        <i class="fab fa-tiktok text-gray-900 dark:text-white"></i>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3">
                                @auth
                                    @if (auth()->id() === $UserProfile->user_id)
                                        @php
                                            $editUrl = route('channel.edit', [
                                                'channel_name' => $UserProfile->channel_name,
                                                'tab' => 'customization',
                                            ]);
                                        @endphp
                                        <a href="{{ $editUrl }}"
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors font-medium flex items-center gap-2 shadow-lg hover:shadow-xl">
                                            <i class="fas fa-edit"></i>
                                            Modifica canale
                                        </a>
                                    @else
                                        <button id="subscribeBtn"
                                            onclick="toggleSubscription('{{ $UserProfile->channel_name }}')"
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all font-medium flex items-center gap-2 shadow-lg hover:shadow-xl {{ $isSubscribed ? 'bg-gray-600 hover:bg-gray-700' : '' }}">
                                            <i class="fas fa-bell {{ $isSubscribed ? 'fa-solid' : 'fa-regular' }}"></i>
                                            <span id="subscribeText">{{ $isSubscribed ? 'Iscritto' : 'Iscriviti' }}</span>
                                        </button>

                                        @auth
                                            @if (auth()->id() !== $UserProfile->user_id)
                                                <button
                                                    onclick="openReportModal('channel', {{ $UserProfile->user_id }}, '{{ addslashes($UserProfile->channel_name ?? $UserProfile->user->name) }}')"
                                                    class="px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium flex items-center gap-2">
                                                    <i class="fas fa-flag"></i>
                                                    Segnala
                                                </button>
                                            @endif
                                        @endauth
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors font-medium flex items-center gap-2 shadow-lg">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Accedi per iscriverti
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
                <nav class="flex space-x-8">
                    <button onclick="switchTab('videos')" id="tab-videos"
                        class="tab-link py-4 px-1 border-b-2 border-red-600 text-red-600 font-medium text-sm whitespace-nowrap">
                        <i class="fas fa-video mr-2"></i>
                        Video
                    </button>
                    <button onclick="switchTab('playlists')" id="tab-playlists"
                        class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm whitespace-nowrap">
                        <i class="fas fa-list mr-2"></i>
                        Playlist
                    </button>
                    <button onclick="switchTab('about')" id="tab-about"
                        class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm whitespace-nowrap">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informazioni
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="pb-12">
                <!-- Videos Tab -->
                <div id="content-videos" class="tab-content">
                    @if ($videos->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($videos as $video)
                                <a href="{{ route('videos.show', $video) }}" class="group">
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-200 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-800">
                                        <!-- Thumbnail -->
                                        <div class="relative aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                            @if ($video->thumbnail_path)
                                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                    alt="{{ $video->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-4xl text-gray-400"></i>
                                                </div>
                                            @endif

                                            @if ($video->duration)
                                                <div
                                                    class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-lg font-medium">
                                                    {{ gmdate('i:s', $video->duration) }}
                                                </div>
                                            @endif

                                            <!-- Hover Overlay -->
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                                <div
                                                    class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-75 group-hover:scale-100">
                                                    <i class="fas fa-play text-red-600 ml-1"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Video Info -->
                                        <div class="p-4">
                                            <h3
                                                class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">
                                                {{ $video->title }}
                                            </h3>

                                            <div
                                                class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-eye"></i>
                                                    {{ number_format($video->views_count) }}
                                                </span>
                                                <span>
                                                    {{ $video->published_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $videos->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div
                                class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                                <i class="fas fa-video text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                Nessun video disponibile
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Questo canale non ha ancora caricato video
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Playlists Tab -->
                <div id="content-playlists" class="tab-content hidden">
                    @if ($playlists->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($playlists as $playlist)
                                <a href="{{ route('playlists.show', $playlist) }}" class="group">
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-800">
                                        <!-- Playlist Thumbnail -->
                                        <div
                                            class="relative aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                            @if ($playlist->dynamic_thumbnail_url)
                                                <img src="{{ $playlist->dynamic_thumbnail_url }}"
                                                    alt="{{ $playlist->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-list text-4xl text-gray-400"></i>
                                                </div>
                                            @endif

                                            <!-- Video Count Badge -->
                                            <div
                                                class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-lg font-medium">
                                                {{ $playlist->videos_count }} video
                                            </div>

                                            <!-- Play Overlay -->
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                                <div
                                                    class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-75 group-hover:scale-100">
                                                    <i class="fas fa-play text-red-600 ml-1"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Playlist Info -->
                                        <div class="p-4">
                                            <h3
                                                class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">
                                                {{ $playlist->title }}
                                            </h3>

                                            @if ($playlist->description)
                                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                    {{ Str::limit($playlist->description, 100) }}
                                                </p>
                                            @endif

                                            <div
                                                class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                <span>Playlist</span>
                                                <span>{{ $playlist->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div
                                class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                                <i class="fas fa-list text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                Nessuna playlist disponibile
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Questo canale non ha ancora creato playlist
                            </p>
                        </div>
                    @endif
                </div>

                <!-- About Tab -->
                <div id="content-about" class="tab-content hidden">
                    <div class="max-w-4xl">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Informazioni sul canale
                                </h2>
                            </div>

                            <div class="p-6 space-y-6">
                                <!-- Description -->
                                @if ($UserProfile && $UserProfile->channel_description)
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Descrizione
                                        </h3>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {{ $UserProfile->channel_description }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Stats -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Statistiche</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                            <div class="text-2xl font-bold text-red-600 mb-1">
                                                {{ $stats['videos_count'] }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Video</div>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                            <div class="text-2xl font-bold text-red-600 mb-1">
                                                {{ number_format($stats['subscribers_count']) }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Iscritti</div>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                            <div class="text-2xl font-bold text-red-600 mb-1">
                                                {{ number_format($stats['total_views']) }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Visualizzazioni</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Channel Details -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Dettagli del
                                        canale</h3>
                                    <div class="space-y-3">
                                        @if ($UserProfile && $UserProfile->country)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Paese</span>
                                                <span class="text-gray-900 dark:text-white font-medium">
                                                    @if ($UserProfile->country == 'IT')
                                                        🇮🇹 Italia
                                                    @elseif($UserProfile->country == 'US')
                                                        🇺🇸 Stati Uniti
                                                    @elseif($UserProfile->country == 'GB')
                                                        🇬🇧 Regno Unito
                                                    @elseif($UserProfile->country == 'FR')
                                                        🇫🇷 Francia
                                                    @elseif($UserProfile->country == 'DE')
                                                        🇩🇪 Germania
                                                    @elseif($UserProfile->country == 'ES')
                                                        🇪🇸 Spagna
                                                    @else
                                                        {{ $UserProfile->country }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif

                                        @if ($UserProfile && $UserProfile->channel_created_at)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Canale creato</span>
                                                <span class="text-gray-900 dark:text-white font-medium">
                                                    {{ \Carbon\Carbon::parse($UserProfile->channel_created_at)->format('d F Y') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab links
            document.querySelectorAll('.tab-link').forEach(link => {
                link.classList.remove('border-red-600', 'text-red-600');
                link.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                    'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to clicked tab link
            const activeLink = document.getElementById('tab-' + tabName);
            activeLink.classList.add('border-red-600', 'text-red-600');
            activeLink.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400',
                'dark:hover:text-gray-300');
        }

        // Subscription toggle
        function toggleSubscription(username) {
            const subscribeBtn = document.getElementById('subscribeBtn');
            const subscribeText = document.getElementById('subscribeText');

            // Add loading state
            subscribeBtn.disabled = true;
            subscribeText.textContent = 'Caricamento...';

            fetch(`/channel/${username}/subscribe`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const isSubscribed = data.is_subscribed;

                        if (isSubscribed) {
                            subscribeBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
                            subscribeBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                            subscribeText.textContent = 'Iscritto';
                            subscribeBtn.querySelector('i').className = 'fas fa-bell fa-solid';
                        } else {
                            subscribeBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                            subscribeBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                            subscribeText.textContent = 'Iscriviti';
                            subscribeBtn.querySelector('i').className = 'fas fa-bell fa-regular';
                        }
                    } else {
                        alert(data.message || 'Errore durante l\'operazione');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore durante l\'operazione');
                })
                .finally(() => {
                    subscribeBtn.disabled = false;
                });
        }

        // Initialize default tab
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('videos');
        });

        function openReportModal(type, id, title) {
            // Check if user is authenticated
            @auth
            // Dispatch event to Livewire component
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('openReportModal', {
                    type,
                    id,
                    title
                });
            } else {
                // Fallback: dispatch custom event
                window.dispatchEvent(new CustomEvent('open-report-modal', {
                    detail: {
                        type,
                        id,
                        title
                    }
                }));
            }
        @else
            alert('Devi essere autenticato per segnalare contenuti');
            window.location.href = '{{ route('login') }}';
        @endauth
        }

        // Handle fallback custom event for browsers where Livewire.dispatch() isn't available immediately
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-report-modal', function(e) {
                // Try to find and call the modal
                if (typeof Livewire !== 'undefined' && Livewire.first()) {
                    Livewire.first().call('openReportModal', e.detail.type, e.detail.id, e.detail.title);
                }
            });
        });
    </script>

    <!-- Report Modal Component -->
    <livewire:report-modal />
</x-layout>
