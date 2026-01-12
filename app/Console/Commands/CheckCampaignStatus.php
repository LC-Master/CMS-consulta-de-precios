<?php

namespace App\Console\Commands;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Status;
use Illuminate\Console\Command;

class CheckCampaignStatus extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'campaigns:check';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Verifica las campañas activas y las finaliza si su fecha de fin ha pasado';

    public function handle()
    {
        $this->info('Comprobando campañas...');

        $statusActiva = Status::where('status', CampaignStatus::ACTIVE->value)->first();
        $statusFinalizada = Status::where('status', CampaignStatus::FINISHED->value)->first();

        if (!$statusActiva) {
            $this->error(string: 'Error: No se encontró el estatus con nombre "' . CampaignStatus::ACTIVE->value . '" en la tabla statuses.');
        }

        if (!$statusFinalizada) {
            $this->error('Error: No se encontró el estatus con nombre "' . CampaignStatus::FINISHED->value . '" en la tabla statuses.');
        }

        $now = now();
        $campaigns = Campaign::where('status_id', $statusActiva->getKey())
            ->where('end_at', '<=', $now)
            ->get();

        $affectedRows = $campaigns->count();

        foreach ($campaigns as $campaign) {
            $campaign->setAttribute('status_id', $statusFinalizada->getKey());
            $campaign->save();

            if (method_exists($campaign, 'user')) {
                $campaign->user->notify(new \App\Notifications\Campaigns\CampaignFinishedNotification($campaign));
            }
        }

        if ($affectedRows > 0) {
            $this->info("¡Éxito! Se han finalizado {$affectedRows} campañas.");
        } else {
            $this->info("No se encontraron campañas vencidas para finalizar.");
        }
        $this->info('Proceso completado.');
    }
}
