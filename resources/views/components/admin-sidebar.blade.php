<!-- Desktop Admin Sidebar -->
<aside
    class="hidden lg:block fixed left-0 top-16 bottom-0 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hover:overflow-y-auto z-30">
    <div class="p-4">
        <!-- Admin Navigation -->
        <nav class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-tachometer-alt w-4 h-4"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Users Management -->
            <a href="{{ route('admin.users') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-users w-4 h-4"></i>
                <span class="font-medium">Utenti</span>
                <span
                    class="ml-auto bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">
                    {{ \App\Models\User::count() }}
                </span>
            </a>

            <!-- Videos Management -->
            <a href="{{ route('admin.videos-management') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.videos-management*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-video w-4 h-4"></i>
                <span class="font-medium">Video</span>
                <span
                    class="ml-auto bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">
                    {{ \App\Models\Video::count() }}
                </span>
            </a>

            <!-- Comments -->
            <a href="{{ route('admin.comments') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.comments') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-comments w-4 h-4"></i>
                <span class="font-medium">Commenti</span>
                <span
                    class="ml-auto bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs px-2 py-0.5 rounded-full">
                    {{ \App\Models\Comment::count() }}
                </span>
            </a>

            <!-- Statistics -->
            <a href="{{ route('admin.statistics') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.statistics') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-chart-bar w-4 h-4"></i>
                <span class="font-medium">Statistiche</span>
            </a>

            <!-- FFmpeg Settings -->
            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fa-solid fa-screwdriver-wrench w-4 h-4"></i>
                <span class="font-medium">Impostazioni FFmpeg</span>
                <div class="ml-auto flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full" title="FFmpeg Online"></div>
                </div>
            </a>

            <!-- General Settings -->
            <a href="{{ route('admin.settings') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fa-solid fa-gear w-4 h-4"></i>
                <span class="font-medium">Impostazioni Generali</span>
            </a>

            <!-- Pagine Legali -->
            <a href="{{ route('admin.legal.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.legal*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fa-solid fa-file-contract w-4 h-4"></i>
                <span class="font-medium">Pagine Legali</span>
            </a>

            <!-- Advertisement Management -->
            <a href="{{ route('admin.advertisements') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.advertisements') && !request()->routeIs('admin.advertisements.settings') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-bullhorn w-4 h-4"></i>
                <span class="font-medium">Pubblicità</span>
                <span
                    class="ml-auto bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs px-2 py-0.5 rounded-full">
                    {{ \App\Models\Advertisement::currentlyActive()->count() }}
                </span>
            </a>

            <!-- Advertisement Settings -->
            <a href="{{ route('admin.advertisements.settings') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.advertisements.settings') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-cogs w-4 h-4"></i>
                <span class="font-medium">Impostazioni Ads</span>
            </a>

            <!-- Analytics & Reports -->
            <a href="{{ route('admin.analytics') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.analytics*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-chart-line w-4 h-4"></i>
                <span class="font-medium">Analytics Avanzate</span>
            </a>

            <!-- Reports Management -->
            <a href="{{ route('admin.reports') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.reports') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-flag w-4 h-4"></i>
                <span class="font-medium">Gestione Segnalazioni</span>
                <span
                    class="ml-auto bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs px-2 py-0.5 rounded-full">
                    {{ \App\Models\Report::where('status', 'pending')->count() }}
                </span>
            </a>
        </nav>

        <!-- Admin Quick Stats -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Statistiche Rapide
            </h4>

            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Utenti Oggi</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\User::whereDate('created_at', today())->count() }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Video Oggi</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Video::whereDate('created_at', today())->count() }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Video in Processamento</span>
                        <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                            {{ \App\Models\Video::where('status', 'processing')->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Stato Sistema
            </h4>

            <div class="space-y-2">
                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                    </div>
                    <span class="text-xs text-green-600 dark:text-green-400">Online</span>
                </div>

                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">FFmpeg</span>
                    </div>
                    <span class="text-xs text-green-600 dark:text-green-400">Ready</span>
                </div>

                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Storage</span>
                    </div>
                    <span class="text-xs text-yellow-600 dark:text-yellow-400">Monitor</span>
                </div>
            </div>
        </div>

    </div>
</aside>

<script>
    function toggleMonetizationMenu() {
        const submenu = document.querySelector('.monetization-submenu');
        const arrow = document.querySelector('.monetization-arrow');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            submenu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    // Apri automaticamente il menu se siamo in una pagina di monetizzazione
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.href.includes('/admin/monetization')) {
            const submenu = document.querySelector('.monetization-submenu');
            const arrow = document.querySelector('.monetization-arrow');
            if (submenu && arrow) {
                submenu.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    });
</script>

<script>
    function toggleMonetizationMenu() {
        const submenu = document.querySelector('.monetization-submenu');
        const arrow = document.querySelector('.monetization-arrow');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            submenu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    // Apri automaticamente il menu se siamo in una pagina di monetizzazione
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.href.includes('/admin/monetization')) {
            const submenu = document.querySelector('.monetization-submenu');
            const arrow = document.querySelector('.monetization-arrow');
            if (submenu && arrow) {
                submenu.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    });
</script>

<!-- Mobile Admin Sidebar -->
<aside id="admin-mobile-sidebar"
    class="fixed left-0 top-16 bottom-0 w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-50 transform -translate-x-full lg:hidden transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="p-4">
        <!-- Same navigation as desktop but optimized for mobile -->
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-tachometer-alt w-4 h-4"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.users') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-users w-4 h-4"></i>
                <span class="font-medium">Gestione Utenti</span>
            </a>

            <a href="{{ route('admin.videos-management') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.videos-management*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-video w-4 h-4"></i>
                <span class="font-medium">Gestione Video</span>
            </a>

            <a href="{{ route('admin.legal.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.legal*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fa-solid fa-file-contract w-4 h-4"></i>
                <span class="font-medium">Pagine Legali</span>
            </a>

            <a href="{{ route('admin.advertisements') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.advertisements*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-bullhorn w-4 h-4"></i>
                <span class="font-medium">Pubblicità</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                <i class="fas fa-cogs w-4 h-4"></i>
                <span class="font-medium">Impostazioni FFmpeg</span>
            </a>
        </nav>

        <!-- Mobile Quick Stats -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Statistiche Oggi
            </h4>

            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\User::whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Nuovi Utenti</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\Video::whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Nuovi Video</div>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    function toggleMonetizationMenu() {
        const submenu = document.querySelector('.monetization-submenu');
        const arrow = document.querySelector('.monetization-arrow');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            submenu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    // Apri automaticamente il menu se siamo in una pagina di monetizzazione
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.href.includes('/admin/monetization')) {
            const submenu = document.querySelector('.monetization-submenu');
            const arrow = document.querySelector('.monetization-arrow');
            if (submenu && arrow) {
                submenu.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    });
</script>
