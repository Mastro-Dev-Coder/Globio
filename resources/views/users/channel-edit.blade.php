<x-studio-layout>
    <!-- Breadcrumbs -->
    @php
        $breadcrumbs = [
            ['name' => __('ui.studio'), 'url' => route('channel.edit')],
            ['name' => __('ui.channel_management'), 'url' => ''],
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
                        <img src="{{ Storage::url($userProfile->avatar_url) }}" alt="{{ __('ui.avatar') }}"
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
                    {{ number_format($stats['subscribers_count']) }} {{ __('ui.subscribers_count') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    <i class="fas fa-mouse-pointer mr-1"></i>
                    {{ __('ui.channel_edit_open_new_tab') }}
                </p>
            </div>

            <!-- Navigation Menu -->
            <nav class="space-y-1">
                <button onclick="switchMenu('dashboard', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'dashboard' || !request()->has('tab') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-tachometer-alt w-4"></i>
                    <span class="font-medium">{{ __('ui.dashboard') }}</span>
                </button>

                <button onclick="switchMenu('content', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'content' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-video w-4"></i>
                    <span class="font-medium">{{ __('ui.content') }}</span>
                </button>

                <button onclick="switchMenu('analytics', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'analytics' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-chart-line w-4"></i>
                    <span class="font-medium">{{ __('ui.analytics') }}</span>
                </button>

                <button onclick="switchMenu('community', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'community' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-users w-4"></i>
                    <span class="font-medium">{{ __('ui.community') }}</span>
                </button>

                <button onclick="switchMenu('reports', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'reports' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-flag w-4"></i>
                    <span class="font-medium">{{ __('ui.reports') }}</span>
                </button>

                <button onclick="switchMenu('customization', event)"
                    class="menu-item w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm {{ request()->query('tab') == 'customization' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-palette w-4"></i>
                    <span class="font-medium">{{ __('ui.customization') }}</span>
                </button>
            </nav>

            <!-- Quick Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">
                    {{ __('ui.quick_actions') }}</h4>

                <div class="space-y-2">
                    <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-camera w-4 text-red-500"></i>
                        <span>{{ __('ui.upload_video') }}</span>
                    </a>
                    <a href="{{ route('channel.show', $userProfile && $userProfile->channel_name ? $userProfile->channel_name : ($userProfile ? $userProfile->id : $user->id)) }}"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-eye w-4 text-blue-500"></i>
                        <span>{{ __('ui.view_channel') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Dashboard Canale -->
            <div id="dashboard-content"
                class="content-section {{ request()->query('tab') == 'dashboard' || !request()->has('tab') ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    <!-- Featured Latest Video + Recent List -->
                    <div class="xl:col-span-7">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('ui.channel_edit_latest_video') }}
                                    </h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('ui.channel_edit_latest_video_subtitle') }}
                                    </p>
                                </div>
                                <a href="{{ route('channel.edit', auth()->user()->userProfile->channel_name) }}?tab=content"
                                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                    {{ __('ui.view_all') }}
                                </a>
                            </div>
                            <div class="p-6">
                                @if ($latestVideo)
                                    <div
                                        class="group relative rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <a href="{{ route('videos.show', $latestVideo) }}" target="_blank"
                                            class="block">
                                            <div class="relative aspect-video">
                                                @if ($latestVideo->thumbnail_path)
                                                    <img src="{{ asset('storage/' . $latestVideo->thumbnail_path) }}"
                                                        alt="{{ $latestVideo->title }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-video text-5xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent">
                                                </div>
                                                <div class="absolute bottom-4 left-4 right-4">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-800">
                                                            {{ $latestVideo->is_public ? __('ui.public') : __('ui.private') }}
                                                        </span>
                                                        <span
                                                            class="text-xs text-white/80">{{ $latestVideo->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <h3 class="mt-2 text-xl font-semibold text-white line-clamp-2">
                                                        {{ $latestVideo->title }}
                                                    </h3>
                                                    <div class="mt-2 flex items-center gap-4 text-xs text-white/80">
                                                        <span class="flex items-center gap-1">
                                                            <i class="fas fa-eye"></i>
                                                            {{ number_format($latestVideo->views_count) }}
                                                            {{ __('ui.views_count') }}
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <i class="fas fa-thumbs-up"></i>
                                                            {{ number_format($latestVideo->likes_count) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <i class="fas fa-video text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-500 dark:text-gray-400">{{ __('ui.no_videos_published') }}</p>
                                        <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                                            class="inline-flex items-center mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                            <i class="fas fa-camera mr-2"></i>
                                            {{ __('ui.upload_first_video') }}
                                        </a>
                                    </div>
                                @endif

                                @if ($recentVideos->count() > 1)
                                    <div class="mt-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                {{ __('ui.channel_edit_recent_videos') }}
                                            </h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ max(0, $recentVideos->count() - 1) }} {{ __('ui.video_short') }}
                                            </span>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach ($recentVideos->skip(1)->take(5) as $video)
                                                <a href="{{ route('videos.show', $video) }}" target="_blank"
                                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                    <div
                                                        class="w-24 h-14 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                                        @if ($video->thumbnail_path)
                                                            <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                                alt="{{ $video->title }}"
                                                                class="w-full h-full object-cover">
                                                        @else
                                                            <div
                                                                class="w-full h-full flex items-center justify-center">
                                                                <i class="fas fa-video text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p
                                                            class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                            {{ $video->title }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ number_format($video->views_count) }} {{ __('ui.views_count') }} -
                                                            {{ $video->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    <i class="fas fa-chevron-right text-gray-300"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Channel Analytics Column -->
                    <div class="xl:col-span-5 space-y-6">
                        <div
                            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.channel_analytics') }}</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.overview') }}</span>
                            </div>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/60 dark:bg-gray-800/60">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                                            <i class="fas fa-video text-red-600 dark:text-red-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.content') }}</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ $stats['videos_count'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/60 dark:bg-gray-800/60">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                                            <i class="fas fa-eye text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.views_metric') }}</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ number_format($stats['total_views']) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/60 dark:bg-gray-800/60">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.community') }}</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ number_format($stats['subscribers_count']) }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('ui.channel_edit_total_subscribers') }}
                                    </p>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white/60 dark:bg-gray-800/60">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center">
                                            <i class="fas fa-thumbs-up text-yellow-600 dark:text-yellow-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('ui.channel_edit_engagement') }}
                                            </p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ number_format($stats['total_likes']) }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('ui.channel_edit_total_likes') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.quick_actions') }}</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('ui.channel_edit_management') }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                                        <i class="fas fa-upload text-red-600 dark:text-red-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('ui.upload_video') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_new_content') }}
                                        </p>
                                    </div>
                                </a>
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=analytics"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                        <i class="fas fa-chart-line text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ __('ui.analytics') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_data_performance') }}
                                        </p>
                                    </div>
                                </a>
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=community"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                                        <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('ui.community') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_subscribers_feedback') }}
                                        </p>
                                    </div>
                                </a>
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center">
                                        <i class="fas fa-list text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('ui.content') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_video_management') }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- {{ __('ui.content') }} -->
            <div id="content-content"
                class="content-section {{ request()->query('tab') == 'content' ? 'block' : 'hidden' }}">

                <!-- Upload Button -->
                <div class="mb-6">
                    <button onclick="openUploadModal()"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2 font-medium">
                        <i class="fas fa-plus"></i>
                        {{ __('ui.upload_new_video') }}
                    </button>
                </div>

                <!-- Bulk Actions Toolbar -->
                <div id="bulk-actions-toolbar"
                    class="hidden mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-blue-800 dark:text-blue-400">
                                <span id="selected-count">0</span> {{ __('ui.channel_edit_videos_selected') }}
                            </span>
                            <button onclick="clearSelection()"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                {{ __('ui.channel_edit_clear_selection') }}
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="bulkSetPublic()"
                                class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                                <i class="fas fa-globe mr-1"></i> {{ __('ui.channel_edit_publish') }}
                            </button>
                            <button onclick="bulkSetPrivate()"
                                class="px-3 py-1.5 text-xs bg-yellow-600 hover:bg-yellow-700 text-white rounded transition-colors">
                                <i class="fas fa-lock mr-1"></i> {{ __('ui.channel_edit_make_private') }}
                            </button>
                            <button onclick="bulkDelete()"
                                class="px-3 py-1.5 text-xs bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                <i class="fas fa-trash mr-1"></i> {{ __('ui.delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Toolbar -->
                <div id="bulk-actions-toolbar"
                    class="hidden mb-6 p-4 bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                <span id="selected-count">0</span> {{ __('ui.channel_edit_videos_selected') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="bulkSetPublic()"
                                class="px-3 py-2 text-xs bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-globe mr-1"></i> {{ __('ui.channel_edit_publish') }}
                            </button>
                            <button onclick="bulkSetPrivate()"
                                class="px-3 py-2 text-xs bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-lock mr-1"></i> {{ __('ui.private') }}
                            </button>
                            <button onclick="bulkDelete()"
                                class="px-3 py-2 text-xs bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i> {{ __('ui.delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Video List Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('ui.channel_edit_your_videos') }}
                            </h2>
                            <span
                                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-xs">
                                {{ $recentVideos->count() }} {{ __('ui.video_short') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-filter"></i>
                            {{ __('ui.channel_edit_content_management') }}
                        </div>
                    </div>

                    <div class="px-6 pb-6">
                        @if ($recentVideos->count() > 0)

                            <!-- HEADER -->
                            <div
                                class="hidden xl:grid grid-cols-11 gap-2 px-3 py-3 text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">

                                <!-- checkbox + video -->
                                <div class="col-span-4 flex items-center gap-2">
                                    <label class="flex cursor-pointer">
                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll()"
                                            class="peer sr-only">
                                        <span
                                            class="h-5 w-5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 flex items-center justify-center transition peer-checked:bg-red-600 peer-checked:border-red-600">
                                            <i
                                                class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                        </span>
                                    </label>
                                    <span>{{ __('ui.videos') }}</span>
                                </div>

                                <div class="col-span-2">{{ __('ui.visibility') }}</div>
                                <div class="col-span-2">{{ __('ui.date') }}</div>
                                <div class="col-span-1">{{ __('ui.views_count') }}</div>
                                <div class="col-span-1">{{ __('ui.comments') }}</div>
                                <div class="col-span-1">{{ __('ui.like_rate') }}</div>
                            </div>


                            <!-- ROWS -->
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">

                                @foreach ($recentVideos as $video)
                                    @php
                                        $likeRate =
                                            $video->views_count > 0
                                                ? round(($video->likes_count / $video->views_count) * 100, 1)
                                                : 0;
                                    @endphp

                                    <div class="group grid grid-cols-1 xl:grid-cols-11 gap-2 px-3 py-4 items-center">

                                        <!-- checkbox + video -->
                                        <div class="xl:col-span-4 flex items-center gap-2 min-w-0">

                                            <!-- checkbox -->
                                            <label class="flex cursor-pointer shrink-0">
                                                <input type="checkbox" name="selected_videos[]"
                                                    value="{{ $video->id }}" onchange="updateSelection()"
                                                    class="peer sr-only video-checkbox">
                                                <span
                                                    class="h-5 w-5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 flex items-center justify-center transition peer-checked:bg-red-600 peer-checked:border-red-600">
                                                    <i
                                                        class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                                </span>
                                            </label>

                                            <!-- thumbnail -->
                                            <a href="{{ route('videos.show', $video->video_url) }}" target="_blank"
                                                class="block">
                                                <div
                                                    class="relative w-28 h-16 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">

                                                    @if ($video->thumbnail_path)
                                                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}"
                                                            alt="{{ $video->title }}"
                                                            class="w-full h-full object-cover">

                                                        <div
                                                            class="absolute bottom-1 right-1 bg-black/80 text-white text-[11px] px-1.5 py-0.5 rounded">
                                                            {{ $video->formatted_duration }}
                                                        </div>
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <i class="fas fa-video text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>

                                            <!-- titolo -->
                                            <div class="min-w-0">
                                                <a href="{{ route('videos.show', $video->video_url) }}"
                                                    target="_blank" class="block">
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:underline">
                                                        {{ $video->title }}
                                                    </p>
                                                </a>
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400 truncate group-hover:invisible">
                                                    {{ $video->description }}
                                                </p>
                                                <div
                                                    class="flex space-x-3 opacity-0 bg-white dark:bg-gray-800 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity">

                                                    <a href="{{ route('videos.show', $video->video_url) }}"
                                                        class="w- h-9 rounded-lg hover:bg-gray-300/70 dark:hover:bg-gray-900/70 rounded-full p-2 flex items-center justify-center transition cursor-pointer">
                                                        <i class="fas fa-play text-sm"></i>
                                                    </a>

                                                    <a href="{{ route('videos.download', $video) }}"
                                                        class="w-9 h-9 rounded-lg hover:bg-gray-300/70 dark:hover:bg-gray-900/70 rounded-full p-2 flex items-center justify-center transition cursor-pointer">
                                                        <i class="fas fa-download text-sm"></i>
                                                    </a>

                                                    <button type="button"
                                                        onclick="openPlaylistPicker({{ $video->id }})"
                                                        class="w-9 h-9 rounded-lg hover:bg-gray-300/70 dark:hover:bg-gray-900/70 rounded-full p-2 flex items-center justify-center  transition cursor-pointer">
                                                        <i class="fas fa-list text-sm"></i>
                                                    </button>

                                                    <a href="{{ route('videos.edit', $video) }}"
                                                        class="w-9 h-9 rounded-lg hover:bg-gray-300/70 dark:hover:bg-gray-900/70 rounded-full p-2 flex items-center justify-center  transition cursor-pointer">
                                                        <i class="fas fa-pen text-sm"></i>
                                                    </a>

                                                    <button type="button"
                                                        onclick="deleteVideo('{{ $video->video_url }}')"
                                                        class="w-9 h-9 rounded-lg hover:bg-gray-300/70 dark:hover:bg-gray-900/70 rounded-full p-2 flex items-center justify-center transition cursor-pointer">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- visibility -->
                                        <div class="xl:col-span-2">
                                            <select
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 visibility-select"
                                                data-video-id="{{ $video->id }}"
                                                data-video-key="{{ $video->video_url }}"
                                                data-current="{{ $video->is_public ? 'public' : 'private' }}">
                                                <option value="public" {{ $video->is_public ? 'selected' : '' }}>
                                                    {{ __('ui.public') }}
                                                </option>
                                                <option value="private" {{ !$video->is_public ? 'selected' : '' }}>
                                                    {{ __('ui.private') }}
                                                </option>
                                            </select>
                                        </div>


                                        <!-- date -->
                                        <div class="xl:col-span-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="font-medium">{{ $video->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $video->created_at->diffForHumans() }}
                                            </div>
                                        </div>


                                        <!-- views -->
                                        <div class="xl:col-span-1 text-sm text-gray-700 dark:text-gray-300">
                                            {{ number_format($video->views_count) }}
                                        </div>


                                        <!-- comments -->
                                        <div class="xl:col-span-1 text-sm text-gray-700 dark:text-gray-300">
                                            {{ number_format($video->comments_count ?? 0) }}
                                        </div>


                                        <!-- like rate -->
                                        <div class="xl:col-span-1">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $likeRate }}%
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format($video->likes_count) }} {{ __('ui.like_short') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium mb-2">{{ __('ui.channel_edit_no_videos_yet') }}</h3>
                                <p class="text-gray-500 mb-6">{{ __('ui.channel_edit_start_uploading') }}</p>

                                <button onclick="openUploadModal()"
                                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                    <i class="fas fa-camera mr-2"></i>
                                    {{ __('ui.upload_video') }}
                                </button>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

            <!-- Playlist Picker Modal -->
            <div id="playlist-picker" class="hidden fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/50" onclick="closePlaylistPicker()"></div>
                <div
                    class="relative max-w-lg mx-auto mt-24 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('ui.channel_edit_add_to_playlist') }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.channel_edit_select_playlist') }}
                            </p>
                        </div>
                        <button type="button" onclick="closePlaylistPicker()"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="playlist-picker-content" class="p-6">
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('ui.channel_edit_loading_playlists') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div id="analytics-content"
                class="content-section {{ request()->query('tab') == 'analytics' ? 'block' : 'hidden' }}">
                    @php
                        $period = $period ?? 30;

                        if (!isset($channelStats) || ($channelStats->total_views ?? 0) == 0) {
                            $totalViews = $stats['total_views'] ?? 0;
                            $totalLikes = $stats['total_likes'] ?? 0;
                            $totalComments = \App\Models\Comment::whereHas('video', function ($query) use ($user) {
                                $query->where('user_id', $user->id)->published();
                            })
                                ->where('status', 'approved')
                                ->count();

                            $channelStats = (object) [
                                'total_views' => $totalViews,
                                'total_watch_time' => $totalViews * 5,
                                'total_likes' => $totalLikes,
                                'total_comments' => $totalComments,
                            ];
                        }

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
                            <i class="fas fa-chart-line mr-3 text-red-600"></i>{{ __('ui.channel_edit_advanced_analytics') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('ui.channel_edit_advanced_analytics_subtitle') }}
                        </p>
                    </div>

                    <!-- Filtri periodo -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('ui.channel_edit_analysis_period') }}
                            </h2>
                            <form method="GET" class="flex items-center gap-4">
                                @php
                                    $period = $period ?? 30; // Default a 30 giorni
                                @endphp
                                <select name="period" onchange="this.form.submit()"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>
                                        {{ __('ui.channel_edit_last_7_days') }}
                                    </option>
                                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>
                                        {{ __('ui.channel_edit_last_30_days') }}
                                    </option>
                                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>
                                        {{ __('ui.channel_edit_last_3_months') }}
                                    </option>
                                    <option value="365" {{ $period == 365 ? 'selected' : '' }}>
                                        {{ __('ui.channel_edit_last_year') }}
                                    </option>
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
                                {{ __('ui.channel_edit_total_views') }}
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
                                {{ number_format(($channelStats->total_watch_time ?? 0) / 60, 1) }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('ui.channel_edit_estimated_watch_hours') }}
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
                                {{ __('ui.channel_edit_total_likes') }}
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
                                {{ __('ui.channel_edit_total_comments') }}
                            </p>
                        </div>
                    </div>

                    <!-- Grafico trend giornaliero -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-chart-area mr-2 text-blue-500"></i>
                                {{ __('ui.channel_edit_views_trend') }}
                            </h2>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.views_metric') }}</span>
                            </div>
                        </div>

                        @if (isset($dailyStats) && count($dailyStats) > 0)
                            <div class="relative">
                                <canvas id="analyticsChart" width="400" height="200"></canvas>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ __('ui.channel_edit_no_data_for_period') }}
                                </p>
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
                                    {{ __('ui.channel_edit_top_videos') }}
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
                                                        {{ $video->title ?? ($video->video->title ?? __('ui.channel_edit_video_deleted')) }}
                                                    </h3>
                                                    <div
                                                        class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                        <span>
                                                            <i class="fas fa-eye mr-1"></i>
                                                            {{ number_format($video->views_count ?? ($video->total_views ?? 0)) }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-thumbs-up mr-1"></i>
                                                            {{ number_format($video->likes_count ?? ($video->total_likes ?? 0)) }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $video->duration ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <a href="{{ route('videos.show', $video->video) }}"
                                                    class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                    {{ __('ui.channel_edit_details') }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @if ($topVideosFallback->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($topVideosFallback as $index => $video)
                                                <div
                                                    class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                            {{ $index + 1 }}
                                                        </div>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <h3
                                                            class="font-semibold text-gray-900 dark:text-white truncate">
                                                            {{ $video->title }}
                                                        </h3>
                                                        <div
                                                            class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            <span>
                                                                <i class="fas fa-eye mr-1"></i>
                                                                {{ number_format($video->views_count) }}
                                                            </span>
                                                            <span>
                                                                <i class="fas fa-thumbs-up mr-1"></i>
                                                                {{ number_format($video->likes_count) }}
                                                            </span>
                                                            <span>
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $video->duration ?? 'N/A' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                <a href="{{ route('videos.show', $video) }}"
                                                    class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                    {{ __('ui.channel_edit_details') }}
                                                </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
                                            <p class="text-gray-500 dark:text-gray-400">
                                                {{ __('ui.channel_edit_no_videos_in_period') }}
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Fonti di traffico -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <i class="fas fa-share-alt mr-2 text-green-500"></i>
                                    {{ __('ui.channel_edit_traffic_sources') }}
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
                                                        <i
                                                            class="fas fa-search text-green-600 dark:text-green-400"></i>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="font-medium text-gray-900 dark:text-white capitalize">
                                                            {{ ucfirst($source->traffic_source) }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $source->video_count }} {{ __('ui.video_short') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ number_format($source->views) }}
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ __('ui.views_count') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-share-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_no_traffic_data') }}
                                        </p>
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
                                    {{ __('ui.channel_edit_views_by_country') }}
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
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_no_geo_data') }}
                                        </p>
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
                                    {{ __('ui.channel_edit_devices_used') }}
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
                                                        <h4
                                                            class="font-medium text-gray-900 dark:text-white capitalize">
                                                            {{ ucfirst($device->device_type) }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            @php
                                                                $totalDeviceViews = $demographics['devices']->sum(
                                                                    'views',
                                                                );
                                                                $devicePercentage =
                                                                    ($device->views / $totalDeviceViews) * 100;
                                                            @endphp
                                                            {{ number_format($devicePercentage, 1) }}%
                                                            {{ __('ui.channel_edit_of_traffic') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                                                        {{ number_format($device->views) }}
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ __('ui.views_count') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-mobile-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_no_device_data') }}
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
                            <i class="fas fa-users mr-3 text-red-600"></i>{{ __('ui.community') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('ui.channel_edit_manage_community') }}
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
                                {{ __('ui.channel_edit_total_subscribers') }}
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
                                {{ __('ui.channel_edit_new_subscribers_month') }}
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
                                {{ __('ui.channel_edit_total_comments') }}
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
                                {{ __('ui.channel_edit_subscriber_growth_rate') }}
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
                                        {{ __('ui.channel_edit_latest_subscribers') }}
                                    </h2>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $recentSubscribers->count() }} {{ __('ui.channel_edit_of') }}
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
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_no_subscribers_yet') }}
                                        </p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                            {{ __('ui.channel_edit_subscribers_will_appear') }}
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
                                        {{ __('ui.channel_edit_recent_comments') }}
                                    </h2>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $recentComments->count() }} {{ __('ui.comments') }}
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
                                                            <h4
                                                                class="font-medium text-gray-900 dark:text-white text-sm">
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
                                                            {{ __('ui.channel_edit_on') }} <a
                                                                href="{{ route('videos.show', $comment['video_id']) }}"
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
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.channel_edit_no_comments_yet') }}
                                        </p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                            {{ __('ui.channel_edit_comments_will_appear') }}
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
                                {{ __('ui.channel_edit_engagement_metrics') }}
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
                                        {{ __('ui.channel_edit_subscriber_growth_rate') }}
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
                                        {{ __('ui.channel_edit_comment_engagement_rate') }}
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
                                        {{ __('ui.channel_edit_comments_this_month') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segnalazioni -->
                <div id="reports-content"
                    class="content-section {{ request()->query('tab') == 'reports' ? 'block' : 'hidden' }}">
                    <!-- Header -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            <i class="fas fa-flag mr-3 text-red-600"></i>{{ __('ui.channel_edit_reports_and_feedback') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('ui.channel_edit_reports_subtitle') }}
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
                                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $totalReports }}
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('ui.channel_edit_total_reports') }}
                                </p>
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
                                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $pendingReports }}
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('ui.channel_edit_pending') }}
                                </p>
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
                                    {{ $creator->count() }}</div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('ui.channel_edit_feedback_received') }}
                                </p>
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
                                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $unread }}
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.unread') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="flex space-x-8" aria-label="{{ __('ui.tabs') }}">
                                <button onclick="switchReportsTab('active-reports')"
                                    class="report-tab py-4 px-1 border-b-2 font-medium text-sm transition-colors active-tab border-red-600 text-red-600 dark:text-red-400"
                                    data-tab="active-reports">
                                    <i class="fas fa-flag mr-2"></i>{{ __('ui.active_reports') }}
                                </button>
                                <button onclick="switchReportsTab('feedback')"
                                    class="report-tab py-4 px-1 border-b-2 font-medium text-sm transition-colors text-gray-500 hover:text-gray-700 dark:text-gray-400"
                                    data-tab="feedback">
                                    <i class="fas fa-comment-dots mr-2"></i>{{ __('ui.feedback') }}
                                    @if ($unread > 0)
                                        <span
                                            class="ml-2 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 py-0.5 px-2 rounded-full text-xs">{{ $unread }}</span>
                                    @endif
                                </button>
                            </nav>
                        </div>
                    </div>

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
                                                <strong>{{ __('ui.channel_edit_reason') }}</strong>
                                                {{ $report->effective_reason }}
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
                                                            class="w-24 h-14 object-cover rounded"
                                                            alt="{{ __('ui.channel_edit_thumbnail') }}">
                                                    @endif
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white">
                                                            {{ $report->video->title }}
                                                        </p>
                                                        <a href="{{ route('videos.show', $report->video) }}"
                                                            class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                                            {{ __('ui.channel_edit_view_video') }} <i
                                                                class="fas fa-external-link-alt ml-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div
                                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-user"></i>
                                                <span>{{ __('ui.channel_edit_reported_by') }} {{ $report->reporter->name }}</span>
                                            </div>
                                            <a href="{{ route('creator.reports') }}"
                                                class="text-red-600 dark:text-red-400 hover:underline">
                                                {{ __('ui.channel_edit_view_details') }} <i class="fas fa-arrow-right ml-1"></i>
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
                                        {{ __('ui.channel_edit_view_all_reports', ['count' => $totalReports]) }}
                                    </a>
                                </div>
                            @endif
                        @else
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                                <i class="fas fa-shield-alt text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    {{ __('ui.channel_edit_no_active_reports') }}
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ __('ui.no_active_reports') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- {{ __('ui.feedback') }} -->
                    <div id="feedback" class="report-tab-content hidden">
                        @if ($creator->count() > 0)
                            <div class="space-y-4">
                                @foreach ($creator as $feedback)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ !$feedback->is_read ? 'border-l-4 border-l-blue-500' : '' }}">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                    <i
                                                        class="fas fa-comment-dots text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                                        {{ $feedback->title }}
                                                    </h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ __('ui.channel_edit_from') }} {{ $feedback->admin->name }} •
                                                        {{ $feedback->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if (!$feedback->is_read)
                                                    <span
                                                        class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                                        {{ __('ui.channel_edit_new') }}
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
                                                {{ __('ui.channel_edit_related_report') }} #{{ $feedback->report->id }}
                                            </div>
                                        @endif

                                        <div
                                            class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                @if ($feedback->is_read)
                                                    <i class="fas fa-check mr-1"></i>
                                                    {{ __('ui.channel_edit_read_on') }}
                                                    {{ $feedback->read_at->format('d/m/Y H:i') }}
                                                @else
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ __('ui.channel_edit_unread') }}
                                                @endif
                                            </div>
                                            @if (!$feedback->is_read)
                                                <button onclick="mark{{ __('ui.feedback') }}AsRead({{ $feedback->id }})"
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-check mr-2"></i>{{ __('ui.channel_edit_mark_read') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($unread > 0)
                                <div class="mt-6 text-center">
                                    <button onclick="markAll{{ __('ui.feedback') }}AsRead()"
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        <i class="fas fa-check-double mr-2"></i>
                                        {{ __('ui.channel_edit_mark_all_read', ['count' => $unread]) }}
                                    </button>
                                </div>
                            @endif
                        @else
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                                <i class="fas fa-comment-dots text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    {{ __('ui.channel_edit_no_feedback') }}
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ __('ui.no_reports') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Personalizzazione -->
                <div id="customization-content"
                    class="content-section {{ request()->query('tab') == 'customization' ? 'block' : 'hidden' }}">
                    <form id="channelCustomizationForm" method="POST"
                        action="{{ route('channel.update-profile') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Cover Banner -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ __('ui.channel_edit_cover_image') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('ui.channel_edit_cover_image_subtitle') }}
                                </p>
                            </div>
                            <div class="p-6">
                                <div
                                    class="h-48 bg-gradient-to-r from-slate-700 to-slate-800 rounded-lg overflow-hidden relative group">
                                    @if ($userProfile && $userProfile->banner_url)
                                        <img src="{{ Storage::url($userProfile->banner_url) }}" alt="{{ __('ui.cover') }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                                            <div class="text-center text-slate-400">
                                                <svg class="w-12 h-12 mx-auto mb-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <p class="text-sm font-medium">{{ __('ui.channel_edit_channel_cover') }}</p>
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
                                            <span>
                                                {{ $userProfile && $userProfile->banner_url ? __('ui.channel_edit_change_cover') : __('ui.channel_edit_upload_cover') }}
                                            </span>
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
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ __('ui.channel_edit_profile_info') }}
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
                                                        alt="{{ __('ui.avatar') }}" class="w-full h-full object-cover">
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
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('ui.channel_name') }}
                                            </label>
                                            <input type="text" name="channel_name"
                                                value="{{ old('channel_name', $userProfile->channel_name ?? '') }}"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('ui.channel_edit_url_preview') }}
                                            </label>
                                            <div class="flex items-center">
                                                <span class="text-gray-500 dark:text-gray-400 mr-2">globio.com/</span>
                                                <div class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-mono"
                                                    id="url-preview">
                                                    {{ $userProfile->channel_name ?? __('ui.channel_edit_url_placeholder') }}
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between mt-2">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    {{ __('ui.channel_edit_url_updates') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('ui.username') }}
                                            </label>
                                            <div class="flex items-center">
                                                <span class="text-gray-500 dark:text-gray-400 mr-2">@</span>
                                                <input type="text" name="username"
                                                    value="{{ old('username', $userProfile->username ?? $user->name) }}"
                                                    placeholder="{{ __('ui.username') }}"
                                                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ __('ui.channel_edit_username_help') }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('ui.channel_description') }}
                                            </label>
                                            <textarea name="channel_description" rows="4"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm outline-none focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none">{{ old('channel_description', $userProfile->channel_description ?? '') }}</textarea>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ __('ui.channel_edit_max_chars', ['count' => 500]) }}
                                            </p>
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
                                {{ __('ui.channel_edit_save_changes') }}
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('ui.upload_new_video') }}
                    </h3>
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
                            {{ __('ui.channel_edit_upload_guidelines') }}
                        </h4>
                        <div class="grid md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <h5 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                    {{ __('ui.channel_edit_supported_formats') }}
                                </h5>
                                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                                    <li class="flex items-center"><i
                                            class="fas fa-check-circle mr-2 text-green-500"></i>{{ __('ui.channel_edit_format_mp4') }}</li>
                                    <li class="flex items-center"><i
                                            class="fas fa-check-circle mr-2 text-green-500"></i>{{ __('ui.channel_edit_format_avi') }}</li>
                                    <li class="flex items-center"><i
                                            class="fas fa-check-circle mr-2 text-green-500"></i>{{ __('ui.channel_edit_format_flv') }}</li>
                                    <li class="flex items-center"><i
                                            class="fas fa-check-circle mr-2 text-green-500"></i>{{ __('ui.channel_edit_max_size') }}
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                    {{ __('ui.channel_edit_best_practices') }}
                                </h5>
                                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                                    <li class="flex items-center"><i
                                            class="fas fa-lightbulb mr-2 text-yellow-500"></i>{{ __('ui.channel_edit_tip_title') }}
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-lightbulb mr-2 text-yellow-500"></i>{{ __('ui.channel_edit_tip_thumbnail') }}
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-lightbulb mr-2 text-yellow-500"></i>{{ __('ui.channel_edit_tip_tags') }}</li>
                                    <li class="flex items-center"><i
                                            class="fas fa-lightbulb mr-2 text-yellow-500"></i>{{ __('ui.channel_edit_tip_original') }}</li>
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

            const channelEditI18n = {
                selectedOne: @json(__('ui.channel_edit_selected_one')),
                selectedMany: @json(__('ui.channel_edit_selected_many', ['count' => ':count'])),
                confirmPublic: @json(__('ui.channel_edit_confirm_public', ['count' => ':count'])),
                noticePublic: @json(__('ui.channel_edit_public_notice', ['count' => ':count'])),
                confirmPrivate: @json(__('ui.channel_edit_confirm_private', ['count' => ':count'])),
                noticePrivate: @json(__('ui.channel_edit_private_notice', ['count' => ':count'])),
                confirmDeleteMany: @json(__('ui.channel_edit_confirm_delete_many', ['count' => ':count'])),
                deleteNotice: @json(__('ui.channel_edit_delete_notice', ['count' => ':count'])),
                operationError: @json(__('ui.channel_edit_operation_error')),
                connectionError: @json(__('ui.channel_edit_connection_error')),
                visibilityError: @json(__('ui.channel_edit_visibility_error')),
                confirmDeleteOne: @json(__('ui.channel_edit_confirm_delete_one')),
                deleteError: @json(__('ui.channel_edit_delete_error')),
                loadingPlaylists: @json(__('ui.channel_edit_loading_playlists')),
                noPlaylists: @json(__('ui.channel_edit_no_playlists')),
                createPlaylist: @json(__('ui.channel_edit_create_playlist')),
                loadError: @json(__('ui.channel_edit_load_error')),
                addedToPlaylist: @json(__('ui.channel_edit_added_to_playlist')),
                addToPlaylistError: @json(__('ui.channel_edit_add_to_playlist_error')),
                removeAvatarConfirm: @json(__('ui.channel_edit_remove_avatar_confirm')),
                removeBannerConfirm: @json(__('ui.channel_edit_remove_cover_confirm')),
                changeCover: @json(__('ui.channel_edit_change_cover')),
                uploadCover: @json(__('ui.channel_edit_upload_cover')),
                channelCover: @json(__('ui.channel_edit_channel_cover')),
                cover: @json(__('ui.cover')),
                urlPlaceholder: @json(__('ui.channel_edit_url_placeholder')),
                channelNameRules: @json(__('ui.channel_edit_channel_name_rules')),
                csrfNotFound: @json(__('ui.channel_edit_csrf_not_found'))
            };

            function formatChannelEdit(str, vars = {}) {
                return Object.keys(vars).reduce((result, key) => {
                    return result.replace(new RegExp(`:${key}`, 'g'), vars[key]);
                }, str);
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

                    // {{ __('ui.feedback') }} visivo discreto
                    if (selectedCount === 1) {
                        showNotification(channelEditI18n.selectedOne, 'info');
                    } else if (selectedCount <= 5) {
                        showNotification(formatChannelEdit(channelEditI18n.selectedMany, {
                            count: selectedCount
                        }), 'info');
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

                if (!confirm(formatChannelEdit(channelEditI18n.confirmPublic, {
                        count: selectedIds.length
                    }))) return;

                showNotification(formatChannelEdit(channelEditI18n.noticePublic, {
                    count: selectedIds.length
                }), 'info');

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
                        alert(channelEditI18n.operationError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
                }
            }

            async function bulkSetPrivate() {
                const selectedIds = getSelectedVideoIds();
                if (selectedIds.length === 0) return;

                if (!confirm(formatChannelEdit(channelEditI18n.confirmPrivate, {
                        count: selectedIds.length
                    }))) return;

                showNotification(formatChannelEdit(channelEditI18n.noticePrivate, {
                    count: selectedIds.length
                }), 'info');

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
                        alert(channelEditI18n.operationError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
                }
            }

            async function bulkDelete() {
                const selectedIds = getSelectedVideoIds();
                if (selectedIds.length === 0) return;

                if (!confirm(formatChannelEdit(channelEditI18n.confirmDeleteMany, {
                        count: selectedIds.length
                    })))
                    return;

                showNotification(formatChannelEdit(channelEditI18n.deleteNotice, {
                    count: selectedIds.length
                }), 'info');

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
                        alert(channelEditI18n.operationError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
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
                        alert(channelEditI18n.operationError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
                }
            }

            async function setVideoVisibility(selectEl) {
                const videoId = selectEl.dataset.videoId;
                const videoKey = selectEl.dataset.videoKey || videoId;
                const current = selectEl.dataset.current;
                const next = selectEl.value;

                if (current === next) {
                    return;
                }

                selectEl.disabled = true;
                try {
                    const response = await fetch(`/videos/${encodeURIComponent(videoKey)}/toggle-privacy`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json().catch(() => null);

                    if (response.ok && data && data.success) {
                        const isPublic = !!data.is_public;
                        const value = isPublic ? 'public' : 'private';
                        selectEl.value = value;
                        selectEl.dataset.current = value;
                    } else {
                        selectEl.value = current;
                        const message = data?.message || channelEditI18n.visibilityError;
                        alert(message);
                    }
                } catch (error) {
                    selectEl.value = current;
                    alert(channelEditI18n.connectionError);
                } finally {
                    selectEl.disabled = false;
                }
            }

            function bindVisibilitySelects() {
                document.querySelectorAll('.visibility-select').forEach(selectEl => {
                    selectEl.addEventListener('change', function() {
                        setVideoVisibility(selectEl);
                    });
                });
            }

            async function deleteVideo(videoKey) {
                if (!confirm(channelEditI18n.confirmDeleteOne)) return;

                try {
                    const response = await fetch(`/videos/${encodeURIComponent(videoKey)}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (response.ok) {
                        location.reload();
                    } else {
                        alert(channelEditI18n.deleteError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
                }
            }

            let playlistPickerVideoId = null;

            async function openPlaylistPicker(videoId) {
                playlistPickerVideoId = videoId;
                const modal = document.getElementById('playlist-picker');
                const content = document.getElementById('playlist-picker-content');
                if (!modal || !content) return;

                modal.classList.remove('hidden');
                content.innerHTML =
                    `<div class="text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>${channelEditI18n.loadingPlaylists}</div>`;

                try {
                    const response = await fetch('{{ route('playlists.data') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (!data.success || !data.playlists || data.playlists.length === 0) {
                        content.innerHTML = `
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <p class="text-sm font-medium">${channelEditI18n.noPlaylists}</p>
                            <a href="{{ route('playlists') }}" class="inline-block mt-3 text-sm text-red-600 hover:text-red-700">${channelEditI18n.createPlaylist}</a>
                        </div>
                    `;
                        return;
                    }

                    content.innerHTML = data.playlists.map(playlist => `
                    <button type="button" onclick="addVideoToPlaylist(${playlist.id})"
                        class="w-full text-left px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors mb-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">${playlist.title}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${playlist.videos_count || 0} {{ __('ui.video_short') }}</p>
                            </div>
                            <i class="fas fa-plus text-gray-400"></i>
                        </div>
                    </button>
                `).join('');
                } catch (error) {
                    content.innerHTML =
                        `<div class="text-center text-gray-500 dark:text-gray-400">${channelEditI18n.loadError}</div>`;
                }
            }

            function closePlaylistPicker() {
                const modal = document.getElementById('playlist-picker');
                if (modal) {
                    modal.classList.add('hidden');
                }
                playlistPickerVideoId = null;
            }

            async function addVideoToPlaylist(playlistId) {
                if (!playlistPickerVideoId) return;

                try {
                    const response = await fetch(`/playlists/${playlistId}/videos`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            video_id: playlistPickerVideoId
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        closePlaylistPicker();
                        alert(channelEditI18n.addedToPlaylist);
                    } else {
                        alert(data.message || channelEditI18n.addToPlaylistError);
                    }
                } catch (error) {
                    alert(channelEditI18n.connectionError);
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
                            <img src="${event.target.result}" alt="${channelEditI18n.cover}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>${channelEditI18n.changeCover}</span>
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
                if (confirm(channelEditI18n.removeAvatarConfirm)) {
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
                if (confirm(channelEditI18n.removeBannerConfirm)) {
                    const bannerContainer = document.querySelector('.h-48');
                    if (bannerContainer) {
                        bannerContainer.innerHTML = `
                        <div class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                            <div class="text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">${channelEditI18n.channelCover}</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>${channelEditI18n.uploadCover}</span>
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
                    preview.textContent = channelName || channelEditI18n.urlPlaceholder;
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
                    show{{ __('ui.feedback') }}('{{ __('ui.channel_name_valid') }}', 'success');
                } else {
                    input.classList.add('border-red-500');
                    let message = channelEditI18n.channelNameRules;
                    if (channelName.length < 3) {
                        message = '{{ __('ui.channel_name_min') }}';
                    } else if (channelName.length > 50) {
                        message = '{{ __('ui.channel_name_max') }}';
                    }
                    show{{ __('ui.feedback') }}(message, 'error');
                }
            }

            function show{{ __('ui.feedback') }}(message, type) {
                const input = document.querySelector('input[name="channel_name"]');
                const existing{{ __('ui.feedback') }} = document.getElementById('channel-name-feedback');

                if (existing{{ __('ui.feedback') }}) {
                    existing{{ __('ui.feedback') }}.remove();
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
                    {{ __('ui.saving') }}
                </div>
            `;
                submitButton.classList.add('opacity-75');

                try {
                    // Get CSRF token with fallback
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        throw new Error(channelEditI18n.csrfNotFound);
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
                            showNotification(result.message || '{{ __('ui.error') }}', 'error');
                        } else {
                            // Handle non-JSON responses (like 419 CSRF errors)
                            const text = await response.text();
                            if (response.status === 419) {
                                showNotification('{{ __('ui.session_expired') }}', 'error');
                            } else {
                                showNotification(`{{ __('ui.error') }} (${response.status}): ${text.substring(0, 100)}`,
                                    'error');
                            }
                        }
                        return;
                    }

                    const result = await response.json();

                    if (response.ok) {
                        // Success
                        showNotification('{{ __('ui.channel_updated_success') }}', 'success');

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
                    console.error('{{ __('ui.error') }}:', error);

                    let errorMessage = '{{ __('ui.connection_retry') }}';
                    if (error.message.includes('fetch')) {
                        errorMessage = '{{ __('ui.connection_check_internet') }}';
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
            window.mark{{ __('ui.feedback') }}AsRead = function(feedbackId) {
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

            window.markAll{{ __('ui.feedback') }}AsRead = function() {
                if (confirm('{{ __('ui.confirm_mark_all_read') }}')) {
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
                                label: '{{ __('ui.views_metric') }}',
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

            document.addEventListener('DOMContentLoaded', function() {
                bindVisibilitySelects();
            });
        </script>
</x-studio-layout>
