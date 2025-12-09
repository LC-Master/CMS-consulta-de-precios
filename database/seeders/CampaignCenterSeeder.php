<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Center;
use App\Models\CampaignCenter;
use Illuminate\Database\Seeder;

class CampaignCenterSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = Campaign::all();
        $centers = Center::all();

        if ($campaigns->isEmpty() || $centers->isEmpty()) {
            $this->command->warn('Debes tener Campañas y Centros creados antes de correr este seeder.');
            return;
        }

        foreach ($campaigns as $campaign) {
            
           
            $centersToAssign = $centers->random(rand(1, 3)); 

            foreach ($centersToAssign as $center) {
           
                CampaignCenter::factory()->create([
                    'campaign_id' => $campaign->id,
                    'center_id' => $center->id,
                ]);
            }
        }
    }
}
