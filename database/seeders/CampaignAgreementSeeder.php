<?php

namespace Database\Seeders;

use App\Models\Agreement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;;

class CampaignAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $campaigns = Campaign::all();
        $agreements = Agreement::all();

        foreach ($campaigns as $campaign) {
            $randomAgreementIds = $agreements->random(rand(1, 3))->pluck('id');

            $campaign->agreements()->attach($randomAgreementIds);
        }
    }
}
