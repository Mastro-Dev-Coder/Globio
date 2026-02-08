<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::getValue('site_name') }} - {{ __('ui.studio') }}</title>
    <meta name="description"
        content="{{ \App\Models\Setting::getValue('site_name') }} - {{ __('ui.studio_description') }}">
    <meta name="keywords"
        content="studio, creazione, contenuti, video, {{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="author" content="{{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ route('dynamic.styles') }}">
</head>

<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-inter antialiased transition-colors duration-300">

    <!-- {{ __('ui.studio') }} Header -->
    <header
        class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 shadow-sm">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            <!-- Logo e titolo studio -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    @if (App\Models\Setting::getValue('logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}" class="w-8 h-8"
                            alt="{{ \App\Models\Setting::getValue('site_name') }}">
                    @else
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-play text-white text-sm"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Setting::getValue('site_name') }} <span
                                class="text-red-600 dark:text-red-400">{{ __('ui.studio') }}</span>
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('ui.studio_center') }}
                        </p>
                    </div>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-xl mx-8">
                <div class="relative">
                    <input type="text" id="channel-search" placeholder="{{ __('ui.studio_search_placeholder') }}"
                        class="w-full pl-12 pr-4 py-2.5 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <kbd
                            class="hidden lg:inline-flex items-center px-2 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 rounded">
                            ⌘K
                        </kbd>
                    </div>
                </div>
                <!-- Search Results Dropdown -->
                <div class="relative">
                    <div id="search-results"
                        class="hidden absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto">
                    </div>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center space-x-4">
                <!-- Quick Actions -->
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                        title="{{ __('ui.upload_video') }}">
                        <i class="fas fa-upload text-gray-600 dark:text-gray-400"></i>
                    </a>
                    <a href="{{ route('channel.show', Auth::user()->userProfile && Auth::user()->userProfile->channel_name ? Auth::user()->userProfile->channel_name : (Auth::user()->userProfile ? Auth::user()->userProfile->id : Auth::user()->user->id)) }}"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                        title="{{ __('ui.view_channel') }}">
                        <i class="fas fa-external-link-alt text-gray-600 dark:text-gray-400"></i>
                    </a>
                </div>

                <!-- Notifications Bell -->
                <div class="relative">

                </div>

                <!-- Toggle theme -->
                <button onclick="toggleTheme()"
                    class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                    title="{{ __('ui.theme') }}">
                    <i class="fas fa-sun text-yellow-500" id="sun-icon" style="display: none;"></i>
                    <i class="fas fa-moon text-gray-600 dark:text-gray-400" id="moon-icon"></i>
                </button>

                <!-- Breadcrumb -->
                @isset($breadcrumbs)
                    <nav class="hidden lg:flex items-center space-x-2 text-sm">
                        <a href="{{ route('home') }}"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-home"></i>
                        </a>
                        @foreach ($breadcrumbs as $breadcrumb)
                            <i class="fas fa-chevron-right text-gray-400"></i>
                            @if ($loop->last)
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $breadcrumb['name'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}"
                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    {{ $breadcrumb['name'] }}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                @endisset

                <!-- User menu -->
                <div class="relative" id="user-dropdown-container">
                    <button onclick="toggleUserDropdown()"
                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" alt="Avatar"
                                class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-24">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 capitalize">
                                {{ __('ui.creator') }}
                            </p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Dropdown -->
                    <div id="user-dropdown"
                        class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 opacity-0 scale-95 transition-all duration-200">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('users.profile') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user w-4 mr-3"></i> {{ __('ui.profile') }}
                            </a>

                            <a href="{{ route('channel.show', Auth::user()->userProfile && Auth::user()->userProfile->channel_name ? Auth::user()->userProfile->channel_name : (Auth::user()->userProfile ? Auth::user()->userProfile->id : Auth::user()->user->id)) }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-eye w-4 mr-3"></i> {{ __('ui.my_channel') }}
                            </a>

                            <a href="{{ route('home') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-home w-4 mr-3"></i> {{ __('ui.go_home') }}
                            </a>

                            <hr class="my-2 border-gray-200 dark:border-gray-600">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt w-4 mr-3"></i> {{ __('ui.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="pt-16">
        <!-- Page Header -->
        @isset($pageHeader)
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 lg:px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                @isset($pageHeader['title'])
                                    {{ $pageHeader['title'] }}
                                @else
                                    {{ $title ?? __('ui.studio') }}
                                @endisset
                            </h2>
                            @isset($pageHeader['subtitle'])
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $pageHeader['subtitle'] }}</p>
                            @endisset
                        </div>

                        @isset($pageHeader['actions'])
                            <div class="flex items-center space-x-3">
                                {{ $pageHeader['actions'] }}
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        @endisset

        <!-- Content -->
        <main class="max-w-full mx-auto px-4 lg:px-6 py-6">
            {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Custom Scripts -->
    <script>
        // Theme management
        function toggleTheme() {
            const html = document.documentElement;
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                sunIcon.style.display = 'inline';
                moonIcon.style.display = 'none';
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'inline';
            }
        }

        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            const html = document.documentElement;
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            if (savedTheme === 'light') {
                html.classList.remove('dark');
                sunIcon.style.display = 'inline';
                moonIcon.style.display = 'none';
            } else {
                html.classList.add('dark');
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'inline';
            }
        });

        // User dropdown toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => dropdown.classList.remove('opacity-0', 'scale-95'), 10);
            } else {
                dropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userContainer = document.getElementById('user-dropdown-container');
            const userDropdown = document.getElementById('user-dropdown');

            if (userContainer && !userContainer.contains(event.target)) {
                userDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            }
        });

        // {{ __('ui.studio') }} search functionality (local to the current studio page)
        let searchTimeout;
        const searchInput = document.getElementById('channel-search');
        const searchResults = document.getElementById('search-results');
        let studioSearchIndex = [];
        const studioTranslations = {
            studio_results: @json(__('ui.studio_results')),
            results_found: @json(__('ui.results_found')),
            search_results_for: @json(__('ui.search_results_for')),
            no_search_results: @json(__('ui.no_search_results')),
            use_specific_keywords: @json(__('ui.use_specific_keywords')),
        };

        function buildStudioSearchIndex() {
            const main = document.querySelector('main');
            if (!main) {
                studioSearchIndex = [];
                return;
            }

            const candidates = main.querySelectorAll('[data-studio-search], h2, h3, h4, label, legend');
            const seen = new Set();
            studioSearchIndex = [];

            candidates.forEach((el, idx) => {
                const raw = (el.getAttribute('data-studio-search') || el.textContent || '').trim();
                const text = raw.replace(/\s+/g, ' ');
                if (text.length < 3) return;

                const key = text.toLowerCase();
                if (seen.has(key)) return;
                seen.add(key);

                if (!el.id) {
                    const slug = key.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 32);
                    el.id = `studio-search-${idx}-${slug || 'item'}`;
                }

                const type = el.getAttribute('data-studio-search-type') || el.tagName.toLowerCase();
                studioSearchIndex.push({
                    id: el.id,
                    text,
                    type
                });
            });
        }

        function performSearch(query) {
            const trimmed = query.trim();
            if (trimmed.length < 2) {
                hideSearchResults();
                return;
            }

            const needle = trimmed.toLowerCase();
            const results = studioSearchIndex
                .filter(item => item.text.toLowerCase().includes(needle))
                .slice(0, 12);

            displaySearchResults(results, trimmed);
        }

        function escapeHtml(value) {
            return value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function escapeRegExp(value) {
            return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function formatResultType(type) {
            const map = {
                h2: '{{ __('ui.section') }}',
                h3: '{{ __('ui.subsection') }}',
                h4: '{{ __('ui.detail') }}',
                label: '{{ __('ui.field') }}',
                legend: '{{ __('ui.group') }}'
            };
            return map[type] || '{{ __('ui.item') }}';
        }

        function highlightMatch(text, query) {
            const safeText = escapeHtml(text);
            if (!query) return safeText;
            const re = new RegExp(escapeRegExp(query), 'ig');
            return safeText.replace(re, match => `
                <span class="text-red-600 dark:text-red-400 font-semibold">${match}</span>
            `.trim());
        }

        function displaySearchResults(results, query) {
            const safeQuery = escapeHtml(query || '');
            const header = `
                <div class="px-4 pt-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-search text-red-500"></i>
                            <span>${studioTranslations.studio_results}</span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">${results.length} ${studioTranslations.results_found}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        ${studioTranslations.search_results_for}: <span class="font-semibold text-gray-700 dark:text-gray-200">${safeQuery}</span>
                    </p>
                </div>
            `;

            if (results.length === 0) {
                searchResults.innerHTML = `
                    ${header}
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        <div class="mx-auto mb-3 h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <p class="text-sm font-medium">${studioTranslations.no_search_results.replace(':query', safeQuery)}</p>
                        <p class="text-xs mt-1">${studioTranslations.use_specific_keywords}</p>
                    </div>
                `;
            } else {
                const list = results.map(result => {
                    const label = highlightMatch(result.text, query);
                    const badge = formatResultType(result.type);
                    return `
                        <a href="#${result.id}" data-studio-target="${result.id}"
                            class="group block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-red-50 to-white dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-sliders-h text-red-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">${label}</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">${badge}</span>
                                        <span class="text-[11px] text-gray-400">?</span>
                                        <span class="text-[11px] text-gray-400">{{ __('ui.studio') }}</span>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-gray-400">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');

                searchResults.innerHTML = `
                    ${header}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        ${list}
                    </div>
                `;
            }
            searchResults.classList.remove('hidden');
        }

        function hideSearchResults() {
            searchResults.classList.add('hidden');
        }

        // Search input event listeners
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => performSearch(e.target.value), 200);
            });

            searchInput.addEventListener('focus', function(e) {
                build{{ __('ui.studio') }}SearchIndex();
                if (e.target.value.length >= 2) {
                    performSearch(e.target.value);
                }
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    hideSearchResults();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    build{{ __('ui.studio') }}SearchIndex();
                    searchInput.focus();
                    searchInput.select();
                }
                if (e.key === 'Escape') {
                    hideSearchResults();
                    searchInput.blur();
                }
            });
        }

        if (searchResults) {
            searchResults.addEventListener('click', function(e) {
                const link = e.target.closest('a[data-studio-target]');
                if (!link) return;
                e.preventDefault();

                const targetId = link.getAttribute('data-studio-target');
                const targetEl = document.getElementById(targetId);
                if (!targetEl) return;

                const hiddenSection = targetEl.closest('.content-section.hidden');
                if (hiddenSection && typeof window.switchMenu === 'function') {
                    const sectionId = hiddenSection.getAttribute('id') || '';
                    if (sectionId.endsWith('-content')) {
                        const menuName = sectionId.replace('-content', '');
                        window.switchMenu(menuName);
                    } else {
                        hiddenSection.classList.remove('hidden');
                    }
                }

                hideSearchResults();
                setTimeout(() => {
                    const y = targetEl.getBoundingClientRect().top + window.pageYOffset - 90;
                    window.scrollTo({
                        top: y,
                        behavior: 'smooth'
                    });
                }, hiddenSection ? 80 : 0);
                targetEl.classList.add(
                    'ring-2',
                    'ring-red-500',
                    'ring-offset-2',
                    'ring-offset-white',
                    'dark:ring-offset-gray-900',
                    'bg-yellow-50',
                    'dark:bg-yellow-900/20',
                    'transition-colors'
                );
                setTimeout(() => {
                    targetEl.classList.remove(
                        'ring-2',
                        'ring-red-500',
                        'ring-offset-2',
                        'ring-offset-white',
                        'dark:ring-offset-gray-900',
                        'bg-yellow-50',
                        'dark:bg-yellow-900/20',
                        'transition-colors'
                    );
                }, 2000);
            });
        }

        // Sistema di aggiornamento colori dinamici
        function updateDynamicColors() {
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
            const primaryColorLight = getComputedStyle(document.documentElement).getPropertyValue('--primary-color-light')
                .trim();
            const primaryColorDark = getComputedStyle(document.documentElement).getPropertyValue('--primary-color-dark')
                .trim();

            // Converti HEX in RGB per trasparenze
            const rgb = hexToRgb(primaryColor);
            const rgbLight = hexToRgb(primaryColorLight);
            const rgbDark = hexToRgb(primaryColorDark);

            // Aggiorna variabili CSS custom
            document.documentElement.style.setProperty('--primary-rgb', rgb);
            document.documentElement.style.setProperty('--primary-light-rgb', rgbLight);
            document.documentElement.style.setProperty('--primary-dark-rgb', rgbDark);
        }

        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) :
                '220, 38, 38';
        }

        // Inizializza colori dinamici
        updateDynamicColors();

        // Ricarica colori ogni volta che la pagina cambia (per SPA behavior)
        if (window.history && window.history.pushState) {
            const originalPushState = history.pushState;
            history.pushState = function() {
                originalPushState.apply(history, arguments);
                setTimeout(updateDynamicColors, 100);
            };
        }
    </script>
</body>

</html>
