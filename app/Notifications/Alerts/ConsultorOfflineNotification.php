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
    /**
     * @param  array<int, array{store_name: string, fail_hour: string}>|null  $summary
     */
    public function __construct(public string $name, public string $failHour, public ?array $summary = null)
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
     * @param  array<int, array{store_name: string, fail_hour: string}>  $summary
     */
    public static function sendSummaryToAdmins(array $summary): void
    {
        if (empty($summary)) {
            return;
        }

        $admins = User::role(['admin', 'supervisor'])->get();

            \Illuminate\Support\Facades\Notification::send(
            $admins,
            new self('Resumen de tiendas', 'N/A', $summary)
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
        if (is_array($this->summary) && count($this->summary) > 0) {
            $message = (new MailMessage)
                ->subject('Resumen táctico: tiendas fuera de ventana de sincronización')
                ->greeting('Estimado equipo,')
                ->line('Se identificaron tiendas que no completaron sincronización dentro de la ventana horaria esperada:')
                ->line('');

            foreach ($this->summary as $item) {
                $message->line("• {$item['store_name']} — horario esperado: {$item['fail_hour']}");
            }

            return $message
                ->line('Plan de acción recomendado:')
                ->line('• Verificar conectividad y estado del consultor en cada tienda listada.')
                ->line('• Revisar logs de sincronización y ejecutar reintento controlado.')
                ->line('• Escalar a soporte si la tienda permanece fuera de ventana tras el reintento.')
                ->level('error')
                ->action('Ver listado de tiendas', url('/stores'))
                ->salutation('Atentamente, Plataforma CMS Locatel');
        }

        return (new MailMessage)
            ->subject("Alerta operativa: tienda fuera de horario de sincronización ({$this->name})")
            ->greeting('Estimado equipo,')
                ->line("La tienda \"{$this->name}\" no completó la sincronización en la ventana planificada.")
            ->line("Horario objetivo no cumplido: {$this->failHour}")
            ->line('Acción táctica sugerida:')
            ->line('• Validar estado de red y disponibilidad del consultor en sitio.')
            ->line('• Revisar logs de sincronización y ejecutar reintento supervisado.')
            ->line('• Escalar a soporte técnico si la incidencia persiste.')
            ->level('error')
            ->action('Ver listado de tiendas', url('/stores'))
            ->salutation('Atentamente, Plataforma CMS Locatel');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if (is_array($this->summary) && count($this->summary) > 0) {
            return [
                'type' => 'summary',
                'items' => $this->summary,
            ];
        }

        return [
            'name' => $this->name,
            'failHour' => $this->failHour
        ];
    }
}
