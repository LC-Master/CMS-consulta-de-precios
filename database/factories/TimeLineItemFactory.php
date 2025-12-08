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
            // Laravel intentará crear una Campaign y un Media si no se pasan al llamar al factory
            'campaign_id' => Campaign::factory(),
            'media_id' => Media::factory(),
            'scheduled_at' => $this->faker->time('H:i:s'),
        ];
    }
}