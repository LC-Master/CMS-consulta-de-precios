<?php

namespace App\Console\Commands\Campaign;

use Illuminate\Console\Command;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Notifications\Campaigns\CampaignExpiringCritical;
use App\Notifications\Campaigns\CampaignExpiringWarning;

class CheckExpiringCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiring-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para verificar campañas que están por expirar y notificar a los usuarios correspondientes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $eightHours = $now->copy()->addHours(8);
        $twentyFourHours = $now->copy()->addHours(24);

       
        $expiringInTwentyFour = Campaign::query()
            ->whereHas('status', fn($q) => $q->where('status', CampaignStatus::ACTIVE->value))
            ->whereBetween('end_at', [$eightHours, $twentyFourHours])
            ->get();

        if ($expiringInTwentyFour->isNotEmpty()) {
            $this->info('Enviando advertencias de 24 horas...');

            foreach ($expiringInTwentyFour as $campaign) {
                $campaign->user->notify(new CampaignExpiringWarning($campaign));
            }
        }



    }
}