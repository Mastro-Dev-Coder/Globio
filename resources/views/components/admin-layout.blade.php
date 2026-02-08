<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::getValue('site_name') }} - {{ __('ui.admin') }}</title>
    <meta name="description"
        content="{{ \App\Models\Setting::getValue('site_name') }} - {{ __('ui.admin_panel_description') }}">
    <meta name="keywords" content="admin, administration, {{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="author" content="{{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ route('dynamic.styles') }}">
</head>

<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-inter antialiased transition-colors duration-300">

    <!-- Admin Header -->
    <header
        class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 shadow-sm">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            <!-- Logo e titolo admin -->
            <div class="flex items-center space-x-4">
                <button onclick="toggleMobileMenu()"
                    class="lg:hidden text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex items-center space-x-3">
                    @if (\App\Models\Setting::getValue('logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}"
                            class="w-8 h-8 rounded-lg" alt="{{ \App\Models\Setting::getValue('site_name') }}">
                    @else
                        <div class="w-8 h-8 bg-gradient-to-br rounded-lg flex items-center justify-center shadow-md"
                            style="background: linear-gradient(to bottom right, var(--primary-color), var(--primary-color-dark));">
                            <i class="fas fa-shield-alt text-white text-sm"></i>
                        </div>
                    @endif

                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Setting::getValue('site_name') }}
                        </h1>
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">{{ __('ui.admin_panel') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions header -->
            <div class="flex items-center space-x-4">
                <!-- Search Bar (condensed) -->
                <div class="hidden lg:flex items-center">
                    <div class="relative">
                        <input type="text" placeholder="{{ __('ui.quick_search') }}..."
                            class="w-64 pl-10 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Language Switcher -->
                <div class="relative">
                    @livewire('language-switcher')
                </div>

                <!-- Notifications Bell -->
                <div class="relative">

                </div>

                <!-- Toggle theme -->
                <button onclick="toggleTheme()"
                    class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                    title="{{ __('ui.toggle_theme') }}">
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
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
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
                                {{ auth()->user()->role ?? 'user' }}
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

                            <a href="{{ route('admin.settings') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-cog w-4 mr-3"></i> {{ __('ui.settings') }}
                            </a>

                            <a href="{{ route('home') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-home w-4 mr-3"></i> {{ __('ui.back_to_site') }}
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
    <div class="flex min-h-screen pt-16">
        <!-- Admin Sidebar -->
        <x-admin-sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 transition-all duration-300 ease-in-out lg:ml-72">
            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6">
                <!-- Page Header -->
                @isset($pageHeader)
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    @isset($pageHeader['title'])
                                        {{ $pageHeader['title'] }}
                                    @else
                                        {{ $title ?? __('ui.admin_panel') }}
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
                @endisset

                <!-- Content -->
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"
        onclick="closeMobileMenu()"></div>

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

        // Mobile menu toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('admin-mobile-sidebar');
            const overlay = document.getElementById('mobile-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close mobile menu
        function closeMobileMenu() {
            const sidebar = document.getElementById('admin-mobile-sidebar');
            const overlay = document.getElementById('mobile-overlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
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

        // Admin Quick Actions Functions
        function clearCache() {
            if (confirm('{{ __('ui.confirm_clear_cache') }}')) {
                fetch('/admin/clear-cache', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('{{ __('ui.cache_cleared_success') }}');
                            location.reload();
                        } else {
                            alert('{{ __('ui.error_cache_clear') }}');
                        }
                    })
                    .catch(error => {
                        alert('{{ __('ui.error') }}: ' + error.message);
                    });
            }
        }

        function testFFmpeg() {
            // Redirect to FFmpeg settings where test function is available
            window.location.href = '{{ route('admin.settings.index') }}';
        }
    </script>
</body>

</html>
