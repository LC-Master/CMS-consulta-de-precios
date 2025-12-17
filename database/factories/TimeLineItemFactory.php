<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'media_id' => Media::factory(),
        ];
    }
}
