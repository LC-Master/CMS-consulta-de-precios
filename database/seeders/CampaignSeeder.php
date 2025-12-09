<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Department;
use App\Models\Agreement;
use App\Models\Center;
use App\Models\CampaignStore;
use App\Models\CampaignLog;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos los catálogos existentes
        $statuses = Status::all();
        $departments = Department::all();
        $agreements = Agreement::all();
        $centers = Center::all();

        Campaign::factory()
            ->count(30)
            ->recycle($statuses)     
            ->recycle($departments)  
            ->recycle($agreements)   
            ->create()              
            ->each(function ($campaign) use ($centers) {
                
                $randomCenters = $centers->random(rand(1, 5));

                foreach ($randomCenters as $center) {
                    CampaignStore::factory()->create([
                        'campaign_id' => $campaign->id,
                        'center_id' => $center->id 
                    ]);
                }

                CampaignLog::factory()->count(rand(0, 5))->create([
                    'campaign_id' => $campaign->id
                ]);
            });
    }
}