<?php

namespace App\Notifications\Campaigns;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Campaign $campaign)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->campaign->getAttribute('title') ?? 'Campaña';
        $id = $this->campaign->getKey() ?? 'N/A';
        $start = $this->campaign->getAttribute('start_at') ?? 'N/A';
        $end = $this->campaign->getAttribute('end_at') ?? 'N/A';

        $mail = (new MailMessage)
            ->subject("Campaña finalizada: {$title}")
            ->greeting('Hola ' . ($notifiable->name ?? 'equipo') . ',')
            ->line("La campaña \"{$title}\" ha finalizado correctamente.")
            ->line('Aquí tienes un resumen rápido:')
            ->line("• ID: {$id}")
            ->line("• Inicio: {$start}")
            ->line("• Finalización: {$end}");

        $mail->action('Ver resultados', url("/campaign/{$id}"))
            ->salutation('Saludos cordiales,');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $_notifiable): array
    {
        return [
            'campaign_id' => $this->campaign->getKey() ?? null,
            'title' => $this->campaign->getAttribute('title') ?? 'Campaña',
            'message' => 'La campaña ha finalizado correctamente.',
            'ended_at' => now()->toDateTimeString(),
        ];
    }
}
