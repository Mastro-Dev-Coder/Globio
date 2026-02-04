<?php

namespace App\Notifications;

use App\Models\CreatorFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreatorFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(public CreatorFeedback $feedback) {}

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
        $feedback = $this->feedback;
        
        return (new MailMessage)
            ->subject('Feedback dal Team')
            ->greeting('Ciao ' . $notifiable->name)
            ->line('Hai ricevuto un nuovo feedback dal nostro team riguardo ai tuoi contenuti.')
            ->line('Feedback: ' . $feedback->feedback)
            ->action('Vedi Feedback', route('creator.reports'))
            ->line('Il nostro team è qui per aiutarti a migliorare i tuoi contenuti.');
    }

    public function toDatabase(object $notifiable): array
    {
        $feedback = $this->feedback;
        
        return [
            'type' => 'creator_feedback',
            'post_id' => $feedback->id,
            'post_title' => 'Feedback dal team',
            'excerpt' => str($feedback->feedback)->limit(80),
            '__action_url' => route('creator.reports'), // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $feedback = $this->feedback;
        
        return [
            'type' => 'creator_feedback',
            'post_id' => $feedback->id,
            'post_title' => 'Feedback dal team',
            'excerpt' => str($feedback->feedback)->limit(80),
            'url' => route('creator.reports'),
        ];
    }
}