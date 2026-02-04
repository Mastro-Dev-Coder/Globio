<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsBell extends Component
{
    public int $unread = 0;
    public array $items = [];
    public bool $menuOpen = false;

    protected $listeners = ['notification-read'];

    public function mount()
    {
        $this->updateNotifications();
    }

    public function refreshData()
    {
        $this->updateNotifications();
    }

    private function getNotificationStyle(string $type): array
    {
        return match($type) {
            'new_like' => [
                'icon' => 'fa-solid fa-heart',
                'icon_color' => 'text-red-500',
                'bg_color' => 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-950/20 dark:to-rose-950/20',
                'icon_bg' => 'bg-gradient-to-br from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30',
                'border_color' => 'border-red-200/50 dark:border-red-800/50'
            ],
            'new_comment' => [
                'icon' => 'fa-solid fa-comment',
                'icon_color' => 'text-blue-500',
                'bg_color' => 'bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20',
                'icon_bg' => 'bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30',
                'border_color' => 'border-blue-200/50 dark:border-blue-800/50'
            ],
            'new_subscriber' => [
                'icon' => 'fa-solid fa-user-plus',
                'icon_color' => 'text-green-500',
                'bg_color' => 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/20 dark:to-emerald-950/20',
                'icon_bg' => 'bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30',
                'border_color' => 'border-green-200/50 dark:border-green-800/50'
            ],
            'video_ready', 'video_processed' => [
                'icon' => 'fa-solid fa-circle-check',
                'icon_color' => 'text-green-500',
                'bg_color' => 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/20 dark:to-emerald-950/20',
                'icon_bg' => 'bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30',
                'border_color' => 'border-green-200/50 dark:border-green-800/50'
            ],
            'video_processing', 'video_processing_started' => [
                'icon' => 'fa-solid fa-cog',
                'icon_color' => 'text-amber-500',
                'bg_color' => 'bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/20 dark:to-yellow-950/20',
                'icon_bg' => 'bg-gradient-to-br from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30',
                'border_color' => 'border-amber-200/50 dark:border-amber-800/50'
            ],
            'video_shared' => [
                'icon' => 'fa-solid fa-share-nodes',
                'icon_color' => 'text-purple-500',
                'bg_color' => 'bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-950/20 dark:to-violet-950/20',
                'icon_bg' => 'bg-gradient-to-br from-purple-100 to-violet-100 dark:from-purple-900/30 dark:to-violet-900/30',
                'border_color' => 'border-purple-200/50 dark:border-purple-800/50'
            ],
            'report_assigned' => [
                'icon' => 'fa-solid fa-flag',
                'icon_color' => 'text-orange-500',
                'bg_color' => 'bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-950/20 dark:to-red-950/20',
                'icon_bg' => 'bg-gradient-to-br from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30',
                'border_color' => 'border-orange-200/50 dark:border-orange-800/50'
            ],
            'creator_feedback' => [
                'icon' => 'fa-solid fa-lightbulb',
                'icon_color' => 'text-yellow-500',
                'bg_color' => 'bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-950/20 dark:to-amber-950/20',
                'icon_bg' => 'bg-gradient-to-br from-yellow-100 to-amber-100 dark:from-yellow-900/30 dark:to-amber-900/30',
                'border_color' => 'border-yellow-200/50 dark:border-yellow-800/50'
            ],
            default => [
                'icon' => 'fa-solid fa-bell',
                'icon_color' => 'text-gray-500',
                'bg_color' => 'bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-950/20 dark:to-slate-950/20',
                'icon_bg' => 'bg-gradient-to-br from-gray-100 to-slate-100 dark:from-gray-900/30 dark:to-slate-900/30',
                'border_color' => 'border-gray-200/50 dark:border-gray-800/50'
            ]
        };
    }

    private function generateNotificationTitle(array $data): string
    {
        $type = $data['type'] ?? '';
        
        return match($type) {
            'new_like' => 'Nuovo Mi Piace',
            'new_comment' => 'Nuovo Commento',
            'new_subscriber' => 'Nuovo Iscritto',
            'video_ready' => 'Video Pronto',
            'video_processed' => 'Video Elaborato',
            'video_processing' => 'Video in Elaborazione',
            'video_processing_started' => 'Elaborazione Video Avviata',
            'video_shared' => 'Video Condiviso',
            'report_assigned' => 'Segnalazione Assegnata',
            'creator_feedback' => 'Feedback del Creatore',
            default => 'Notifica'
        };
    }

    private function generateNotificationMessage(array $data): ?string
    {
        $type = $data['type'] ?? '';
        
        return match($type) {
            'new_like' => $data['excerpt'] ?? 'Alcuno ha messo mi piace al tuo video',
            'new_comment' => $data['excerpt'] ?? 'Alcuno ha commentato il tuo video',
            'new_subscriber' => 'Un nuovo utente si è iscritto al tuo canale',
            'video_ready' => 'Il tuo video è stato elaborato con successo ed è ora disponibile',
            'video_processed' => 'L\'elaborazione del tuo video è completata',
            'video_processing' => 'Il tuo video è attualmente in elaborazione',
            'video_processing_started' => 'L\'elaborazione del tuo video è iniziata',
            'video_shared' => 'Il tuo video è stato condiviso da un altro utente',
            'report_assigned' => 'Ti è stata assegnata una nuova segnalazione da revisionare',
            'creator_feedback' => 'Hai ricevuto nuovo feedback sui tuoi contenuti',
            default => $data['excerpt'] ?? null
        };
    }

    private function formatItalianDateTime($dateTime): string
    {
        $now = now();
        $diff = $now->diffForHumans($dateTime, true, false, 2);
        
        // Traduzioni per diffForHumans
        $translations = [
            'second' => 'secondo',
            'seconds' => 'secondi',
            'minute' => 'minuto',
            'minutes' => 'minuti',
            'hour' => 'ora',
            'hours' => 'ore',
            'day' => 'giorno',
            'days' => 'giorni',
            'week' => 'settimana',
            'weeks' => 'settimane',
            'month' => 'mese',
            'months' => 'mesi',
            'year' => 'anno',
            'years' => 'anni',
            'ago' => 'fa',
            'from now' => 'tra'
        ];
        
        $italianDiff = strtr($diff, $translations);
        
        if ($dateTime->isToday()) {
            return 'Oggi alle ' . $dateTime->format('H:i');
        }
        
        if ($dateTime->isYesterday()) {
            return 'Ieri alle ' . $dateTime->format('H:i');
        }
        
        if ($dateTime->isCurrentYear()) {
            return $dateTime->format('d/m H:i');
        }
        
        return $dateTime->format('d/m/Y H:i');
    }

    private function generateNotificationUrl(array $data): string
    {
        $type = $data['type'] ?? '';

        switch ($type) {
            case 'new_comment':
                if (isset($data['video_id'])) {
                    $video = \App\Models\Video::find($data['video_id']);
                    if ($video) {
                        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
                        if (isset($data['comment_id'])) {
                            $url .= '#comment-' . $data['comment_id'];
                        }
                        return $url;
                    }
                }
                break;
            case 'new_like':
            case 'video_ready':
            case 'video_processing':
            case 'video_processed':
            case 'video_shared':
                if (isset($data['video_id'])) {
                    $video = \App\Models\Video::find($data['video_id']);
                    if ($video) {
                        return $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
                    }
                }
                break;

            case 'new_subscriber':
                if (isset($data['user_id'])) {
                    $user = \App\Models\User::find($data['user_id']);
                    if ($user && $user->profile) {
                        return route('channel.show', $user->profile->username ?? $user->name);
                    }
                }
                break;

            case 'report_assigned':
            case 'creator_feedback':
                return route('creator.reports');

            default:
                return $data['url'] ?? $data['__action_url'] ?? '#';
        }

        return '#';
    }

    private function updateNotifications(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->unread = $user->unreadNotifications->count();
        $this->items = $user->notifications->sortByDesc('created_at')->take(10)->map(function ($n) {
            $data = $n->data;
            $type = $data['type'] ?? 'default';
            
            // Genera URL basato sul tipo di notifica
            $url = $this->generateNotificationUrl($data);
            
            // Ottieni stile per il tipo di notifica
            $style = $this->getNotificationStyle($type);
            
            return [
                'id' => $n->id,
                'type' => $type,
                'title' => $this->generateNotificationTitle($data),
                'message' => $this->generateNotificationMessage($data),
                'icon' => $style['icon'],
                'icon_color' => $style['icon_color'],
                'icon_bg' => $style['icon_bg'],
                'bg_color' => $style['bg_color'],
                'border_color' => $style['border_color'],
                'url' => $url,
                'read_at' => $n->read_at,
                'created_at' => $this->formatItalianDateTime($n->created_at),
                'original_date' => $n->created_at
            ];
        })->toArray();
    }

    public function markAsRead(string $id)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        $this->updateNotifications();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->unreadNotifications->each(function ($notification) {
            $notification->markAsRead();
        });

        $this->updateNotifications();
    }

    public function toggleMenu()
    {
        $this->menuOpen = !$this->menuOpen;
    }

    public function render()
    {
        return view('livewire.notifications-bell');
    }
}
