<!-- Desktop Sidebar -->
<aside class="hidden lg:block w-64 bg-white dark:bg-gray-900 h-[calc(100vh-4rem)] sticky top-16 overflow-y-auto">
    <div class="p-4">
        <!-- Main Navigation -->
        <nav class="space-y-2">
            <a href="{{ route('home') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('home') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                <i class="fas fa-home w-4 h-4"></i>
                <span class="font-medium">{{ __('ui.home') }}</span>
            </a>

            <a href="{{ route('reels.index') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('reels.index*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                <i class="fa-solid fa-circle-play w-4 h-4"></i>
                <span class="font-medium">{{ __('ui.reels') }}</span>
            </a>

            <a href="{{ route('explore') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('explore') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                <i class="fas fa-compass w-4 h-4"></i>
                <span class="font-medium">{{ __('ui.explore') }}</span>
            </a>

            @auth
                <a href="{{ route('subscriptions') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('subscriptions') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                    <i class="fas fa-heart w-4 h-4"></i>
                    <span class="font-medium">{{ __('ui.subscriptions') }}</span>
                </a>
            @endauth

            <hr class="my-3 h-0.5 border-t-0 bg-neutral-100 dark:bg-white/10" />
        </nav>

        @auth
            <!-- My Library -->
            <div class="mt-4">
                <h3 class="px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('ui.library') }}
                </h3>
                <nav class="mt-2 space-y-1">
                    <a href="{{ route('history') }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('history') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-history w-4 h-4"></i>
                        <span>{{ __('ui.history') }}</span>
                    </a>

                    <a href="{{ route('playlists') }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('playlists') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-list w-4 h-4"></i>
                        <span>{{ __('ui.playlists') }}</span>
                    </a>

                    <a href="{{ route('liked-videos') }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('liked-videos') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-thumbs-up w-4 h-4"></i>
                        <span>{{ __('ui.liked_videos') }}</span>
                    </a>

                    <a href="{{ route('watch-later') }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('watch-later') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-clock w-4 h-4"></i>
                        <span>{{ __('ui.watch_later') }}</span>
                    </a>
                </nav>

                <hr class="my-3 h-0.5 border-t-0 bg-neutral-100 dark:bg-white/10" />
            </div>

            <!-- Subscriptions -->
            @auth
                @php
                    $subscriptions = auth()->user()->subscriptions()->with('userProfile')->limit(10)->get();
                @endphp

                @if ($subscriptions->count() > 0)
                    <div class="mt-4">
                        <h3 class="px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('ui.subscriptions') }}
                        </h3>
                        <nav class="mt-2 space-y-1">
                            @foreach ($subscriptions as $subscription)
                                @php
                                    $channelProfile = $subscription->userProfile;
                                    $channelName = $channelProfile
                                        ? $channelProfile->channel_name
                                        : $subscription->name;
                                @endphp
                                <a href="{{ route('channel.show', $channelName) }}"
                                    class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    @if ($channelProfile && $channelProfile->avatar_url)
                                        <img src="{{ asset('storage/' . $channelProfile->avatar_url) }}"
                                            alt="{{ $subscription->name }}" class="w-6 h-6 rounded-full object-cover">
                                    @else
                                        <div
                                            class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">
                                                {{ strtoupper(substr($subscription->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <span class="truncate">{{ $subscription->name }}</span>
                                </a>
                            @endforeach

                            @if (auth()->user()->subscriptions()->count() > 10)
                                <a href="{{ route('subscriptions') }}"
                                    class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-red-600 dark:text-red-400">
                                    <i class="fas fa-ellipsis-h w-4 h-4"></i>
                                    <span class="font-medium">{{ __('ui.show_more') }}</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                @endif
            @endauth

            <!-- My Channel -->
            <div class="mt-4">
                <h3 class="px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('ui.my_channel') }}
                </h3>
                <nav class="mt-2 space-y-1">
                    <a href="{{ route('channel.show', auth()->user()->userProfile->channel_name) }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('channel.show') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-user w-4 h-4"></i>
                        <span>{{ __('ui.channel') }}</span>
                    </a>

                    <a href="{{ route('channel.edit', auth()->user()->userProfile->channel_name) }}?tab=content"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('videos.my') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-video w-4 h-4"></i>
                        <span>{{ __('ui.my_videos') }}</span>
                    </a>

                    <a href="{{ route('channel.edit', auth()->user()->userProfile->channel_name) }}?tab=analytics"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('channel.edit') && request()->query('tab') == 'analytics' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-chart-bar w-4 h-4"></i>
                        <span>{{ __('ui.analytics') }}</span>
                    </a>
                </nav>
            </div>

            <hr class="my-3 h-0.5 border-t-0 bg-neutral-100 dark:bg-white/10" />
        @endauth
    </div>

    <x-footer />
</aside>

<!-- Mobile Sidebar -->
<aside id="mobile-sidebar"
    class="fixed left-0 top-16 bottom-0 w-80 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 z-50 transform -translate-x-full lg:hidden transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="p-4">
        <!-- Same content as desktop sidebar but optimized for mobile -->
        <nav class="space-y-2">
            <a href="{{ route('home') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('home') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                <i class="fas fa-home w-4 h-4"></i>
                <span class="font-medium">Home</span>
            </a>
        </nav>

        @auth
            <!-- Subscriptions -->
            @php
                $subscriptions = auth()->user()->subscriptions()->with('userProfile')->limit(8)->get();
            @endphp

            @if ($subscriptions->count() > 0)
                <div class="mt-8">
                    <h3 class="px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Iscrizioni
                    </h3>
                    <nav class="mt-2 space-y-1">
                        @foreach ($subscriptions as $subscription)
                            @php
                                $channelProfile = $subscription->userProfile;
                                $channelName = $channelProfile ? $channelProfile->channel_name : $subscription->name;
                            @endphp
                            <a href="{{ route('channel.show', $channelName) }}"
                                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                @if ($channelProfile && $channelProfile->avatar_url)
                                    <img src="{{ asset('storage/' . $channelProfile->avatar_url) }}"
                                        alt="{{ $subscription->name }}" class="w-6 h-6 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">
                                            {{ strtoupper(substr($subscription->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="truncate">{{ $subscription->name }}</span>
                            </a>
                        @endforeach

                        <a href="{{ route('subscriptions') }}"
                            class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-red-600 dark:text-red-400">
                            <i class="fas fa-ellipsis-h w-4 h-4"></i>
                            <span class="font-medium">Mostra tutte</span>
                        </a>
                    </nav>
                </div>
            @endif

            <div class="mt-8">
                <h3 class="px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    La mia libreria
                </h3>
                <nav class="mt-2 space-y-1">
                    <a href="{{ route('history') }}"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('history') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}">
                        <i class="fas fa-history w-4 h-4"></i>
                        <span>Cronologia</span>
                    </a>
                </nav>
            </div>
        @endauth
    </div>
</aside>
