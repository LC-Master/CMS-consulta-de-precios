<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Status;
use Illuminate\Console\Command;
use Carbon\Carbon;

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

        $statusActiva = Status::where('status', 'Activa')->first();
        $statusFinalizada = Status::where('status', 'Finalizada')->first();

        if (!$statusActiva) {
            $this->error('Error: No se encontró el estatus con nombre "Activa" en la tabla statuses.');
            return Command::FAILURE;
        }

        if (!$statusFinalizada) {
            $this->error('Error: No se encontró el estatus con nombre "Finalizada" en la tabla statuses.');
            return Command::FAILURE;
        }

        $now = Carbon::now();

        // Buscamos campañas que tengan el UUID de activa y cuya fecha haya vencido
        $affectedRows = Campaign::where('status_id', $statusActiva->id)
            ->where('end_at', '<=', $now)
            ->update(['status_id' => $statusFinalizada->id]);

        if ($affectedRows > 0) {
            $this->info("¡Éxito! Se han finalizado {$affectedRows} campañas.");
        } else {
            $this->info("No se encontraron campañas vencidas para finalizar.");
        }
        
        return Command::SUCCESS;
    }
}