<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm">
    <!-- Desktop Layout (lg and above) -->
    <div class="hidden lg:flex items-center justify-between px-4 h-16">
        <!-- Left Section: Logo -->
        <div class="flex items-center space-x-4">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-2xl font-bold">
                @if (\App\Models\Setting::getValue('logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}"
                        class="w-8 h-8 rounded-lg object-contain" alt="{{ \App\Models\Setting::getValue('site_name') }}">
                @else
                    <div class="w-8 h-8 bg-gradient-to-br rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(to bottom right, var(--primary-color), var(--primary-color-dark));">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                @endif
                <span class="text-gray-800 dark:text-white">
                    {{ \App\Models\Setting::getValue('site_name') }}
                </span>
            </a>
        </div>

        <!-- Center Section: Search Bar -->
        <div class="flex-1 max-w-2xl mx-4">
            <div class="relative">
                <form action="{{ route('search') }}" method="GET" class="flex">
                    <div class="relative flex-1">
                        <input type="text" name="q" placeholder="{{ __('ui.search_placeholder') }}"
                            class="w-full h-full px-4 pl-10 text-base bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-l-full focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                            style="--tw-ring-color: var(--primary-color);">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-600 rounded-r-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Section: Actions -->
        <div class="flex items-center space-x-2">
            @auth
                <!-- Upload Video -->
                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                    class="flex items-center px-4 py-2 text-white rounded-lg hover:opacity-90 transition-all no-underline"
                    style="background-color: var(--primary-color);">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="font-medium">{{ __('ui.upload') }}</span>
                </a>

                <!-- Notifications -->
                <div class="relative">
                    <livewire:notifications-bell />
                </div>

                <!-- User Menu -->
                <div class="relative" id="user-menu">
                    <button onclick="toggleUserMenu()"
                        class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" alt="Avatar"
                                class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div id="user-dropdown"
                        class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 opacity-0 scale-95 transform translate-y-2 pointer-events-none transition-all duration-200">
                        <!-- User Info -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                                    <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                                        alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-lg font-medium">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        <span>@</span>{{ Auth::user()->userProfile->username }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('channel.show', Auth::user()->userProfile?->channel_name) }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.my_channel') }}</span>
                            </a>
                            <a href="{{ route('videos.my') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-video w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.my_videos') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <a href="{{ route('users.profile') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-cog w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.settings') }}</span>
                            </a>
                            <a href="{{ route('history') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-history w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.history') }}</span>
                            </a>
                            <a href="{{ route('liked-videos') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-thumbs-up w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.liked_videos') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-dashboard w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.admin') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <button onclick="toggleTheme()"
                                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left">
                                <div class="relative w-5 h-5 flex items-center justify-center">
                                    <i id="dropdown-moon-icon"
                                        class="fas fa-moon absolute text-gray-500 dark:text-gray-400 theme-icon moon dark:hidden"></i>
                                    <i id="dropdown-sun-icon"
                                        class="fas fa-sun absolute text-gray-500 dark:text-gray-400 theme-icon sun hidden dark:block"></i>
                                </div>
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="dark:hidden">{{ __('ui.theme_dark') }}</span>
                                    <span class="hidden dark:inline">{{ __('ui.theme_light') }}</span>
                                </span>
                            </button>

                            <!-- Language Selector as dropdown -->
                            <div class="relative" x-data="{ langOpen: false }">
                                <button @click="langOpen = !langOpen"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                        </path>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        @switch(app()->getLocale())
                                            @case('it')
                                                🇮🇹 Italiano
                                            @break

                                            @case('en')
                                                🇬🇧 English
                                            @break

                                            @case('es')
                                                🇪🇸 Español
                                            @break

                                            @default
                                                🇮🇹 Italiano
                                        @endswitch
                                    </span>
                                    <i class="fas fa-chevron-right ml-auto text-xs text-gray-400"
                                        :class="{ 'rotate-90': langOpen }" x-show="!langOpen"></i>
                                </button>

                                <!-- Language Submenu -->
                                <div x-show="langOpen" x-transition
                                    class="pl-4 border-l border-gray-200 dark:border-gray-700 my-1">
                                    <a href="{{ route('setLocale', 'it') }}"
                                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() === 'it' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        <span class="text-base">🇮🇹</span>
                                        <span class="font-medium">Italiano</span>
                                        @if (app()->getLocale() === 'it')
                                            <i class="fas fa-check ml-auto text-blue-500"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('setLocale', 'en') }}"
                                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() === 'en' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        <span class="text-base">🇬🇧</span>
                                        <span class="font-medium">English</span>
                                        @if (app()->getLocale() === 'en')
                                            <i class="fas fa-check ml-auto text-blue-500"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('setLocale', 'es') }}"
                                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() === 'es' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        <span class="text-base">🇪🇸</span>
                                        <span class="font-medium">Español</span>
                                        @if (app()->getLocale() === 'es')
                                            <i class="fas fa-check ml-auto text-blue-500"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left"
                                    style="color: var(--primary-color);">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>{{ __('ui.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Login/Register -->
                <div class="flex items-center space-x-2">
                    <!-- Language Selector for guests -->
                    <div class="relative mr-2" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen"
                            class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                </path>
                            </svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false"
                            class="absolute right-0 top-full mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <a href="{{ route('setLocale', 'it') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'it' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇮🇹</span> Italiano
                            </a>
                            <a href="{{ route('setLocale', 'en') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇬🇧</span> English
                            </a>
                            <a href="{{ route('setLocale', 'es') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'es' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇪🇸</span> Español
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>{{ __('ui.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors"
                        style="background-color: var(--primary-color);">
                        <i class="fas fa-user-plus mr-2"></i>{{ __('ui.register') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Mobile Layout (below lg) -->
    <div class="lg:hidden flex items-center justify-between px-4 h-16">
        <!-- Left Section: Logo -->
        <div class="flex items-center">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-xl font-bold">
                @if (\App\Models\Setting::getValue('logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}"
                        class="w-8 h-8 rounded-lg object-contain"
                        alt="{{ \App\Models\Setting::getValue('site_name') }}">
                @else
                    <div class="w-8 h-8 bg-gradient-to-br rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(to bottom right, var(--primary-color), var(--primary-color-dark));">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                @endif
            </a>
        </div>

        <!-- Right Section: Search and Notifications -->
        <div class="flex items-center space-x-2">
            @auth
                <!-- Search Button -->
                <button onclick="toggleSearchModal()"
                    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors touch-feedback">
                    <i class="fas fa-search text-gray-600 dark:text-gray-300"></i>
                </button>

                <!-- Notifications -->
                <div class="relative">
                    <livewire:notifications-bell />
                </div>

                <!-- User Avatar -->
                <div class="relative" id="user-menu-mobile">
                    <button onclick="toggleUserMenuMobile()"
                        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors touch-feedback">
                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" alt="Avatar"
                                class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </button>

                    <!-- Mobile User Dropdown -->
                    <div id="user-dropdown-mobile"
                        class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 opacity-0 scale-95 transform translate-y-2 pointer-events-none transition-all duration-200">
                        <!-- User Info -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                                    <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                                        alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-lg font-medium">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        <span>@</span>{{ Auth::user()->userProfile->username }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('channel.show', Auth::user()->userProfile?->channel_name) }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.my_channel') }}</span>
                            </a>
                            <a href="{{ route('videos.my') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-video w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.my_videos') }}</span>
                            </a>
                            <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-plus w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.upload_video') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <a href="{{ route('users.profile') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-cog w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.settings') }}</span>
                            </a>
                            <a href="{{ route('history') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-history w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.history') }}</span>
                            </a>
                            <a href="{{ route('liked-videos') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-thumbs-up w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.liked_videos') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-dashboard w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('ui.admin') }}</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <button onclick="toggleTheme()"
                                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left">
                                <div class="relative w-5 h-5 flex items-center justify-center">
                                    <i
                                        class="fas fa-moon absolute text-gray-500 dark:text-gray-400 theme-icon moon dark:hidden"></i>
                                    <i
                                        class="fas fa-sun absolute text-gray-500 dark:text-gray-400 theme-icon sun hidden dark:block"></i>
                                </div>
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="dark:hidden">{{ __('ui.theme_dark') }}</span>
                                    <span class="hidden dark:inline">{{ __('ui.theme_light') }}</span>
                                </span>
                            </button>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left"
                                    style="color: var(--primary-color);">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>{{ __('ui.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Login/Register for Mobile -->
                <div class="flex items-center space-x-2">
                    <!-- Language Selector for guests -->
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen"
                            class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                </path>
                            </svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false"
                            class="absolute right-10 top-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <a href="{{ route('setLocale', 'it') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'it' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇮🇹</span> Italiano
                            </a>
                            <a href="{{ route('setLocale', 'en') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇬🇧</span> English
                            </a>
                            <a href="{{ route('setLocale', 'es') }}"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'es' ? 'text-blue-500' : 'text-gray-700 dark:text-gray-300' }}">
                                <span>🇪🇸</span> Español
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('login') }}"
                        class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors text-sm">
                        Accedi
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-3 py-2 text-white rounded-lg hover:opacity-90 transition-colors text-sm"
                        style="background-color: var(--primary-color);">
                        Registrati
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>

<!-- Search Modal for Mobile -->
<div id="search-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="toggleSearchModal()"></div>
    <div class="relative z-10 p-4 pt-20">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl">
            <div class="p-4">
                <form action="{{ route('search') }}" method="GET" class="flex">
                    <div class="relative flex-1">
                        <input type="text" name="q" placeholder="Cerca video, canali..." autofocus
                            class="w-full px-4 py-3 pl-10 text-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-l-full focus:outline-none focus:ring-2 focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color); font-size: 16px;">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-600 rounded-r-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // State management
    let userMenuOpen = false;
    let userMenuMobileOpen = false;
    let searchModalOpen = false;

    // Desktop user menu
    function toggleUserMenu() {
        const dropdown = document.getElementById('user-dropdown');
        userMenuOpen = !userMenuOpen;

        if (userMenuOpen) {
            dropdown.classList.remove('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            dropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
        } else {
            dropdown.classList.add('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
        }
    }

    // Mobile user menu
    function toggleUserMenuMobile() {
        const dropdown = document.getElementById('user-dropdown-mobile');
        userMenuMobileOpen = !userMenuMobileOpen;

        if (userMenuMobileOpen) {
            dropdown.classList.remove('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            dropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
        } else {
            dropdown.classList.add('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
        }
    }

    // Search modal for mobile
    function toggleSearchModal() {
        const modal = document.getElementById('search-modal');
        searchModalOpen = !searchModalOpen;

        if (searchModalOpen) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Focus on input after a short delay
            setTimeout(() => {
                const input = modal.querySelector('input[name="q"]');
                if (input) input.focus();
            }, 100);
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        // Desktop user menu
        const userMenu = document.getElementById('user-menu');
        const userDropdown = document.getElementById('user-dropdown');
        if (userMenu && !userMenu.contains(event.target) && userMenuOpen) {
            userMenuOpen = false;
            userDropdown.classList.add('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            userDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
        }

        // Mobile user menu
        const userMenuMobile = document.getElementById('user-menu-mobile');
        const userDropdownMobile = document.getElementById('user-dropdown-mobile');
        if (userMenuMobile && !userMenuMobile.contains(event.target) && userMenuMobileOpen) {
            userMenuMobileOpen = false;
            userDropdownMobile.classList.add('opacity-0', 'scale-95', 'translate-y-2', 'pointer-events-none');
            userDropdownMobile.classList.remove('opacity-100', 'scale-100', 'translate-y-0',
                'pointer-events-auto');
        }

        // Search modal
        const searchModal = document.getElementById('search-modal');
        if (searchModal && !searchModal.contains(event.target) && searchModalOpen && !event.target.closest(
                'button[onclick="toggleSearchModal()"]')) {
            toggleSearchModal();
        }
    });

    // Close search modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && searchModalOpen) {
            toggleSearchModal();
        }
    });

    // Theme toggle
    function toggleTheme() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }

        updateThemeIcons();
    }

    function updateThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');

        // Update all theme icons
        const moonIcons = document.querySelectorAll('.theme-icon.moon');
        const sunIcons = document.querySelectorAll('.theme-icon.sun');

        moonIcons.forEach(icon => {
            if (isDark) {
                icon.classList.add('hidden');
            } else {
                icon.classList.remove('hidden');
            }
        });

        sunIcons.forEach(icon => {
            if (isDark) {
                icon.classList.remove('hidden');
            } else {
                icon.classList.add('hidden');
            }
        });
    }

    // Initialize theme
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }
        updateThemeIcons();
    });
</script>
