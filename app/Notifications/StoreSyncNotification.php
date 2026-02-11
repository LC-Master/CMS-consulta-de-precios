<?php

namespace App\Notifications;

use App\Enums\SyncStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class StoreSyncNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $storeName,
        public SyncStatusEnum $status
    ) {}
    public static function  sendToAdmins(string $storeName, SyncStatusEnum $status){
        $users = User::role(['admin','supervisor'])->get();

        \Illuminate\Support\Facades\Notification::send(
            $users,
            new self($storeName, $status)
        );
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusValue = $this->status->value;

        // Configuración visual y semántica según el estado
        $config = match ($statusValue) {
            'success' => [
                'subject' => "✅ Sincronización Exitosa: {$this->storeName}",
                'level' => 'success', 
                'title' => '¡Sincronización Completada!',
                'message' => "La tienda {$this->storeName} ha finalizado su proceso de sincronización correctamente. El sistema se encuentra actualizado y operativo.",
            ],
            'failed' => [
                'subject' => "🚨 Alerta Crítica: Fallo en {$this->storeName}",
                'level' => 'error', 
                'title' => 'Error de Sincronización Detectado',
                'message' => "Se han reportado errores graves en {$this->storeName}. Es posible que el contenido multimedia no se esté reproduciendo correctamente. Se requiere atención inmediata.",
            ],
            'stale' => [
                'subject' => "⚠️ Alerta de Conexión: {$this->storeName}",
                'level' => 'error', 
                'title' => 'Sin Comunicación Reciente',
                'message' => "La tienda {$this->storeName} ha dejado de reportar actividad (estado Obsoleto). Verifique si el equipo está encendido y conectado a la red.",
            ],
            'syncing' => [
                'subject' => "🔄 Sincronizando: {$this->storeName}",
                'level' => 'info',
                'title' => 'Sincronización en Curso',
                'message' => "La tienda {$this->storeName} ha comenzado a descargar actualizaciones. Le notificaremos si ocurre algún imprevisto.",
            ],
            default => [
                'subject' => "Notificación de Estado: {$this->storeName}",
                'level' => 'info',
                'title' => 'Actualización de Estado',
                'message' => "El estado de sincronización ha cambiado a: " . strtoupper($statusValue),
            ],
        };

        $footerMessage = match ($statusValue) {
            'failed', 'stale' => 'Si el problema persiste, por favor revise los logs detallados en el panel de administración.',
            'success' => 'El sistema continuará monitoreando la actividad automáticamente.',
            'syncing' => 'Este proceso puede tardar unos minutos dependiendo de la conexión.',
            default => 'Para más información, consulte el panel de control.',
        };

        return (new MailMessage)
            ->level($config['level'])
            ->subject($config['subject'])
            ->greeting("Hola,")
            ->line("**{$config['title']}**")
            ->line($config['message'])
            ->line("Estado reportado: **" . strtoupper($this->getTranslatedStatus($statusValue)) . "**")
            ->action('Ver Detalles de la Tienda', url('/stores?search=' . urlencode($this->storeName)))
            ->line($footerMessage)
            ->salutation('Atentamente, CMS Locatel');
    }

    protected function getTranslatedStatus(string $status): string
    {
        return match ($status) {
            'success' => 'Exitoso',
            'failed' => 'Fallido',
            'stale' => 'Obsoleto',
            'syncing' => 'Sincronizando',
            'pending' => 'Pendiente',
            default => $status,
        };
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
            'status' => $this->status->value,
            'message' => "Actualización de estado en {$this->storeName}: {$this->status->value}",
        ];
    }
}
