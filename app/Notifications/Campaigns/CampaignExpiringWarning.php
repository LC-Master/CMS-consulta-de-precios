<?php

namespace App\Notifications\Campaigns;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignExpiringWarning extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly \App\Models\Campaign $campaign)
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Aviso: Tu campaña vence en menos de 24 horas')
            ->greeting("Hola, {$notifiable->name}")
            ->line('Te informamos que la campaña "**' . $this->campaign->getAttribute('title') . '**" está próxima a finalizar.')
            ->line('Fecha de finalización: ' . $this->campaign->getAttribute('end_at')->format('d/m/Y H:i'))
            ->action('Ver Campaña', url('/campaign/' . $this->campaign->getKey()))
            ->line('Si necesitas extenderla, por favor entre en la edición de la campaña.')
            ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'campaign_id' => $this->campaign->getKey(),
            'title' => $this->campaign->getAttribute('title'),
            'message' => 'Vence en menos de 24 horas',
        ];
    }
}
