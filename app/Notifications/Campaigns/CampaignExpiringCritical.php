<?php

namespace App\Notifications\Campaigns;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignExpiringCritical extends Notification implements ShouldQueue
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
            ->error() 
            ->subject('URGENTE: Tu campaña vence en menos de 8 horas')
            ->greeting("Atención, {$notifiable->name}")
            ->line('La campaña "**' . $this->campaign->getAttribute('title') . '**" finalizará en muy poco tiempo.')
            ->line('Quedan menos de 8 horas para que el contenido deje de mostrarse.')
            ->action('Revisar ahora', url('/campaign/' . $this->campaign->getKey()))
            ->line('Asegúrate de tomar las acciones necesarias antes de la expiración.');
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
            'message' => 'URGENTE: Vence en menos de 8 horas',
        ];
    }
}
