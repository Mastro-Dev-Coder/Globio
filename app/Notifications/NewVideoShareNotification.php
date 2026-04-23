<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVideoShareNotification extends Notification
{
    use Queueable;

    public function __construct(public User $sharedBy, public Video $video) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if (method_exists($notifiable, 'prefersEmailNotifications') ? $notifiable->prefersEmailNotifications() : ($notifiable->email_notifications ?? false)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sharedBy = $this->sharedBy;
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return (new MailMessage)
            ->subject('Il Tuo Video è Stato Condiviso!')
            ->greeting('Ciao ' . $notifiable->name)
            ->line($sharedBy->name . ' ha condiviso il tuo video "' . $video->title . '"!')
            ->action('Vedi Video', $url)
            ->line('Grazie per i tuoi contenuti!');
    }

    public function toDatabase(object $notifiable): array
    {
        $sharedBy = $this->sharedBy;
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'video_shared',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => 'Video condiviso: ' . $video->title,
            'excerpt' => $sharedBy->name . ' ha condiviso il tuo video',
            '__action_url' => $url, // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $sharedBy = $this->sharedBy;
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'video_shared',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => 'Video condiviso: ' . $video->title,
            'excerpt' => $sharedBy->name . ' ha condiviso il tuo video',
            'url' => $url,
        ];
    }
}