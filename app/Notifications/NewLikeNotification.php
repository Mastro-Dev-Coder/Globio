<?php

namespace App\Notifications;

use App\Models\Like;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification
{
    use Queueable;

    public function __construct(public Like $like) {}

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
        $like = $this->like;
        $video = $like->video;
        $liker = $like->user;
        $url = $video->is_reel ? url()->route('reels.show', $video) : url()->route('videos.show', $video->video_url);
        
        return (new MailMessage)
            ->subject('Nuovo Like sul Tuo Video!')
            ->greeting('Ciao ' . $notifiable->name)
            ->line($liker->name . ' ha messo mi piace al tuo video "' . $video->title . '"')
            ->action('Vedi Video', $url)
            ->line('Grazie per i tuoi contenuti!');
    }

    public function toDatabase(object $notifiable): array
    {
        $like = $this->like;
        $video = $like->video;
        $liker = $like->user;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'new_like',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => 'Nuovo like su: ' . $video->title,
            'excerpt' => $liker->name . ' ha messo mi piace al tuo video',
            '__action_url' => $url, // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $like = $this->like;
        $video = $like->video;
        $liker = $like->user;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        
        return [
            'type' => 'new_like',
            'video_id' => $video->id,
            'post_id' => $video->id,
            'post_title' => 'Nuovo like su: ' . $video->title,
            'excerpt' => $liker->name . ' ha messo mi piace al tuo video',
            'url' => $url,
        ];
    }
}