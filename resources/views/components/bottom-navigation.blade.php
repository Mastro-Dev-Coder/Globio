<!-- Bottom Navigation Mobile - Simile a YouTube -->
<nav
    class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700 lg:hidden bottom-navigation">
    <div class="flex items-center justify-around py-2 px-1">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="flex flex-col items-center justify-center p-2 rounded-lg transition-all duration-200 min-w-0 flex-1 bottom-nav-item touch-feedback {{ request()->routeIs('home') ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs font-medium truncate">Home</span>
            @if (request()->routeIs('home'))
                <div
                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-red-600 dark:bg-red-400 rounded-full">
                </div>
            @endif
        </a>

        <!-- Reels -->
        <a href="{{ route('reels.index') }}"
            class="flex flex-col items-center justify-center p-2 rounded-lg transition-all duration-200 min-w-0 flex-1 bottom-nav-item touch-feedback {{ request()->routeIs('reels*') ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
            <div class="relative">
                <i class="fas fa-play text-lg mb-1 transform"></i>
                @if (request()->routeIs('reels*'))
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-600 dark:bg-red-400 rounded-full"></div>
                @endif
            </div>
            <span class="text-xs font-medium truncate">Reels</span>
        </a>

        <!-- Upload Video -->
        <div class="relative flex items-center justify-center">
            @auth
                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                    class="flex items-center justify-center w-14 h-14 bg-gray-800/70 text-white rounded-full shadow-lg transform hover:scale-105 transition-all duration-200 active:scale-95 upload-button touch-feedback">
                    <i class="fas fa-plus text-xl"></i>
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="flex items-center justify-center w-14 h-14 bg-gray-800/70 text-white rounded-full shadow-lg transform hover:scale-105 transition-all duration-200 active:scale-95 upload-button touch-feedback">
                    <i class="fas fa-plus text-xl"></i>
                </a>
            @endauth

            <!-- Ripple effect -->
            <div class="absolute inset-0 rounded-full bg-red-500 opacity-30 animate-ping"></div>
        </div>

        <!-- Iscrizioni -->
        <a href="{{ auth()->check() ? route('subscriptions') : route('login') }}"
            class="flex flex-col items-center justify-center p-2 rounded-lg transition-all duration-200 min-w-0 flex-1 bottom-nav-item touch-feedback {{ request()->routeIs('subscriptions') ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
            <div class="relative">
                <i class="fas fa-heart text-lg mb-1"></i>
                @auth
                    @php
                        $unreadSubscriptions = \App\Models\Notification::where('user_id', Auth::id())
                            ->where('type', 'new_video')
                            ->where('read_at', null)
                            ->count();
                    @endphp
                    @if ($unreadSubscriptions > 0)
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center notification-badge">
                            {{ $unreadSubscriptions > 9 ? '9+' : $unreadSubscriptions }}
                        </span>
                    @endif
                @endauth
            </div>
            <span class="text-xs font-medium truncate">Iscrizioni</span>
        </a>

        <!-- Profilo/Tu -->
        @auth
            <a href="{{ route('users.profile') }}"
                class="flex flex-col items-center justify-center p-2 rounded-lg transition-all duration-200 min-w-0 flex-1 bottom-nav-item touch-feedback profile-avatar {{ request()->routeIs('users.profile') || request()->routeIs('channel.show*') ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <div class="relative mb-1">
                    @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                        <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" alt="Profilo"
                            class="w-6 h-6 rounded-full object-cover border-2 border-current">
                    @else
                        <div
                            class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-2 border-current">
                            <span class="text-white text-xs font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif

                    <!-- Online indicator -->
                    <div
                        class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-green-500 border border-white dark:border-gray-900 rounded-full">
                    </div>
                </div>
                <span class="text-xs font-medium truncate">Tu</span>

                @if (request()->routeIs('users.profile') || request()->routeIs('channel.show*'))
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-red-600 dark:bg-red-400 rounded-full">
                    </div>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}"
                class="flex flex-col items-center justify-center p-2 rounded-lg transition-all duration-200 min-w-0 flex-1 bottom-nav-item touch-feedback text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <i class="fas fa-user text-lg mb-1"></i>
                <span class="text-xs font-medium truncate">Accedi</span>
            </a>
        @endauth

    </div>
</nav>

<!-- Safe area padding per i dispositivi con notch -->
<style>
    /* Safe area per iPhone con notch */
    @supports (padding: max(0px)) {
        .bottom-navigation {
            padding-bottom: max(8px, env(safe-area-inset-bottom));
        }
    }

    /* Animazioni migliorate per il bottom navigation */
    .bottom-nav-item {
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bottom-nav-item:hover {
        transform: translateY(-2px);
    }

    .bottom-nav-item:active {
        transform: translateY(0);
    }

    /* Glow effect per il pulsante centrale */
    .upload-button {
        position: relative;
        overflow: hidden;
    }

    .upload-button::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .upload-button:active::before {
        width: 100px;
        height: 100px;
    }

    /* Indicatore di pagina attiva */
    .active-page-indicator {
        animation: pageIndicator 0.3s ease-out;
    }

    @keyframes pageIndicator {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Miglioramenti per accessibilità */
    @media (prefers-reduced-motion: reduce) {

        .bottom-nav-item,
        .upload-button,
        .upload-button::before {
            transition: none;
            animation: none;
        }
    }

    /* Tema scuro miglioramenti */
    @media (prefers-color-scheme: dark) {
        .bottom-navigation {
            border-color: rgba(75, 85, 99, 0.3);
        }
    }
</style>

<script>
    // Gestione touch events per migliorare l'esperienza mobile
    document.addEventListener('DOMContentLoaded', function() {
        const navItems = document.querySelectorAll('.bottom-nav-item');

        navItems.forEach(item => {
            item.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(1px) scale(0.98)';
            });

            item.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Gestione dell'indicatore di pagina attiva
        const currentPath = window.location.pathname;
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath === href) {
                item.classList.add('active-page-indicator');
            }
        });

        // Haptic feedback per dispositivi supportati
        if ('vibrate' in navigator) {
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navigator.vibrate(10); // Vibrazione breve per feedback
                });
            });
        }
    });
</script>
