<x-studio-layout>
    <!-- Breadcrumbs -->
    @php
        $breadcrumbs = [
            ['name' => 'Studio', 'url' => route('channel.edit')],
            ['name' => 'Gestione Canale', 'url' => ''],
        ];
    @endphp

    <div class="flex gap-6">
        <!-- Studio Sidebar -->
        <div
            class="w-64 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 h-fit sticky top-24">
            <!-- Channel Info -->
            <div class="text-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div
                    class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 mx-auto mb-3 relative group cursor-pointer">
                    @if ($userProfile && $userProfile->avatar_url)
                        <img src="{{ Storage::url($userProfile->avatar_url) }}" alt="Avatar"
                            class="w-full h-full object-cover">
                    @else
                        <div
                            class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    <!-- Hover overlay con icona -->
                    <div
                        class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <a href="{{ route('channel.show', $userProfile && $userProfile->channel_name ? $userProfile->channel_name : ($userProfile ? $userProfile->id : $user->id)) }}"
                            target="_blank"
                            class="w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center transition-all transform hover:scale-110 shadow-lg">
                            <i class="fas fa-external-link-alt text-gray-700 text-sm"></i>
                        </a>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                    {{ $userProfile && $userProfile->channel_name ? $userProfile->channel_name : $user->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ number_format($stats['subscribers_count']) }} iscritti</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    <i class="fas fa-mouse-pointer mr-1"></i>
                    Clicca per aprire in nuova scheda
                </p>
            </div>

            <!-- Navigation Menu -->
            <nav class="space-y-1">
                <button onclick="switchMenu('dashboard', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'dashboard' || !request()->has('tab') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-tachometer-alt w-4"></i>
                    <span class="font-medium">Dashboard</span>
                </button>

                <button onclick="switchMenu('content', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'content' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-video w-4"></i>
                    <span class="font-medium">Contenuti</span>
                </button>

                <button onclick="switchMenu('analytics', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'analytics' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-chart-line w-4"></i>
                    <span class="font-medium">Analytics</span>
                </button>

                <button onclick="switchMenu('community', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'community' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-users w-4"></i>
                    <span class="font-medium">Community</span>
                </button>

                <button onclick="switchMenu('reports', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'reports' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-flag w-4"></i>
                    <span class="font-medium">Segnalazioni</span>
                </button>

                <button onclick="switchMenu('customization', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'customization' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-palette w-4"></i>
                    <span class="font-medium">Personalizzazione</span>
                </button>
            </nav>

            <!-- Quick Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">
                    Azioni rapide</h4>
                <div class="space-y-2">
                    <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-camera w-4 text-red-500"></i>
                        <span>Carica video</span>
                    </a>
                    <a href="{{ route('channel.show', $userProfile && $userProfile->channel_name ? $userProfile->channel_name : ($userProfile ? $userProfile->id : $user->id)) }}"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-eye w-4 text-blue-500"></i>
                        <span>Visualizza canale</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Dashboard Canale -->
            <div id="dashboard-content"
                class="content-section {{ request()->query('tab') == 'dashboard' || !request()->has('tab') ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Video pubblicati</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $stats['videos_count'] }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-video text-red-600 dark:text-red-400"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Iscritti</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['subscribers_count']) }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Visualizzazioni</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_views']) }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-eye text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mi piace</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_likes']) }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-thumbs-up text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Videos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Video recenti</h2>
                            <a href="{{ route('videos.my') }}"
                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                Vedi tutti
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($recentVideos->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($recentVideos as $video)
                                    <div class="group">
                                        <div
                                            class="relative aspect-video bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden mb-3">
                                            @if ($video->thumbnail_path)
                                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                    alt="{{ $video->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-4xl text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="absolute top-2 right-2 flex gap-2">
                                                <a href="{{ route('videos.show', $video) }}" target="_blank"
                                                    class="w-8 h-8 bg-blue-500/80 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition-colors"
                                                    title="Vai al video">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('videos.edit', $video) }}"
                                                    class="w-8 h-8 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-colors"
                                                    title="Modifica">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                            </div>
                                            <!-- Status Badge -->
                                            <div class="absolute bottom-2 left-2">
                                                @if ($video->is_public)
                                                    <span
                                                        class="px-2 py-1 bg-green-600/90 text-white text-xs rounded-md flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        Pubblico
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 bg-yellow-600/90 text-white text-xs rounded-md flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        Privato
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <h3 class="font-medium text-gray-900 dark:text-white mb-1 line-clamp-2">
                                            {{ $video->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $video->views_count }} visualizzazioni</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Nessun video pubblicato ancora</p>
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-camera mr-2"></i>
                                    Carica il primo video
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenuti -->
            <div id="content-content"
                class="content-section {{ request()->query('tab') == 'content' ? 'block' : 'hidden' }}">

                <!-- Upload Button -->
                <div class="mb-6">
                    <button onclick="openUploadModal()"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2 font-medium">
                        <i class="fas fa-plus"></i>
                        Carica Nuovo Video
                    </button>
                </div>

                <!-- Bulk Actions Toolbar -->
                <div id="bulk-actions-toolbar"
                    class="hidden mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-blue-800 dark:text-blue-400">
                                <span id="selected-count">0</span> video selezionati
                            </span>
                            <button onclick="clearSelection()"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                Deseleziona tutto
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="bulkSetPublic()"
                                class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                                <i class="fas fa-globe mr-1"></i> Pubblica
                            </button>
                            <button onclick="bulkSetPrivate()"
                                class="px-3 py-1.5 text-xs bg-yellow-600 hover:bg-yellow-700 text-white rounded transition-colors">
                                <i class="fas fa-lock mr-1"></i> Rendi privato
                            </button>
                            <button onclick="bulkDelete()"
                                class="px-3 py-1.5 text-xs bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                <i class="fas fa-trash mr-1"></i> Elimina
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Video List Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">I tuoi video</h2>
                            <span
                                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-sm">
                                {{ $recentVideos->count() }} video
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <label
                                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="checkbox" id="select-all" onchange="toggleSelectAll()"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Seleziona tutto</span>
                            </label>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($recentVideos->count() > 0)
                            <div class="space-y-4">
                                @foreach ($recentVideos as $video)
                                    <div
                                        class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <!-- Checkbox -->
                                        <input type="checkbox" name="selected_videos[]" value="{{ $video->id }}"
                                            onchange="updateSelection()"
                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500 video-checkbox">

                                        <!-- Thumbnail -->
                                        <div
                                            class="relative w-32 h-18 bg-gray-200 dark:bg-gray-700 rounded overflow-hidden flex-shrink-0">
                                            @if ($video->thumbnail_path)
                                                <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                    alt="{{ $video->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Video Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3
                                                        class="font-medium text-gray-900 dark:text-white mb-1 line-clamp-2">
                                                        {{ $video->title }}
                                                    </h3>
                                                    <div
                                                        class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="flex items-center gap-1">
                                                            <i class="fas fa-eye"></i>
                                                            {{ number_format($video->views_count) }} visualizzazioni
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <i class="fas fa-calendar"></i>
                                                            {{ $video->created_at->format('d/m/Y') }}
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            @if ($video->is_public)
                                                                <i class="fas fa-globe text-green-500"></i>
                                                                <span
                                                                    class="text-green-600 dark:text-green-400">Pubblico</span>
                                                            @else
                                                                <i class="fas fa-lock text-yellow-500"></i>
                                                                <span
                                                                    class="text-yellow-600 dark:text-yellow-400">Privato</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center gap-2 ml-4">
                                                    <a href="{{ route('videos.show', $video) }}" target="_blank"
                                                        class="p-2 text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                        title="Vai al video">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('videos.edit', $video) }}"
                                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                        title="Modifica">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </a>
                                                    <button onclick="toggleVideoPrivacy({{ $video->id }})"
                                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                        title="{{ $video->is_public ? 'Rendi privato' : 'Rendi pubblico' }}">
                                                        <i
                                                            class="fas {{ $video->is_public ? 'fa-globe' : 'fa-lock' }} text-sm"></i>
                                                    </button>
                                                    <button onclick="deleteVideo({{ $video->id }})"
                                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                                        title="Elimina">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun video ancora
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6">Inizia caricando il tuo primo video
                                </p>
                                <button onclick="openUploadModal()"
                                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-camera mr-2"></i>
                                    Carica video
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div id="analytics-content"
                class="content-section {{ request()->query('tab') == 'analytics' ? 'block' : 'hidden' }}">
                @php
                    // Gestione fallback per variabili analytics
                    $period = $period ?? 30;
                    $channelStats =
                        $channelStats ??
                        (object) [
                            'total_views' => $stats['total_views'] ?? 0,
                            'total_watch_time' => 0,
                            'total_likes' => $stats['total_likes'] ?? 0,
                            'total_comments' => 0,
                        ];
                    $dailyStats = $dailyStats ?? [];
                    $topVideos = $topVideos ?? collect([]);
                    $trafficSources = $trafficSources ?? collect([]);
                    $demographics = $demographics ?? [
                        'countries' => collect([]),
                        'devices' => collect([]),
                    ];
                @endphp

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-chart-line mr-3 text-red-600"></i>Analytics Avanzate
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Analisi dettagliate e metriche di performance del tuo canale
                    </p>
                </div>

                <!-- Filtri periodo -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Periodo di analisi</h2>
                        <form method="GET" class="flex items-center gap-4">
                            @php
                                $period = $period ?? 30; // Default a 30 giorni
                            @endphp
                            <select name="period" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Ultimi 7 giorni</option>
                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Ultimi 30 giorni</option>
                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Ultimi 3 mesi</option>
                                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Ultimo anno</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Statistiche principali -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-eye text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            @if (isset($channelStats->total_views))
                                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    {{ number_format($channelStats->total_views) }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($channelStats->total_views ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Visualizzazioni totali
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-2xl text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($channelStats->total_watch_time ?? 0, 1) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Ore di visione
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-thumbs-up text-2xl text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($channelStats->total_likes ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Mi piace totali
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-comments text-2xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($channelStats->total_comments ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Commenti totali
                        </p>
                    </div>
                </div>

                <!-- Grafico trend giornaliero -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-chart-area mr-2 text-blue-500"></i>
                            Trend delle visualizzazioni
                        </h2>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Visualizzazioni</span>
                        </div>
                    </div>

                    @if (isset($dailyStats) && count($dailyStats) > 0)
                        <div class="relative">
                            <canvas id="analyticsChart" width="400" height="200"></canvas>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Nessun dato disponibile per il periodo
                                selezionato</p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Video più performanti -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                                Video più performanti
                            </h2>
                        </div>
                        <div class="p-6">
                            @if (isset($topVideos) && $topVideos->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($topVideos as $index => $video)
                                        <div
                                            class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $video->title ?? 'Video eliminato' }}
                                                </h3>
                                                <div
                                                    class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    <span>
                                                        <i class="fas fa-eye mr-1"></i>
                                                        {{ number_format($video->views_count ?? 0) }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-thumbs-up mr-1"></i>
                                                        {{ number_format($video->likes_count ?? 0) }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $video->duration ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <a href="{{ route('videos.show', $video) }}"
                                                class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                Dettagli
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun video con visualizzazioni nel
                                        periodo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Fonti di traffico -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-share-alt mr-2 text-green-500"></i>
                                Fonti di traffico
                            </h2>
                        </div>
                        <div class="p-6">
                            @if (isset($trafficSources) && $trafficSources->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($trafficSources as $source)
                                        <div
                                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-search text-green-600 dark:text-green-400"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                                        {{ ucfirst($source->traffic_source) }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $source->video_count }} video
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ number_format($source->views) }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    visualizzazioni
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-share-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun dato di traffico disponibile</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dati demografici -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Paesi -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-globe mr-2 text-blue-500"></i>
                                Visualizzazioni per paese
                            </h2>
                        </div>
                        <div class="p-6">
                            @if (isset($demographics['countries']) && $demographics['countries']->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($demographics['countries'] as $country)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="text-2xl">{{ \App\Helpers\ChannelHelper::countryFlag($country->country) }}</span>
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    {{ \App\Helpers\ChannelHelper::countryName($country->country) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    @php
                                                        $maxViews = $demographics['countries']->max('views');
                                                        $percentage = ($country->views / $maxViews) * 100;
                                                    @endphp
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900 dark:text-white min-w-[3rem]">
                                                    {{ number_format($country->views) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-globe text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun dato geografico disponibile</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Dispositivi -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-mobile-alt mr-2 text-purple-500"></i>
                                Dispositivi utilizzati
                            </h2>
                        </div>
                        <div class="p-6">
                            @if (isset($demographics['devices']) && $demographics['devices']->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($demographics['devices'] as $device)
                                        <div
                                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                    @if ($device->device_type === 'mobile')
                                                        <i
                                                            class="fas fa-mobile-alt text-purple-600 dark:text-purple-400"></i>
                                                    @elseif($device->device_type === 'tablet')
                                                        <i
                                                            class="fas fa-tablet-alt text-purple-600 dark:text-purple-400"></i>
                                                    @else
                                                        <i
                                                            class="fas fa-desktop text-purple-600 dark:text-purple-400"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                                        {{ ucfirst($device->device_type) }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        @php
                                                            $totalDeviceViews = $demographics['devices']->sum('views');
                                                            $devicePercentage =
                                                                ($device->views / $totalDeviceViews) * 100;
                                                        @endphp
                                                        {{ number_format($devicePercentage, 1) }}% del traffico
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                                    {{ number_format($device->views) }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    visualizzazioni
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-mobile-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun dato sui dispositivi disponibile
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Community -->
            <div id="community-content"
                class="content-section {{ request()->query('tab') == 'community' ? 'block' : 'hidden' }}">

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-users mr-3 text-red-600"></i>Community
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Gestisci la tua community e monitora l'engagement
                    </p>
                </div>

                <!-- Statistiche Community -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            @if (isset($communityStats['new_subscribers_this_month']) && $communityStats['new_subscribers_this_month'] > 0)
                                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    +{{ $communityStats['new_subscribers_this_month'] }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($communityStats['total_subscribers'] ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Iscritti totali
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-plus text-2xl text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            +{{ number_format($communityStats['new_subscribers_this_month'] ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Nuovi iscritti questo mese
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-comments text-2xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($communityStats['total_comments'] ?? 0) }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Commenti totali
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-2xl text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ number_format($communityStats['subscriber_growth_rate'] ?? 0, 1) }}%
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Tasso crescita iscritti
                        </p>
                    </div>
                </div>

                <!-- Ultimi iscritti e Commenti recenti -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Ultimi iscritti -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <i class="fas fa-user-plus mr-2 text-green-500"></i>
                                    Ultimi iscritti
                                </h2>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $recentSubscribers->count() }} di
                                    {{ $communityStats['total_subscribers'] ?? 0 }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($recentSubscribers->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($recentSubscribers as $subscriber)
                                        <div
                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div
                                                class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                                @if ($subscriber['avatar_url'])
                                                    <img src="{{ $subscriber['avatar_url'] }}"
                                                        alt="{{ $subscriber['name'] }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                                                        <span class="text-white text-sm font-medium">
                                                            {{ strtoupper(substr($subscriber['name'], 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $subscriber['name'] }}
                                                </h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $subscriber['time_ago'] }}
                                                </p>
                                            </div>
                                            <div class="text-green-500">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun iscritto ancora</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                        Gli iscritti appariranno qui una volta che qualcuno si iscrive al tuo canale
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Commenti recenti -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <i class="fas fa-comments mr-2 text-blue-500"></i>
                                    Commenti recenti
                                </h2>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $recentComments->count() }} commenti
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($recentComments->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($recentComments as $comment)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                                    @if ($comment['user_avatar'])
                                                        <img src="{{ $comment['user_avatar'] }}"
                                                            alt="{{ $comment['user_name'] }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                                            <span class="text-white text-xs font-medium">
                                                                {{ strtoupper(substr($comment['user_name'], 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                                                            {{ $comment['user_name'] }}
                                                        </h4>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $comment['time_ago'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ $comment['content'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Su: <a href="{{ route('videos.show', $comment['video_id']) }}"
                                                            class="hover:text-red-600 dark:hover:text-red-400">
                                                            {{ Str::limit($comment['video_title'], 30) }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Nessun commento ancora</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                        I commenti sui tuoi video appariranno qui
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Metriche di engagement -->
                <div class="mt-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                            Metriche di Engagement
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-percentage text-2xl text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ number_format($communityStats['subscriber_growth_rate'] ?? 0, 1) }}%
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Tasso crescita iscritti
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-comments text-2xl text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ number_format($communityStats['comment_engagement_rate'] ?? 0, 1) }}%
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Tasso engagement commenti
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-calendar text-2xl text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $communityStats['comments_this_month'] ?? 0 }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Commenti questo mese
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segnalazioni -->
            <div id="reports-content"
                class="content-section {{ request()->query('tab') == 'reports' ? 'block' : 'hidden' }}">

                @php
                    $creatorReports = \App\Models\Report::where('reported_user_id', Auth::id())
                        ->orWhere('channel_id', Auth::id())
                        ->with(['reporter', 'video', 'comment'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();

                    $creatorFeedback = \App\Models\CreatorFeedback::where('creator_id', Auth::id())
                        ->with(['admin', 'report'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();

                    $totalReports = \App\Models\Report::where('reported_user_id', Auth::id())
                        ->orWhere('channel_id', Auth::id())
                        ->count();
                    $pendingReports = \App\Models\Report::where('reported_user_id', Auth::id())
                        ->orWhere('channel_id', Auth::id())
                        ->where('status', 'pending')
                        ->count();
                    $unreadFeedback = \App\Models\CreatorFeedback::where('creator_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                @endphp

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-flag mr-3 text-red-600"></i>Segnalazioni e Feedback
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Visualizza le segnalazioni sui tuoi contenuti e i feedback dagli amministratori
                    </p>
                </div>

                <!-- Statistiche -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flag text-2xl text-red-600 dark:text-red-400"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalReports }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Segnalazioni Totali</p>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $pendingReports }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">In Attesa</p>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-comment-dots text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $creatorFeedback->count() }}</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Feedback Ricevuti</p>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-2xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $unreadFeedback }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Non Letti</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button onclick="switchReportsTab('active-reports')"
                                class="report-tab py-4 px-1 border-b-2 font-medium text-sm transition-colors active-tab border-red-600 text-red-600 dark:text-red-400"
                                data-tab="active-reports">
                                <i class="fas fa-flag mr-2"></i>Segnalazioni Attive
                            </button>
                            <button onclick="switchReportsTab('feedback')"
                                class="report-tab py-4 px-1 border-b-2 font-medium text-sm transition-colors text-gray-500 hover:text-gray-700 dark:text-gray-400"
                                data-tab="feedback">
                                <i class="fas fa-comment-dots mr-2"></i>Feedback
                                @if ($unreadFeedback > 0)
                                    <span
                                        class="ml-2 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 py-0.5 px-2 rounded-full text-xs">{{ $unreadFeedback }}</span>
                                @endif
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Segnalazioni Attive -->
                <div id="active-reports" class="report-tab-content">
                    @if ($creatorReports->count() > 0)
                        <div class="space-y-4">
                            @foreach ($creatorReports as $report)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                                <i class="fas fa-flag text-red-600 dark:text-red-400"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $report->getTypeLabelAttribute() }}
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $report->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                                {{ $report->getStatusLabelAttribute() }}
                                            </span>
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                {{ $report->getPriorityLabelAttribute() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                                            <strong>Motivo:</strong> {{ $report->effective_reason }}
                                        </p>
                                        @if ($report->description)
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                                {{ $report->description }}
                                            </p>
                                        @endif
                                    </div>

                                    @if ($report->video)
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                            <div class="flex items-center gap-3">
                                                @if ($report->video->thumbnail_path)
                                                    <img src="{{ asset('storage/' . $report->video->thumbnail_path) }}"
                                                        class="w-24 h-14 object-cover rounded" alt="Thumbnail">
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">
                                                        {{ $report->video->title }}
                                                    </p>
                                                    <a href="{{ route('videos.show', $report->video) }}"
                                                        class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                                        Visualizza video <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div
                                        class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-user"></i>
                                            <span>Segnalato da: {{ $report->reporter->name }}</span>
                                        </div>
                                        <a href="{{ route('creator.reports') }}"
                                            class="text-red-600 dark:text-red-400 hover:underline">
                                            Vedi dettagli <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($totalReports > 10)
                            <div class="mt-6 text-center">
                                <a href="{{ route('creator.reports') }}"
                                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-list mr-2"></i>
                                    Vedi tutte le segnalazioni ({{ $totalReports }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <i class="fas fa-shield-alt text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessuna segnalazione
                                attiva</h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                Il tuo canale non ha segnalazioni attive al momento.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Feedback -->
                <div id="feedback" class="report-tab-content hidden">
                    @if ($creatorFeedback->count() > 0)
                        <div class="space-y-4">
                            @foreach ($creatorFeedback as $feedback)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ !$feedback->is_read ? 'border-l-4 border-l-blue-500' : '' }}">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <i class="fas fa-comment-dots text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $feedback->title }}
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Da: {{ $feedback->admin->name }} •
                                                    {{ $feedback->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if (!$feedback->is_read)
                                                <span
                                                    class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                                    Nuovo
                                                </span>
                                            @endif
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $feedback->type_color }}-100 dark:bg-{{ $feedback->type_color }}-900/30 text-{{ $feedback->type_color }}-800 dark:text-{{ $feedback->type_color }}-400">
                                                {{ $feedback->type_label }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $feedback->message }}
                                        </p>
                                    </div>

                                    @if ($feedback->report)
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                            <i class="fas fa-link mr-1"></i>
                                            Relativo alla segnalazione #{{ $feedback->report->id }}
                                        </div>
                                    @endif

                                    <div
                                        class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if ($feedback->is_read)
                                                <i class="fas fa-check mr-1"></i>
                                                Letto il {{ $feedback->read_at->format('d/m/Y H:i') }}
                                            @else
                                                <i class="fas fa-envelope mr-1"></i>
                                                Non letto
                                            @endif
                                        </div>
                                        @if (!$feedback->is_read)
                                            <button onclick="markFeedbackAsRead({{ $feedback->id }})"
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors">
                                                <i class="fas fa-check mr-2"></i>Segna come letto
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($unreadFeedback > 0)
                            <div class="mt-6 text-center">
                                <button onclick="markAllFeedbackAsRead()"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-check-double mr-2"></i>
                                    Segna tutti come letti ({{ $unreadFeedback }})
                                </button>
                            </div>
                        @endif
                    @else
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <i class="fas fa-comment-dots text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun feedback ricevuto
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                Non hai ancora ricevuto feedback dagli amministratori.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Personalizzazione -->
            <div id="customization-content"
                class="content-section {{ request()->query('tab') == 'customization' ? 'block' : 'hidden' }}">
                <form id="channelCustomizationForm" method="POST" action="{{ route('channel.update-profile') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Cover Banner -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Immagine di copertina</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">L'immagine che appare in cima al tuo
                                canale</p>
                        </div>
                        <div class="p-6">
                            <div
                                class="h-48 bg-gradient-to-r from-slate-700 to-slate-800 rounded-lg overflow-hidden relative group">
                                @if ($userProfile && $userProfile->banner_url)
                                    <img src="{{ Storage::url($userProfile->banner_url) }}" alt="Cover"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                                        <div class="text-center text-slate-400">
                                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <p class="text-sm font-medium">Cover del canale</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Cover Actions Overlay (Center) -->
                                <div
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                    <label for="bannerInput"
                                        class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $userProfile && $userProfile->banner_url ? 'Cambia cover' : 'Carica cover' }}</span>
                                    </label>
                                </div>

                                <!-- Remove Banner Button (Top Right) -->
                                @if ($userProfile && $userProfile->banner_url)
                                    <button type="button" onclick="removeBanner()"
                                        class="absolute top-3 right-3 w-9 h-9 bg-red-500/90 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all shadow-lg backdrop-blur-sm opacity-0 group-hover:opacity-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Avatar e Informazioni Base -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Foto profilo e informazioni
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="relative group">
                                        <div
                                            class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-2xl bg-gradient-to-br from-red-500 to-red-600 ring-2 ring-gray-100 dark:ring-gray-700 relative">
                                            @if ($userProfile && $userProfile->avatar_url)
                                                <img id="avatarPreview"
                                                    src="{{ Storage::url($userProfile->avatar_url) }}"
                                                    alt="Avatar" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600">
                                                    <span
                                                        class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                            @endif

                                            <!-- Avatar Actions Overlay (Center) -->
                                            <div
                                                class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                                <label for="avatarInput"
                                                    class="cursor-pointer flex flex-col items-center gap-1 p-3 text-white">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                    </svg>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Remove Avatar Button (Top Right) -->
                                        @if ($userProfile && $userProfile->avatar_url)
                                            <button type="button" onclick="removeAvatar()"
                                                class="absolute -top-1 -right-1 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all shadow-md opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Form Fields -->
                                <div class="flex-1 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome
                                            del canale</label>
                                        <input type="text" name="channel_name"
                                            value="{{ old('channel_name', $userProfile->channel_name ?? '') }}"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anteprima
                                            URL</label>
                                        <div class="flex items-center">
                                            <span class="text-gray-500 dark:text-gray-400 mr-2">globio.com/</span>
                                            <div class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-mono"
                                                id="url-preview">
                                                {{ $userProfile->channel_name ?? 'il-tuo-url' }}
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                L'URL del tuo canale si aggiorna automaticamente in base al nome
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                                        <div class="flex items-center">
                                            <span class="text-gray-500 dark:text-gray-400 mr-2">@</span>
                                            <input type="text" name="username"
                                                value="{{ old('username', $userProfile->username ?? $user->name) }}"
                                                placeholder="username"
                                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Username per le menzioni e interazioni (opzionale)
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descrizione
                                            del canale</label>
                                        <textarea name="channel_description" rows="4"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none">{{ old('channel_description', $userProfile->channel_description ?? '') }}</textarea>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 500 caratteri</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Salva modifiche
                        </button>
                    </div>

                    <!-- Hidden File Inputs -->
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
                    <input type="file" id="bannerInput" name="banner" accept="image/*" class="hidden">

                    <!-- Hidden Remove Flags -->
                    <input type="hidden" name="remove_avatar" value="0">
                    <input type="hidden" name="remove_banner" value="0">
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="upload-modal"
        class="hidden fixed inset-0 bg-gray-900/70 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Carica Nuovo Video</h3>
                <button onclick="closeUploadModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
                <livewire:video-upload />

                <!-- Guidelines per l'Upload -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-md font-semibold mb-4 flex items-center" style="color: var(--primary-color);">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Linee Guida per l'Upload
                    </h4>
                    <div class="grid md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <h5 class="font-semibold mb-3 text-gray-900 dark:text-white">Formati Supportati:</h5>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                                <li class="flex items-center"><i
                                        class="fas fa-check-circle mr-2 text-green-500"></i>MP4 (consigliato)</li>
                                <li class="flex items-center"><i
                                        class="fas fa-check-circle mr-2 text-green-500"></i>AVI, MOV, WMV</li>
                                <li class="flex items-center"><i
                                        class="fas fa-check-circle mr-2 text-green-500"></i>FLV, WebM</li>
                                <li class="flex items-center"><i
                                        class="fas fa-check-circle mr-2 text-green-500"></i>Dimensione massima: 1GB
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-semibold mb-3 text-gray-900 dark:text-white">Best Practices:</h5>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                                <li class="flex items-center"><i
                                        class="fas fa-lightbulb mr-2 text-yellow-500"></i>Titolo chiaro e descrittivo
                                </li>
                                <li class="flex items-center"><i
                                        class="fas fa-lightbulb mr-2 text-yellow-500"></i>Thumbnail accattivante</li>
                                <li class="flex items-center"><i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Tag
                                    pertinenti</li>
                                <li class="flex items-center"><i
                                        class="fas fa-lightbulb mr-2 text-yellow-500"></i>Contenuto originale</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Upload Modal Functions
        function openUploadModal() {
            document.getElementById('upload-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on background click
        document.getElementById('upload-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        // Listen for upload completion
        window.addEventListener('upload-completed', function() {
            closeUploadModal();
            // Reload the page to show the new video
            setTimeout(() => location.reload(), 1500);
        });

        // Bulk Selection Functions
        function updateSelection() {
            const checkboxes = document.querySelectorAll('.video-checkbox:checked');
            const selectedCount = checkboxes.length;

            // Update toolbar visibility con animazione
            const toolbar = document.getElementById('bulk-actions-toolbar');
            if (selectedCount > 0) {
                toolbar.classList.remove('hidden');
                document.getElementById('selected-count').textContent = selectedCount;

                // Feedback visivo discreto
                if (selectedCount === 1) {
                    showNotification('1 video selezionato', 'info');
                } else if (selectedCount <= 5) {
                    showNotification(`${selectedCount} video selezionati`, 'info');
                }
            } else {
                toolbar.classList.add('hidden');
            }

            // Update select all checkbox
            const selectAllCheckbox = document.getElementById('select-all');
            const totalCheckboxes = document.querySelectorAll('.video-checkbox').length;
            if (selectedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedCount === totalCheckboxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.video-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateSelection();
        }

        function clearSelection() {
            const checkboxes = document.querySelectorAll('.video-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            updateSelection();
        }

        // Bulk Actions
        async function bulkSetPublic() {
            const selectedIds = getSelectedVideoIds();
            if (selectedIds.length === 0) return;

            if (!confirm(`Sei sicuro di voler rendere pubblici ${selectedIds.length} video?`)) return;

            showNotification(`${selectedIds.length} video verranno resi pubblici...`, 'info');

            try {
                const response = await fetch('{{ route('videos.bulk-update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'set_public',
                        video_ids: selectedIds
                    })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Errore durante l\'operazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            }
        }

        async function bulkSetPrivate() {
            const selectedIds = getSelectedVideoIds();
            if (selectedIds.length === 0) return;

            if (!confirm(`Sei sicuro di voler rendere privati ${selectedIds.length} video?`)) return;

            showNotification(`${selectedIds.length} video verranno resi privati...`, 'info');

            try {
                const response = await fetch('{{ route('videos.bulk-update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'set_private',
                        video_ids: selectedIds
                    })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Errore durante l\'operazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            }
        }

        async function bulkDelete() {
            const selectedIds = getSelectedVideoIds();
            if (selectedIds.length === 0) return;

            if (!confirm(
                    `Sei sicuro di voler eliminare ${selectedIds.length} video? Questa operazione è irreversibile.`))
                return;

            showNotification(`Eliminazione di ${selectedIds.length} video in corso...`, 'info');

            try {
                const response = await fetch('{{ route('videos.bulk-delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        video_ids: selectedIds
                    })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Errore durante l\'operazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            }
        }

        // Individual Video Actions
        async function toggleVideoPrivacy(videoId) {
            try {
                const response = await fetch(`/videos/${videoId}/toggle-privacy`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Errore durante l\'operazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            }
        }

        async function deleteVideo(videoId) {
            if (!confirm('Sei sicuro di voler eliminare questo video? Questa operazione è irreversibile.')) return;

            try {
                const response = await fetch(`/videos/${videoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Errore durante l\'eliminazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            }
        }

        function getSelectedVideoIds() {
            const checkboxes = document.querySelectorAll('.video-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        // Menu switching
        function switchMenu(menuName, event) {
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'text-red-600', 'dark:text-red-400');
                item.classList.add('text-gray-700', 'dark:text-gray-300');
            });

            // Show selected content section
            document.getElementById(menuName + '-content').classList.remove('hidden');

            // Add active class to clicked menu item
            let activeItem;
            if (event && event.target) {
                activeItem = event.target.closest('.menu-item');
            } else {
                // Fallback: find the menu item corresponding to the menuName
                activeItem = document.querySelector(`[onclick*="${menuName}"]`);
            }

            if (activeItem) {
                activeItem.classList.add('bg-red-50', 'dark:bg-red-900/20', 'text-red-600', 'dark:text-red-400');
                activeItem.classList.remove('text-gray-700', 'dark:text-gray-300');
            }

            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', menuName);
            window.history.replaceState({}, '', url);
        }

        // Avatar preview
        document.getElementById('avatarInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview) {
                        preview.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Banner preview
        document.getElementById('bannerInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const bannerContainer = document.querySelector('.h-48');
                    if (bannerContainer) {
                        bannerContainer.innerHTML = `
                            <img src="${event.target.result}" alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Cambia cover</span>
                                </label>
                            </div>
                            <button type="button" onclick="removeBanner()" class="absolute top-3 right-3 w-9 h-9 bg-red-500/90 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all shadow-lg backdrop-blur-sm opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        `;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove avatar
        function removeAvatar() {
            if (confirm('Rimuovere la foto profilo?')) {
                const avatarContainer = document.querySelector('.w-32.h-32');
                if (avatarContainer) {
                    avatarContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600 rounded-full">
                            <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <label for="avatarInput" class="cursor-pointer flex flex-col items-center gap-1 p-3 text-white">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                        </div>
                    `;
                }

                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_avatar';
                removeInput.value = '1';
                document.getElementById('channelCustomizationForm').appendChild(removeInput);
            }
        }

        // Remove banner
        function removeBanner() {
            if (confirm('Rimuovere la cover del canale?')) {
                const bannerContainer = document.querySelector('.h-48');
                if (bannerContainer) {
                    bannerContainer.innerHTML = `
                        <div class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                            <div class="text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">Cover del canale</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Carica cover</span>
                            </label>
                        </div>
                    `;
                }

                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_banner';
                removeInput.value = '1';
                document.getElementById('channelCustomizationForm').appendChild(removeInput);
            }
        }

        // Initialize - set active menu based on URL parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            const shouldOpenUpload = urlParams.get('upload') === 'true';

            if (activeTab) {
                switchMenu(activeTab);
            }

            // If upload=true parameter is present, open upload modal after menu switch
            if (shouldOpenUpload) {
                setTimeout(() => {
                    openUploadModal();
                }, 300); // Small delay to ensure menu switch is complete
            }

            // Initialize channel customization features
            initializeChannelCustomization();
        });

        // Channel customization features
        function initializeChannelCustomization() {
            const channelNameInput = document.querySelector('input[name="channel_name"]');
            const submitButton = document.querySelector('#channelCustomizationForm button[type="submit"]');

            if (channelNameInput && submitButton) {
                // Real-time URL preview
                channelNameInput.addEventListener('input', function() {
                    updateUrlPreview(this.value);
                    validateChannelName(this.value);
                });

                // Form submission handling
                const form = document.getElementById('channelCustomizationForm');
                form.addEventListener('submit', function(e) {
                    handleFormSubmission(e, submitButton);
                });
            }
        }

        function updateUrlPreview(channelName) {
            const preview = document.getElementById('url-preview');
            if (preview) {
                preview.textContent = channelName || 'il-tuo-url';
            }
        }

        function validateChannelName(channelName) {
            const input = document.querySelector('input[name="channel_name"]');
            const feedback = document.getElementById('channel-name-feedback');

            // Remove existing feedback
            input.classList.remove('border-red-500', 'border-green-500');
            if (feedback) {
                feedback.remove();
            }

            if (!channelName) {
                return;
            }

            // Basic validation - più permissiva per caratteri italiani e spazi
            const validPattern = /^[a-zA-Z0-9\s\-_àèéìíîòóùúÀÈÉÌÍÎÒÓÙÚ]+$/;
            const isValid = validPattern.test(channelName) && channelName.length >= 3 && channelName.length <= 50;

            if (isValid) {
                input.classList.add('border-green-500');
                showFeedback('Nome canale valido', 'success');
            } else {
                input.classList.add('border-red-500');
                let message = 'Il nome del canale può contenere lettere, numeri, spazi, trattini e underscore';
                if (channelName.length < 3) {
                    message = 'Il nome del canale deve essere di almeno 3 caratteri';
                } else if (channelName.length > 50) {
                    message = 'Il nome del canale deve essere di massimo 50 caratteri';
                }
                showFeedback(message, 'error');
            }
        }

        function showFeedback(message, type) {
            const input = document.querySelector('input[name="channel_name"]');
            const existingFeedback = document.getElementById('channel-name-feedback');

            if (existingFeedback) {
                existingFeedback.remove();
            }

            const feedback = document.createElement('div');
            feedback.id = 'channel-name-feedback';
            feedback.className = `text-xs mt-1 flex items-center ${
                type === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
            }`;
            feedback.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-1"></i>
                ${message}
            `;

            input.parentNode.appendChild(feedback);
        }

        async function handleFormSubmission(e, submitButton) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData();

            // Collect all form data explicitly to ensure nothing is missed
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.name && !element.disabled) {
                    if (element.type === 'file') {
                        if (element.files.length > 0) {
                            formData.append(element.name, element.files[0]);
                            console.log('Added file:', element.name, element.files[0].name);
                        }
                    } else if (element.type === 'checkbox' || element.type === 'radio') {
                        if (element.checked) {
                            formData.append(element.name, element.value);
                            console.log('Added checked field:', element.name, element.value);
                        }
                    } else {
                        formData.append(element.name, element.value);
                        console.log('Added field:', element.name, element.value);
                    }
                }
            }

            // Debug: Log all form data entries
            console.log('Final FormData entries:');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(key + ': [File] ' + value.name + ' (' + value.size + ' bytes)');
                } else {
                    console.log(key + ': ' + value);
                }
            }

            const originalText = submitButton.innerHTML;

            // Show loading state con stile elegante
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <div class="flex items-center">
                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    Salvando...
                </div>
            `;
            submitButton.classList.add('opacity-75');

            try {
                // Get CSRF token with fallback
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('Token CSRF non trovato. Ricarica la pagina.');
                }

                // Add CSRF token explicitly to FormData
                formData.append('_token', csrfToken);
                formData.append('_method', 'PUT');

                // Debug: Log request details
                console.log('Sending request to:', form.action);
                console.log('Request method: PUT');
                console.log('FormData size:', formData.size);
                console.log('Content-Type will be:', formData.type);

                const response = await fetch(form.action, {
                    method: 'POST', // Use POST since we're adding _method override
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    // Handle HTTP errors
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        showValidationErrors(result.errors || {});
                        showNotification(result.message || 'Errore durante il salvataggio', 'error');
                    } else {
                        // Handle non-JSON responses (like 419 CSRF errors)
                        const text = await response.text();
                        if (response.status === 419) {
                            showNotification('Sessione scaduta. Ricarica la pagina e riprova.', 'error');
                        } else {
                            showNotification(`Errore del server (${response.status}): ${text.substring(0, 100)}`,
                                'error');
                        }
                    }
                    return;
                }

                const result = await response.json();

                if (response.ok) {
                    // Success
                    showNotification('Canale aggiornato con successo!', 'success');

                    // If channel name changed, redirect to new URL
                    if (result.redirect_url) {
                        setTimeout(() => {
                            window.location.href = result.redirect_url;
                        }, 1500);
                    } else {
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
            } catch (error) {
                console.error('Errore durante il salvataggio:', error);

                let errorMessage = 'Errore di connessione. Riprova.';
                if (error.message.includes('fetch')) {
                    errorMessage = 'Impossibile connettersi al server. Verifica la connessione internet.';
                } else if (error.message.includes('CSRF')) {
                    errorMessage = error.message;
                }

                showNotification(errorMessage, 'error');
            } finally {
                // Restore button con animazione
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-75');
                submitButton.innerHTML = originalText;
            }
        }

        function showValidationErrors(errors) {
            // Clear existing error messages
            document.querySelectorAll('.field-error').forEach(el => el.remove());

            // Show new error messages con stile più elegante
            Object.keys(errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('border-red-300', 'dark:border-red-600', 'focus:border-red-500',
                        'focus:ring-red-500');

                    const errorDiv = document.createElement('div');
                    errorDiv.className =
                        'field-error flex items-center mt-2 text-red-600 dark:text-red-400 text-sm bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-md px-3 py-2';
                    errorDiv.innerHTML = `
                        <div class="w-4 h-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-2 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
                        </div>
                        <span class="flex-1">${errors[field][0]}</span>
                    `;

                    input.parentNode.appendChild(errorDiv);
                }
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');

            // Stili professionali e eleganti per tutti i tipi
            const isSuccess = type === 'success';
            const isError = type === 'error';
            const isInfo = type === 'info';

            let bgColor, iconColor, iconClass;

            if (isSuccess) {
                bgColor = 'bg-white dark:bg-gray-800 border-l-4 border-l-green-500';
                iconColor = 'text-green-500';
                iconClass = 'fa-check-circle';
            } else if (isError) {
                bgColor = 'bg-white dark:bg-gray-800 border-l-4 border-l-red-500';
                iconColor = 'text-red-500';
                iconClass = 'fa-exclamation-circle';
            } else { // info
                bgColor = 'bg-white dark:bg-gray-800 border-l-4 border-l-blue-500';
                iconColor = 'text-blue-500';
                iconClass = 'fa-info-circle';
            }
            const textColor = 'text-gray-900 dark:text-gray-100';

            notification.className =
                `fixed top-6 right-6 z-50 ${bgColor} ${textColor} max-w-md shadow-xl rounded-lg border border-gray-200 dark:border-gray-700 transform transition-all duration-300 ease-in-out opacity-0 translate-x-full`;

            notification.innerHTML = `
                <div class="flex items-start p-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${isSuccess ? 'bg-green-50 dark:bg-green-900/20' : isError ? 'bg-red-50 dark:bg-red-900/20' : 'bg-blue-50 dark:bg-blue-900/20'}">
                            <i class="fas ${iconClass} ${iconColor} text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium leading-5">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Animazione di entrata
            setTimeout(() => {
                notification.classList.remove('opacity-0', 'translate-x-full');
            }, 50);

            // Auto remove after 4 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 4000);
        }

        // Funzioni per i tab delle segnalazioni
        window.switchReportsTab = function(tabName) {
            // Nascondi tutti i contenuti dei tab
            document.querySelectorAll('.report-tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Rimuovi classe active da tutti i tab
            document.querySelectorAll('.report-tab').forEach(tab => {
                tab.classList.remove('active-tab', 'border-red-600', 'text-red-600', 'dark:text-red-400');
                tab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400');
            });

            // Mostra il tab selezionato
            document.getElementById(tabName).classList.remove('hidden');

            // Aggiungi classe active al tab selezionato
            const activeTab = document.querySelector(`.report-tab[data-tab="${tabName}"]`);
            if (activeTab) {
                activeTab.classList.add('active-tab', 'border-red-600', 'text-red-600', 'dark:text-red-400');
                activeTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400');
            }
        };

        // Funzioni per i feedback
        window.markFeedbackAsRead = function(feedbackId) {
            fetch(`/creator/feedback/${feedbackId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        window.markAllFeedbackAsRead = function() {
            if (confirm('Sei sicuro di voler segnare tutti i feedback come letti?')) {
                fetch('/creator/feedback/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        };

        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = initializeAnalyticsChart;
            document.head.appendChild(script);
        } else {
            initializeAnalyticsChart();
        }

        function initializeAnalyticsChart() {
            const canvas = document.getElementById('analyticsChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // Dati di esempio per il grafico - verifica se la variabile esiste
            let data = [];
            try {
                data = @json($dailyStats ?? []);
            } catch (e) {
                // Se c'è un errore nella serializzazione, usa dati vuoti
                data = [];
            }

            if (data && data.length > 0) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => {
                            return new Date(item.date).toLocaleDateString('it-IT', {
                                day: 'numeric',
                                month: 'short'
                            });
                        }),
                        datasets: [{
                            label: 'Visualizzazioni',
                            data: data.map(item => item.views),
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
</x-studio-layout>
