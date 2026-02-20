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
    protected $description = 'Verify active campaigns and finish them if their end date has passed';

    public function handle()
    {
        $this->info('Checking campaigns...');

        $statusActiva = Status::where('status', CampaignStatus::ACTIVE->value)->first();
        $statusFinalizada = Status::where('status', CampaignStatus::FINISHED->value)->first();

        if (!$statusActiva) {
            $this->error(string: 'Error: Status with name "' . CampaignStatus::ACTIVE->value . '" not found in statuses table.');
        }

        if (!$statusFinalizada) {
            $this->error('Error: Status with name "' . CampaignStatus::FINISHED->value . '" not found in statuses table.');
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
            $this->info("Success! {$affectedRows} campaigns have been finished.");
        } else {
            $this->info("No expired campaigns found to finish.");
        }
        $this->info('Process completed.');
    }
}
