<?php

namespace App\Notifications\Alerts;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TokenDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Store $store, public string $tokenName)
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
            ->subject("Token eliminado para {$this->store->name} — Acceso a campañas")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha eliminado el token que permitía al centro \"{$this->store->name}\" acceder y reproducir sus campañas.")
            ->line("Nombre del token: {$this->tokenName}")
            ->line('Consecuencias:')
            ->line('• El centro perderá acceso automático a sus campañas y estas podrían dejar de reproducirse.')
            ->line('• Integraciones y procesos automatizados que usaban este token dejarán de funcionar hasta que se genere uno nuevo.')
            ->line('• Pérdida temporal de métricas o sincronización dependiente del token.')
            ->line('Si no solicitaste esta eliminación o detectas actividad sospechosa, contacta al administrador inmediatamente.')
            ->action('Gestionar tokens', url("/centertokens"))
            ->salutation('Atentamente, Locatel CMS');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'store_id' => $this->store->getKey(),
            'store_name' => $this->store->name,
            'token_name' => $this->tokenName,
        ];
    }
}
