<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CampaignsModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CampaignLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'campaign_id' => CampaignsModel::factory(),
        'message' => $this->faker->realText(100),
        'level' => $this->faker->randomElement(['INFO', 'WARNING', 'ERROR', 'DEBUG']),
    ];
}
}
