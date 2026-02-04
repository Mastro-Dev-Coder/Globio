<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubscriberNotification extends Notification
{
    use Queueable;

    public function __construct(public User $subscriber) {}

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
        $subscriber = $this->subscriber;
        $channelUrl = route('channel.show', $subscriber->profile->username ?? $subscriber->name);
        
        return (new MailMessage)
            ->subject('Nuovo Iscritto al Tuo Canale!')
            ->greeting('Ciao ' . $notifiable->name)
            ->line($subscriber->name . ' si è iscritto al tuo canale!')
            ->action('Vedi Canale', $channelUrl)
            ->line('Grazie per i tuoi contenuti!');
    }

    public function toDatabase(object $notifiable): array
    {
        $subscriber = $this->subscriber;
        $channelUrl = route('channel.show', $subscriber->profile->username ?? $subscriber->name);
        
        return [
            'type' => 'new_subscriber',
            'user_id' => $subscriber->id,
            'post_id' => $subscriber->id,
            'post_title' => 'Nuovo iscritto: ' . $subscriber->name,
            'excerpt' => $subscriber->name . ' si è iscritto al tuo canale',
            '__action_url' => $channelUrl, // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $subscriber = $this->subscriber;
        $channelUrl = route('channel.show', $subscriber->profile->username ?? $subscriber->name);
        
        return [
            'type' => 'new_subscriber',
            'user_id' => $subscriber->id,
            'post_id' => $subscriber->id,
            'post_title' => 'Nuovo iscritto: ' . $subscriber->name,
            'excerpt' => $subscriber->name . ' si è iscritto al tuo canale',
            'url' => $channelUrl,
        ];
    }
}