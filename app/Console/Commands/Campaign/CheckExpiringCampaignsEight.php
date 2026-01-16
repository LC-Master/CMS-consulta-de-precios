<?php

namespace App\Console\Commands\Campaign;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Notifications\Campaigns\CampaignExpiringCritical;
use Illuminate\Console\Command;

class CheckExpiringCampaignsEight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiring-campaigns-eight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para verificar campañas que están por expirar en 8 horas y notificar a los usuarios correspondientes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $eightHours = $now->copy()->addHours(8);

        $expiringInEight = Campaign::query()->whereHas('status', fn($q) => $q->where('status', CampaignStatus::ACTIVE->value))
            ->whereBetween('end_at', [$now, $eightHours])
            ->get();
        if ($expiringInEight->isNotEmpty()) {
            $this->info('Enviando alertas críticas de 8 horas...');

            foreach ($expiringInEight as $campaign) {
                $campaign->user->notify(new CampaignExpiringCritical($campaign));
            }
        }
    }
}
