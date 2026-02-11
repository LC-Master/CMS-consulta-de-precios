<?php

namespace App\Traits;

use App\DTOs\HealthReportDTO;
use App\Enums\SyncStatusEnum;
use App\Notifications\Alerts\StoreSyncAlertNotification;

trait HasHealthMetrics
{
    /**
     * Procesa el reporte de salud enviado por el CDS.
     */
    public function processHealthReport(HealthReportDTO $data)
    {
        $status = $data->syncState ?? SyncStatusEnum::PENDING->value;
        $error = $data->errorMessage ?? null;

        if ($status === SyncStatusEnum::SUCCESS->value) {
            $this->handleSyncSuccess();
        } elseif ($status === SyncStatusEnum::FAILED->value) {
            $this->handleSyncFailure($error);
        }

        return $this->save();
    }

    protected function handleSyncSuccess()
    {
        $this->sync_status = SyncStatusEnum::SUCCESS->value;
        $this->sync_retries = 0;
        $this->last_sync_error = null;
        $this->last_synced_at = now();
    }

    protected function handleSyncFailure(?string $error)
    {
        $this->sync_status = SyncStatusEnum::FAILED->value;
        $this->last_sync_error = $error;
        $this->last_error_at = now();

        $this->sendCriticalAlert();
    }

    protected function sendCriticalAlert()
    {
        StoreSyncAlertNotification::sendToAdmin($this->store_id, $this->last_sync_error ?? 'Error desconocido');
    }
}