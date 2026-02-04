<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::getValue('site_name') }} - Studio</title>
    <meta name="description" content="{{ \App\Models\Setting::getValue('site_name') }} - Studio di Creazione Contenuti">
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

    <!-- Studio Header -->
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
                                class="text-red-600 dark:text-red-400">Studio</span>
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Centro di Creazione</p>
                    </div>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-xl mx-8">
                <div class="relative">
                    <input type="text" id="channel-search" placeholder="Cerca nel tuo canale..."
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
                <div id="search-results"
                    class="hidden absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto">
                    <!-- Results will be populated here -->
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center space-x-4">
                <!-- Quick Actions -->
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                        title="Carica video">
                        <i class="fas fa-upload text-gray-600 dark:text-gray-400"></i>
                    </a>
                    <a href="{{ route('channel.show', Auth::user()->userProfile && Auth::user()->userProfile->channel_name ? Auth::user()->userProfile->channel_name : (Auth::user()->userProfile ? Auth::user()->userProfile->id : Auth::user()->user->id)) }}"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                        title="Visualizza canale">
                        <i class="fas fa-external-link-alt text-gray-600 dark:text-gray-400"></i>
                    </a>
                </div>

                <!-- Notifications Bell -->
                <div class="relative">

                </div>

                <!-- Toggle theme -->
                <button onclick="toggleTheme()"
                    class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                    title="Cambia tema">
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
                                Creator
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
                                <i class="fas fa-user w-4 mr-3"></i> Profilo
                            </a>

                            <a href="{{ route('channel.show', Auth::user()->userProfile && Auth::user()->userProfile->channel_name ? Auth::user()->userProfile->channel_name : (Auth::user()->userProfile ? Auth::user()->userProfile->id : Auth::user()->user->id)) }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-eye w-4 mr-3"></i> Il mio canale
                            </a>

                            <a href="{{ route('home') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-home w-4 mr-3"></i> Torna al sito
                            </a>

                            <hr class="my-2 border-gray-200 dark:border-gray-600">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt w-4 mr-3"></i> Esci
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
                                    {{ $title ?? 'Studio' }}
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

        // Search functionality
        let searchTimeout;
        const searchInput = document.getElementById('channel-search');
        const searchResults = document.getElementById('search-results');

        function performSearch(query) {
            if (query.length < 2) {
                hideSearchResults();
                return;
            }

            fetch(`/api/search?q=${encodeURIComponent(query)}&scope=channel`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data.results || []);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    hideSearchResults();
                });
        }

        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-search text-2xl mb-2"></i>
                        <p>Nessun risultato trovato</p>
                    </div>
                `;
            } else {
                searchResults.innerHTML = results.map(result => `
                    <a href="${result.url}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden flex-shrink-0">
                                ${result.thumbnail ? `<img src="${result.thumbnail}" alt="" class="w-full h-full object-cover">` : '<i class="fas fa-file-video text-gray-400 m-2"></i>'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${result.title}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${result.type} • ${result.date}</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </div>
                    </a>
                `).join('');
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
                searchTimeout = setTimeout(() => performSearch(e.target.value), 300);
            });

            searchInput.addEventListener('focus', function(e) {
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
                    searchInput.focus();
                    searchInput.select();
                }
                if (e.key === 'Escape') {
                    hideSearchResults();
                    searchInput.blur();
                }
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
