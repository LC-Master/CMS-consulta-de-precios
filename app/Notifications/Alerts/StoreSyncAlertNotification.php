<?php

namespace App\Notifications\Alerts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\HtmlString;

class StoreSyncAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $storeName, public string $errorMessage)
    {
        //
    }

    public static function sendToAdmin(string $storeName, string $errorMessage)
    {
        $users = User::role(['supervisor'])->get();
        \Illuminate\Support\Facades\Notification::send($users, new self($storeName, $errorMessage));
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
        $recipientName = ($notifiable->name ?? $notifiable->email) ?: 'equipo';

        return (new MailMessage)
            ->subject("[Alerta] Error de sincronización - {$this->storeName}")
            ->greeting("Hola {$recipientName},")
            ->line(new HtmlString("Se ha detectado un error crítico en la sincronización de la tienda <strong>{$this->storeName}</strong>."))
            ->line('Detalle del error:')
            ->line($this->errorMessage)
            ->line('Por favor, revisa la tienda y toma las acciones necesarias para corregir el problema.')
            ->action('Abrir panel de tienda', url('/stores'))
            ->salutation(new HtmlString('Atentamente,<br>Equipo de Integraciones'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'store_name' => $this->storeName,
            'error_message' => $this->errorMessage,
        ];
    }
}
