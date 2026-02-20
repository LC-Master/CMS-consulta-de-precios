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
    /**
     * @param  array<int, array{store_name: string, error_message: string}>|null  $summary
     */
    public function __construct(public string $storeName, public string $errorMessage, public ?array $summary = null)
    {
        //
    }

    public static function sendToAdmin(string $storeName, string $errorMessage)
    {
        $users = User::role(['supervisor'])->get();
        \Illuminate\Support\Facades\Notification::send($users, new self($storeName, $errorMessage));
    }

    /**
     * @param  array<int, array{store_name: string, error_message: string}>  $summary
     */
    public static function sendSummaryToAdmins(array $summary): void
    {
        if (empty($summary)) {
            return;
        }

        $users = User::role(['supervisor'])->get();
        \Illuminate\Support\Facades\Notification::send($users, new self('Resumen de tiendas', 'N/A', $summary));
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

        if (is_array($this->summary) && count($this->summary) > 0) {
            $message = (new MailMessage)
                ->subject('Resumen crítico de sincronización: tiendas con incidencia')
                ->greeting("Estimado {$recipientName},")
                ->line('En esta ejecución se consolidaron tiendas con incidencias críticas de sincronización:')
                ->line('');

            foreach ($this->summary as $item) {
                $message->line(new HtmlString("• <strong>{$item['store_name']}</strong>: {$item['error_message']}"));
            }

            return $message
                ->line('Acción inmediata recomendada: priorizar atención de las tiendas listadas para restablecer continuidad operativa.')
                ->line('Acción complementaria: documentar causa raíz y estado de remediación por tienda.')
                ->action('Acceder al Panel de Tiendas', url('/stores'))
                ->salutation(new HtmlString('Atentamente,<br>Plataforma CMS Locatel'));
        }

        return (new MailMessage)
            ->subject("Alerta crítica de sincronización: {$this->storeName}")
            ->greeting("Estimado {$recipientName},")
            ->line(new HtmlString("Se registró una incidencia crítica durante la sincronización de la tienda <strong>{$this->storeName}</strong>."))
            ->line('Este evento puede comprometer la actualización de contenidos y la continuidad operativa de la tienda afectada.')
            ->line('Se recomienda ejecutar atención prioritaria para contención y recuperación del servicio.')
            ->line(new HtmlString("<strong>Detalles del Error:</strong><br>{$this->errorMessage}"))
            ->line('Si la incidencia persiste tras la intervención inicial, escalar inmediatamente al equipo técnico.')
            ->action('Acceder al Panel de Tiendas', url('/stores'))
            ->salutation(new HtmlString('Atentamente,<br>Plataforma CMS Locatel'));
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
            'store_name' => $this->storeName,
            'error_message' => $this->errorMessage,
        ];
    }
}
