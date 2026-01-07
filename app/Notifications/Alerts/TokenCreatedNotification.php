<?php

namespace App\Notifications\Alerts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TokenCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $center, public string $tokenName)
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
            ->subject("Token creado para {$this->center->name} — Acceso a campañas")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha generado un token para que el centro \"{$this->center->name}\" pueda acceder y reproducir sus campañas.")
            ->line("Nombre del token: {$this->tokenName}")
            ->line('Uso: este token autoriza la reproducción de las campañas del centro desde sistemas y dispositivos autorizados.')
            ->line('Mantén este token seguro y compártelo únicamente con personal o servicios autorizados. Si se filtra, revócalo de inmediato.')
            ->action('Gestionar tokens', url("/centertokens"))
            ->line('Si no solicitaste este token o detectas uso indebido, contacta al administrador para revocarlo y revisar las actividades.')
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
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'token_name' => $this->tokenName,
        ];
    }
}
