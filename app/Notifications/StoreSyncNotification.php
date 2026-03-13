<?php

namespace App\Notifications;

use App\Enums\SyncStatusEnum;
use App\Jobs\SendStoreSyncStatusSummaryMailJob;
use Illuminate\Support\Facades\Cache;
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
        public SyncStatusEnum $status,
        public ?array $summaryStores = null
    ) {}

    public static function sendToAdmins(string $storeName, SyncStatusEnum $status): void
    {
        $users = User::role(['admin','supervisor'])->get();

        \Illuminate\Support\Facades\Notification::send(
            $users,
            new self($storeName, $status)
        );

        $statusValue = $status->value;
        $storesKey = "store_sync_summary_mail:{$statusValue}:stores";
        $scheduledKey = "store_sync_summary_mail:{$statusValue}:scheduled";

        $lock = Cache::lock("{$storesKey}:lock", 10);
        try {
            $lock->block(5, function () use ($storesKey, $storeName) {
                $stores = Cache::get($storesKey, []);
                $stores[] = $storeName;
                $stores = array_values(array_unique($stores));
                Cache::put($storesKey, $stores, now()->addMinutes(10));
            });
        } catch (\Throwable $e) {
            $stores = Cache::get($storesKey, []);
            $stores[] = $storeName;
            $stores = array_values(array_unique($stores));
            Cache::put($storesKey, $stores, now()->addMinutes(10));
        }

        if (Cache::add($scheduledKey, true, now()->addMinutes(2))) {
            SendStoreSyncStatusSummaryMailJob::dispatch($statusValue)->delay(now()->addSeconds(30));
        }
    }

    /**
     * @param  array<int, string>  $stores
     */
    public static function summary(SyncStatusEnum $status, array $stores): self
    {
        return new self('Resumen', $status, $stores);
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->summaryStores !== null ? ['mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusValue = $this->status->value;

        if ($this->summaryStores !== null) {
            $translatedStatus = strtoupper($this->getTranslatedStatus($statusValue));

            $mail = (new MailMessage)
                ->subject("Resumen operativo de sincronización ({$translatedStatus})")
                ->greeting('Estimado equipo,')
                ->line("Se consolidaron eventos de sincronización clasificados en el estado: {$translatedStatus}.")
                ->line('Tiendas incluidas en esta consolidación:');

            foreach ($this->summaryStores as $store) {
                $mail->line("• {$store}");
            }

            return $mail
                ->action('Ver listado de tiendas', url('/stores'))
                ->line('Acción sugerida: priorizar revisión de las tiendas listadas y validar su estado actual en el panel.')
                ->salutation('Atentamente, Plataforma CMS Locatel');
        }

        // Configuración visual y semántica según el estado
        $config = match ($statusValue) {
            'success' => [
                'subject' => "✅ Confirmación de sincronización: {$this->storeName}",
                'level' => 'success', 
                'title' => 'Sincronización finalizada correctamente',
                'message' => "La tienda {$this->storeName} completó la sincronización de forma satisfactoria y quedó en estado operativo.",
            ],
            'failed' => [
                'subject' => "🚨 Incidencia crítica de sincronización: {$this->storeName}",
                'level' => 'error', 
                'title' => 'Fallo de sincronización detectado',
                'message' => "La tienda {$this->storeName} reportó un fallo crítico de sincronización. Se recomienda atender el caso con prioridad para evitar impacto operativo.",
            ],
            'stale' => [
                'subject' => "⚠️ Alerta de conectividad/sin reporte: {$this->storeName}",
                'level' => 'error', 
                'title' => 'Tienda sin comunicación reciente',
                'message' => "La tienda {$this->storeName} no registra reportes recientes (estado obsoleto). Validar conectividad y estado del equipo local.",
            ],
            'syncing' => [
                'subject' => "🔄 Sincronización en curso: {$this->storeName}",
                'level' => 'info',
                'title' => 'Proceso de sincronización iniciado',
                'message' => "La tienda {$this->storeName} inició su proceso de sincronización. Se recomienda monitorear hasta su cierre exitoso.",
            ],
            default => [
                'subject' => "Notificación operativa de estado: {$this->storeName}",
                'level' => 'info',
                'title' => 'Actualización de estado de sincronización',
                'message' => "La tienda {$this->storeName} reportó cambio de estado a: " . strtoupper($statusValue),
            ],
        };

        $footerMessage = match ($statusValue) {
            'failed', 'stale' => 'Acción prioritaria: revisar logs técnicos, validar conectividad y ejecutar corrección en el menor tiempo posible.',
            'success' => 'Se mantiene el monitoreo automático para detectar cualquier desviación posterior.',
            'syncing' => 'Este proceso puede tardar algunos minutos según conectividad y volumen de actualización.',
            default => 'Para seguimiento detallado, consulte el panel operativo de tiendas.',
        };

        return (new MailMessage)
            ->level($config['level'])
            ->subject($config['subject'])
                ->greeting('Estimado equipo,')
            ->line("**{$config['title']}**")
            ->line($config['message'])
                ->line("Estado reportado: **" . strtoupper($this->getTranslatedStatus($statusValue)) . "**")
            ->action('Ver Detalles de la Tienda', url('/stores?search=' . urlencode($this->storeName)))
            ->line($footerMessage)
                ->salutation('Atentamente, Plataforma CMS Locatel');
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
        if ($this->summaryStores !== null) {
            return [
                'status' => $this->status->value,
                'stores' => $this->summaryStores,
                'message' => 'Resumen de estados de sincronización agrupados.',
            ];
        }

        return [
            'store_name' => $this->storeName,
            'status' => $this->status->value,
            'message' => "Actualización de estado en {$this->storeName}: {$this->status->value}",
        ];
    }
}
