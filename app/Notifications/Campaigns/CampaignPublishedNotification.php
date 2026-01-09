<?php

namespace App\Notifications\Campaigns;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Campaign;

class CampaignPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Campaign $campaign)
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
            ->subject('Campaña publicada: ' . $this->campaign->getAttribute('title'))
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Nos complace informarte que la campaña "' . $this->campaign->getAttribute('title') . '" ha sido publicada y ya está activa.')
            ->line('Puedes revisar los detalles, editar la campaña o compartirla desde el siguiente enlace.')
            ->action('Ver campaña', url("/campaign/{$this->campaign->getKey()}"))
            ->salutation('Saludos, ' . config('app.name'));
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
            'campaign_title' => $this->campaign->getAttribute('title'),
            'message' => "La campaña '{$this->campaign->getAttribute('title')}' ha sido publicada y está activa.",
        ];
    }
}
