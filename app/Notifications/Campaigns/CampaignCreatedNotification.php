<?php

namespace App\Notifications\Campaigns;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignCreatedNotification extends Notification implements ShouldQueue
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
        $start = $this->campaign->getAttribute('start_at') ? \Carbon\Carbon::parse($this->campaign->getAttribute('start_at'))->format('d/m/Y H:i') : 'N/A';
        $end = $this->campaign->getAttribute('end_at') ? \Carbon\Carbon::parse($this->campaign->getAttribute('end_at'))->format('d/m/Y H:i') : 'N/A';
        $status = $this->campaign->status->status ?? $this->campaign->getAttribute('status_id') ?? 'N/A';
        $department = $this->campaign->department->name ?? $this->campaign->getAttribute('department_id') ?? 'N/A';
        $agreement = $this->campaign->agreement->name ?? $this->campaign->getAttribute('agreement_id') ?? null;

        $mail = (new MailMessage)
            ->from('programadorweb@locatelve.com', 'Locatel CMS')
            ->subject("Nueva campaña creada: {$this->campaign->getAttribute('title')}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha creado la campaña: {$this->campaign->getAttribute('title')}")
            ->line("Periodo: {$start} — {$end}")
            ->line("Estado: {$status}")
            ->line("Departamento: {$department}")
            ->action('Ver campaña', url("/campaigns/{$this->campaign->getKey()}"))
            ->line('Gracias por usar nuestra aplicación.');

        if ($agreement) {
            $mail->line("Convenio: {$agreement}");
        }

        return $mail->success();
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
            'campaign_name' => $this->campaign->getAttribute('title'),
            'message' => "Se ha creado una nueva campaña: {$this->campaign->getAttribute('title')}",
        ];
    }
}
