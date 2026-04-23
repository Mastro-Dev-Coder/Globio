<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Report $report) {}

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
        $report = $this->report;
        
        return (new MailMessage)
            ->subject('Segnalazione Assegnata')
            ->greeting('Ciao ' . $notifiable->name)
            ->line('È stata assegnata una nuova segnalazione per il tuo contenuto.')
            ->line('Tipo di segnalazione: ' . $report->type)
            ->line('Motivo: ' . $report->reason)
            ->action('Vedi Dettagli', url()->route('creator.reports'))
            ->line('Il nostro team esaminerà la segnalazione al più presto.');
    }

    public function toDatabase(object $notifiable): array
    {
        $report = $this->report;
        
        return [
            'type' => 'report_assigned',
            'post_id' => $report->id,
            'post_title' => 'Segnalazione assegnata',
            'excerpt' => 'Nuova segnalazione: ' . $report->reason,
            '__action_url' => route('creator.reports'), // URL salvato con chiave speciale
        ];
    }

    public function toArray(object $notifiable): array
    {
        $report = $this->report;
        
        return [
            'type' => 'report_assigned',
            'post_id' => $report->id,
            'post_title' => 'Segnalazione assegnata',
            'excerpt' => 'Nuova segnalazione: ' . $report->reason,
            'url' => route('creator.reports'),
        ];
    }
}