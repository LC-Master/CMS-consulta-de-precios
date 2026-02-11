<?php

namespace App\Notifications\Alerts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
class ConsultorOfflineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $name, public string $failHour)
    {
        //
    }
    public static function sendToAdmins(string $name, string $failHour)
    {
        $admins = User::role(['admin', 'supervisor'])->get();

        \Illuminate\Support\Facades\Notification::send(
            $admins,
            new self($name, $failHour)
        );
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
            ->subject("Alerta de sincronización: tienda \"{$this->name}\" no sincronizó en el horario programado")
            ->greeting('Estimado/a,')
            ->line("Se ha detectado que la tienda \"{$this->name}\" no completó la sincronización en el horario previsto.")
            ->line("Horario previsto de sincronización: {$this->failHour}")
            ->line('Acciones recomendadas:')
            ->line('• Verifique la conexión de red de la tienda.')
            ->line('• Revise los registros de sincronización y reintente el proceso si procede.')
            ->line('Si necesita asistencia adicional, contacte al equipo de soporte de CMS Locatel.')
            ->level('error')
            ->action('Ver listado de tiendas', url('/stores'))
            ->salutation('Atentamente,\nEquipo CMS Locatel');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'name' => $this->name,
            'failHour' => $this->failHour
        ];
    }
}
