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
            ->subject("Notificación Urgente: Error Crítico en Sincronización de Tienda {$this->storeName}")
            ->greeting("Estimado {$recipientName},")
            ->line(new HtmlString("Le informamos que se ha producido un error crítico durante el proceso de sincronización de datos en la tienda <strong>{$this->storeName}</strong>."))
            ->line('Este incidente podría afectar la disponibilidad de información actualizada y el funcionamiento normal de los servicios asociados a esta tienda.')
            ->line('Es imperativo que revise los detalles del error a continuación y tome las acciones correctivas necesarias de manera inmediata para minimizar cualquier interrupción en el servicio.')
            ->line(new HtmlString("<strong>Detalles del Error:</strong><br>{$this->errorMessage}"))
            ->line('Si requiere asistencia adicional o tiene alguna pregunta, no dude en contactar al equipo de soporte técnico.')
            ->action('Acceder al Panel de Tiendas', url('/stores'))
            ->salutation(new HtmlString('Atentamente,<br>Equipo de Integraciones de CMS'));
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
