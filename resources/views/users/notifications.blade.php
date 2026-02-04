<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-8 py-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fa-solid fa-bell text-white text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Notifiche</h1>
                                <p class="text-red-100">Gestisci tutte le tue notifiche</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            @if ($notifications->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-bell text-red-600 dark:text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Totale</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $notifications->total() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-circle text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Non Lette</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $notifications->where('read_at', null)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-check text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lette</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $notifications->where('read_at', '!=', null)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-calendar-day text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Oggi</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $todayNotifications }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if ($notifications->where('read_at', null)->count() > 0)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Azioni Rapide</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gestisci le tue notifiche</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fa-solid fa-check-double mr-2"></i>
                                    Segna tutte lette
                                </button>
                            </form>
                            <button onclick="refreshNotifications()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <i class="fa-solid fa-refresh mr-2"></i>
                                Aggiorna
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notifications List -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($notifications->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($notifications as $notification)
                            @php
                                $type = $notification->data['type'] ?? 'default';
                                $style = match ($type) {
                                    'new_like' => [
                                        'icon' => 'fa-heart',
                                        'icon_color' => 'text-red-500',
                                        'bg_color' => 'bg-red-50 dark:bg-red-900/20',
                                        'icon_bg' => 'bg-red-100 dark:bg-red-900/40',
                                    ],
                                    'new_comment' => [
                                        'icon' => 'fa-message-text',
                                        'icon_color' => 'text-blue-500',
                                        'bg_color' => 'bg-blue-50 dark:bg-blue-900/20',
                                        'icon_bg' => 'bg-blue-100 dark:bg-blue-900/40',
                                    ],
                                    'new_subscriber' => [
                                        'icon' => 'fa-user-plus',
                                        'icon_color' => 'text-green-500',
                                        'bg_color' => 'bg-green-50 dark:bg-green-900/20',
                                        'icon_bg' => 'bg-green-100 dark:bg-green-900/40',
                                    ],
                                    'video_ready', 'video_processed' => [
                                        'icon' => 'fa-circle-check',
                                        'icon_color' => 'text-green-500',
                                        'bg_color' => 'bg-green-50 dark:bg-green-900/20',
                                        'icon_bg' => 'bg-green-100 dark:bg-green-900/40',
                                    ],
                                    'video_processing', 'video_processing_started' => [
                                        'icon' => 'fa-cog',
                                        'icon_color' => 'text-amber-500',
                                        'bg_color' => 'bg-amber-50 dark:bg-amber-900/20',
                                        'icon_bg' => 'bg-amber-100 dark:bg-amber-900/40',
                                    ],
                                    'video_shared' => [
                                        'icon' => 'fa-share-nodes',
                                        'icon_color' => 'text-purple-500',
                                        'bg_color' => 'bg-purple-50 dark:bg-purple-900/20',
                                        'icon_bg' => 'bg-purple-100 dark:bg-purple-900/40',
                                    ],
                                    'report_assigned' => [
                                        'icon' => 'fa-flag',
                                        'icon_color' => 'text-orange-500',
                                        'bg_color' => 'bg-orange-50 dark:bg-orange-900/20',
                                        'icon_bg' => 'bg-orange-100 dark:bg-orange-900/40',
                                    ],
                                    'creator_feedback' => [
                                        'icon' => 'fa-lightbulb',
                                        'icon_color' => 'text-yellow-500',
                                        'bg_color' => 'bg-yellow-50 dark:bg-yellow-900/20',
                                        'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/40',
                                    ],
                                    default => [
                                        'icon' => 'fa-bell',
                                        'icon_color' => 'text-gray-500',
                                        'bg_color' => 'bg-gray-50 dark:bg-gray-700/50',
                                        'icon_bg' => 'bg-gray-100 dark:bg-gray-700',
                                    ],
                                };

                                $title = match ($type) {
                                    'new_like' => 'Nuovo Mi Piace',
                                    'new_comment' => 'Nuovo Commento',
                                    'new_subscriber' => 'Nuovo Iscritto',
                                    'video_ready' => 'Video Pronto',
                                    'video_processed' => 'Video Elaborato',
                                    'video_processing' => 'Video in Elaborazione',
                                    'video_processing_started' => 'Elaborazione Avviata',
                                    'video_shared' => 'Video Condiviso',
                                    'report_assigned' => 'Segnalazione Assegnata',
                                    'creator_feedback' => 'Feedback del Creatore',
                                    default => $notification->data['title'] ?? 'Notifica',
                                };

                                $message = $notification->data['message'] ?? ($notification->data['excerpt'] ?? null);

                                if ($notification->created_at->isToday()) {
                                    $formattedTime = 'Oggi alle ' . $notification->created_at->format('H:i');
                                } elseif ($notification->created_at->isYesterday()) {
                                    $formattedTime = 'Ieri alle ' . $notification->created_at->format('H:i');
                                } elseif ($notification->created_at->isCurrentYear()) {
                                    $formattedTime = $notification->created_at->format('d/m H:i');
                                } else {
                                    $formattedTime = $notification->created_at->format('d/m/Y H:i');
                                }

                                $actionUrl =
                                    $notification->action_url ??
                                    ($notification->data['__action_url'] ?? ($notification->data['url'] ?? null));

                                if (empty($actionUrl) && isset($notification->data)) {
                                    $data = is_string($notification->data)
                                        ? json_decode($notification->data, true)
                                        : $notification->data;

                                    if (isset($data['video_id'])) {
                                        $video = \App\Models\Video::find($data['video_id']);
                                        if ($video) {
                                            $actionUrl = $video->is_reel
                                                ? route('reels.show', $video)
                                                : route('videos.show', $video->video_url);
                                        }
                                    } elseif (isset($data['post_id'])) {
                                        $video = \App\Models\Video::find($data['post_id']);
                                        if ($video) {
                                            $actionUrl = $video->is_reel
                                                ? route('reels.show', $video)
                                                : route('videos.show', $video->video_url);
                                        }
                                    } elseif (isset($data['channel_id'])) {
                                        $channel = \App\Models\UserProfile::where(
                                            'user_id',
                                            $data['channel_id'],
                                        )->first();
                                        if ($channel) {
                                            $actionUrl = route('channel.show', $channel->channel_name);
                                        }
                                    } elseif (isset($data['comment_id'])) {
                                        $comment = \App\Models\Comment::find($data['comment_id']);
                                        if ($comment && $comment->video) {
                                            $actionUrl = $comment->video->is_reel
                                                ? route('reels.show', $comment->video)
                                                : route('videos.show', $comment->video->video_url);
                                        }
                                    }
                                }
                            @endphp

                            @if (!empty($actionUrl))
                                <a href="{{ $actionUrl }}"
                                    class="block notification-item {{ !$notification->read_at ? $style['bg_color'] : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }} transition-colors duration-200"
                                    data-notification-id="{{ $notification->id }}">
                                @else
                                    <div class="notification-item {{ !$notification->read_at ? $style['bg_color'] : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }} transition-colors duration-200"
                                        data-notification-id="{{ $notification->id }}">
                            @endif
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 {{ $style['icon_bg'] }} rounded-lg flex items-center justify-center">
                                            <i class="fa-solid {{ $style['icon'] }} {{ $style['icon_color'] }}"></i>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                                        {{ $title }}
                                                    </h3>
                                                    @if (!$notification->read_at)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                            <i class="fa-solid fa-circle text-[6px] mr-1"></i>
                                                            Nuovo
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($message)
                                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $message }}
                                                    </p>
                                                @endif

                                                <div class="flex items-center mt-3 space-x-4">
                                                    <div
                                                        class="flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $formattedTime }}</span>
                                                    </div>

                                                    <span
                                                        class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                                                        {{ str_replace('_', ' ', $type) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                @if (!$notification->read_at)
                                                    <button onclick="markAsRead({{ $notification->id }})"
                                                        class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors">
                                                        <i class="fa-solid fa-check text-sm"></i>
                                                    </button>
                                                @endif

                                                @if (!empty($actionUrl))
                                                    <span
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                                        <i class="fa-solid fa-arrow-right mr-1"></i>
                                                        Clicca per visualizzare
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($actionUrl))
                                </a>
                            @else
                    </div>
                @endif
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando {{ $notifications->firstItem() ?? 0 }} -
                        {{ $notifications->lastItem() ?? 0 }} di {{ $notifications->total() }} notifiche
                    </div>
                    <div>
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="px-8 py-16 text-center">
                <div
                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Nessuna notifica
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Al momento non hai notifiche da visualizzare.
                </p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fa-solid fa-home mr-2"></i>
                    Vai alla Home
                </a>
            </div>
            @endif
        </div>
    </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function markAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notification = document.querySelector(
                            `[data-notification-id="${notificationId}"]`);
                        if (notification) {
                            notification.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'bg-blue-50',
                                'dark:bg-blue-900/20', 'bg-green-50', 'dark:bg-green-900/20',
                                'bg-amber-50', 'dark:bg-amber-900/20', 'bg-purple-50',
                                'dark:bg-purple-900/20', 'bg-orange-50', 'dark:bg-orange-900/20',
                                'bg-yellow-50', 'dark:bg-yellow-900/20');
                            notification.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700/50');

                            const newBadge = notification.querySelector('.bg-red-100, .bg-red-900\\/30');
                            if (newBadge) {
                                newBadge.remove();
                            }

                            const markReadButton = notification.querySelector(
                                'button[onclick*="markAsRead"]');
                            if (markReadButton) {
                                markReadButton.remove();
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
        }

        function refreshNotifications() {
            window.location.reload();
        }

        window.markAsRead = markAsRead;
        window.refreshNotifications = refreshNotifications;
    });
</script>
