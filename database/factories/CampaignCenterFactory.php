<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Center;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignCenterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'center_id' => Center::factory(),
        ];
    }
}
