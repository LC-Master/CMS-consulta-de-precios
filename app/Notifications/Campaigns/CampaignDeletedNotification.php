<?php

namespace App\Notifications\Campaigns;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Campaign $campaign)
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
            ->subject('Campaña eliminada: ' . $this->campaign->getAttribute('title'))
            ->greeting("Hola, {$notifiable->name}")
            ->line('La campaña "' . $this->campaign->getAttribute('title') . '" ha sido eliminada.')
            ->line('Periodo: ' . ($this->campaign->getAttribute('start_at')?->format('Y-m-d H:i') ?? (string) $this->campaign->getAttribute('start_at') ?? 'N/A') . ' → ' . ($this->campaign->getAttribute('end_at')?->format('Y-m-d H:i') ?? (string) $this->campaign->getAttribute('end_at') ?? 'N/A'))
            ->line('Departamento: ' . ($this->campaign->department->name ?? 'N/A'))
            ->line('Estado: ' . ($this->campaign->status->status ?? 'N/A'))
            ->line('Convenio: ' . ($this->campaign->agreement->name ?? 'N/A'))
            ->line('Creada por : ' . ($this->campaign->user->name ?? 'N/A'))
            ->line('Eliminada por : ' . ($this->campaign->updatedBy->name ?? 'N/A'))
            ->line('Fecha de eliminación: ' . ($this->campaign->getAttribute('deleted_at')?->format('Y-m-d H:i') ?? 'N/A'))
            ->action('Ver campañas', url('/campaign'))
            ->line('Si esto fue un error, contacta al administrador o restaura la campaña desde el historial.');
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
            'start_at' => $this->campaign->getAttribute('start_at'),
            'end_at' => $this->campaign->getAttribute('end_at'),
            'status' => $this->campaign->status->status ?? null,
            'department' => $this->campaign->department->name ?? null,
            'agreement' => $this->campaign->agreement->name ?? null,
            'deleted_by' => $this->campaign->updatedBy->name ?? null,
            'deleted_at' => $this->campaign->getAttribute('deleted_at'),
        ];
    }
}
