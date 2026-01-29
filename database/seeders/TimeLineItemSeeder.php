<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Media;
use App\Models\TimeLineItem;

class TimeLineItemSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = Campaign::all();
        $mediaCollection = Media::all();

        if ($campaigns->isEmpty() || $mediaCollection->isEmpty()) {
            $this->command->warn('Debes tener Campañas y Media creados antes de correr TimeLineItemSeeder.');
            return;
        }

        foreach ($campaigns as $campaign) {
            
            $randomMedia = $mediaCollection->random(rand(2, 6));

            $posAm = 1;
            $posPm = 1;

            foreach ($randomMedia as $media) {
                $slot = fake()->randomElement(['am', 'pm']);
                
                $position = ($slot === 'am') ? $posAm++ : $posPm++;

                TimeLineItem::create([
                    'campaign_id' => $campaign->id,
                    'media_id'    => $media->id,
                    'slot'        => $slot,
                    'position'    => $position,
                ]);
            }
        }
    }
}
