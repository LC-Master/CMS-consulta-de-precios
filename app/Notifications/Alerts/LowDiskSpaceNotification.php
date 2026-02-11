<?php

namespace App\Notifications\Alerts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\HtmlString;

class LowDiskSpaceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $storeName, public float $freeSpaceGB)
    {
        //
    }
    public static function sendToAdmins(string $storeName, float $freeSpaceGB)
    {
        $users = User::role(['admin', 'supervisor'])->get();

        \Illuminate\Support\Facades\Notification::send(
            $users,
            new self($storeName, $freeSpaceGB)
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
        $free = number_format($this->freeSpaceGB, 2);

        return (new MailMessage)
            ->subject("Alerta crítica: {$this->storeName} - espacio en disco crítico")
            ->greeting('Hola,')
            ->line(new HtmlString("Se ha detectado que la tienda <strong>{$this->storeName}</strong> tiene <strong>{$free} GB</strong> de espacio libre."))
            ->line('Este nivel de espacio disponible puede provocar fallos en la creación de archivos, pérdida de logs o interrupciones en el servicio.')
            ->line('Acciones recomendadas:')
            ->line('- Verificar archivos temporales y logs para limpieza.')
            ->line('- Mover archivos antiguos a almacenamiento secundario.')
            ->line('- Considerar aumentar la cuota de disco cuanto antes.')
            ->action('Ver detalles y limpiar ahora', url("/admin/stores/{$this->storeName}/disk"))
            ->line("Espacio libre actual: {$free} GB")
            ->salutation(new HtmlString('Saludos,<br>Equipo de Infraestructura'));
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
            'free_space_gb' => $this->freeSpaceGB,
        ];
    }
}
