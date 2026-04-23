<div>
    <div wire:poll.20s="refreshData" class="relative">
        <button wire:click="toggleMenu"
            class="relative p-2.5 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 group">
            <i class="fa-solid fa-bell text-lg cursor-pointer"></i>
            @if ($unread > 0)
                <span
                    class="absolute -top-0.5 -right-0.5 h-5 min-w-[20px] px-1.5 bg-red-600 rounded-full text-[10px] font-semibold text-white flex items-center justify-center shadow-sm">
                    {{ $unread > 99 ? '99+' : $unread }}
                </span>
            @endif
        </button>

        <div x-data="{ open: @entangle('menuOpen') }" x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
            class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
            style="display: none;">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-9 h-9 bg-gray-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-bell text-white text-sm"></i>
                            </div>
                            @if ($unread > 0)
                                <div
                                    class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full animate-pulse">
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('ui.notifications_title') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @if ($unread > 0)
                                    {{ $unread }} {{ $unread === 1 ? __('ui.notifications_unread_single') : __('ui.notifications_unread_multiple') }}
                                @else
                                    {{ __('ui.notifications_all_read') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($unread > 0)
                        <button wire:click="markAllAsRead"
                            class="px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:text-white hover:bg-red-200 rounded-md transition-colors duration-200 flex items-center space-x-1 cursor-pointer">
                            <i class="fa-solid fa-check-double text-xs"></i>
                            <span>{{ __('ui.notifications_mark_all') }}</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto scrollbar-hide">
                @forelse ($items as $n)
                    <div
                        class="group relative {{ !$n['read_at'] ? $n['bg_color'] . ' ' . $n['border_color'] . ' border-l-4 notification-item' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }} transition-colors duration-200">
                        <a href="{{ $n['url'] }}" class="block px-6 py-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 {{ $n['icon_bg'] }} rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-colors duration-200 notification-icon">
                                            <i class="{{ $n['icon'] }} {{ $n['icon_color'] }} text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-start space-x-2">
                                                <h4
                                                    class="text-sm font-medium text-gray-900 dark:text-white leading-5 line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors duration-200">
                                                    {{ $n['title'] }}
                                                </h4>
                                                @if (!$n['read_at'])
                                                    <div
                                                        class="w-1.5 h-1.5 bg-red-500 rounded-full flex-shrink-0 mt-2 shadow-sm">
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($n['message'])
                                                <p
                                                    class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 leading-relaxed line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-200 notification-message">
                                                    {{ $n['message'] }}
                                                </p>
                                            @endif

                                            <div class="flex items-center mt-3 space-x-3">
                                                <div
                                                    class="flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400 notification-time">
                                                    <i class="fa-regular fa-clock text-xs"></i>
                                                    <span>{{ $n['created_at'] }}</span>
                                                </div>
                                                @if (!$n['read_at'])
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800">
                                                        <i class="fa-solid fa-circle text-[6px] mr-1"></i>
                                                        {{ __('ui.notifications_new') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if (!$n['read_at'])
                                            <button wire:click.prevent="markAsRead('{{ $n['id'] }}')"
                                                class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-md transition-opacity duration-200">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center notification-empty">
                        <div class="relative mb-4">
                            <div
                                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto shadow-sm notification-empty-icon">
                                <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                            </div>
                            @if ($unread == 0)
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 notification-empty-title">
                            {{ __('ui.notifications_all_updated') }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed notification-empty-text">
                            {{ __('ui.notifications_empty_subtitle') }}<br>
                            {{ __('ui.notifications_empty_detail') }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if (count($items) > 0)
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <a href="{{ route('user.notifications') }}" wire:click.prevent
                        class="group flex items-center justify-center space-x-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200">
                        <span>{{ __('ui.notifications_view_all') }}</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
