<?php

namespace App\Notifications;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VideoProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(public Video $video) {}

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
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return (new MailMessage)
            ->subject('Video Pronta per la Pubblicazione!')
            ->greeting('Ciao ' . $notifiable->name)
            ->line('Il tuo video "' . $video->title . '" è stato processato ed è pronto per essere pubblicato!')
            ->action('Vedi Video', $url)
            ->line('Puoi ora pubblicare il video o modificarlo come preferisci.');
    }

    public function toDatabase(object $notifiable): array
    {
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'video_ready',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => $video->title,
            'excerpt' => str($video->description)->limit(80),
            '__action_url' => $url, // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $video = $this->video;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'video_ready',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => $video->title,
            'excerpt' => str($video->description)->limit(80),
            'url' => $url,
        ];
    }
}