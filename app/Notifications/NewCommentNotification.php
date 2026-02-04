<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment) {}

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
        $comment = $this->comment;
        $video = $comment->video;
        $commenter = $comment->user;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);

        return (new MailMessage)
            ->subject('Nuovo Commento sul Tuo Video!')
            ->greeting('Ciao ' . $notifiable->name)
            ->line($commenter->name . ' ha commentato il tuo video "' . $video->title . '"')
            ->line('"' . $comment->content . '"')
            ->action('Vedi Video', $url)
            ->line('Grazie per i tuoi contenuti!');
    }

    public function toDatabase(object $notifiable): array
    {
        $comment = $this->comment;
        $video = $comment->video;
        $commenter = $comment->user;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);
        $url .= '#comment-' . $comment->id; // Add comment fragment

        return [
            'type' => 'new_comment',
            'video_id' => $video->id,
            'comment_id' => $comment->id,
            'post_id' => $video->id,
            'post_title' => 'Nuovo commento su: ' . $video->title,
            'excerpt' => str($comment->content)->limit(80),
            '__action_url' => $url, // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $comment = $this->comment;
        $video = $comment->video;
        $commenter = $comment->user;
        $url = $video->is_reel ? route('reels.show', $video) : route('videos.show', $video->video_url);

        return [
            'type' => 'new_comment',
            'video_id' => $video->id,
            'comment_id' => $comment->id,
            'post_id' => $video->id,
            'post_title' => 'Nuovo commento su: ' . $video->title,
            'excerpt' => str($comment->content)->limit(80),
            'url' => $url,
        ];
    }
}
