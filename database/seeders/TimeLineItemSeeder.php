<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Media;
use App\Models\TimeLineItem;
use Illuminate\Database\Seeder;

class TimeLineItemSeeder extends Seeder
{
    public function run(): void
    {
        // $campaigns = Campaign::all();
        // $medias = Media::all();

        // if ($campaigns->isEmpty() || $medias->isEmpty()) {
        //     $this->command->warn('No hay campañas o medios para relacionar. Ejecuta MediaSeeder y CampaignSeeder primero.');
        //     return;
        // }

        // foreach ($campaigns as $campaign) {
            
        //     $numberOfItems = rand(3, 8);

        //     TimeLineItem::factory()
        //         ->count($numberOfItems)
        //         ->state(function (array $attributes) use ($campaign, $medias) {
        //             return [
        //                 'campaign_id' => $campaign->id,
        //                 'media_id' => $medias->random()->id,
        //             ];
        //         })
        //         ->create();
        // }
    }
}
