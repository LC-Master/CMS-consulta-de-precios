<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Center;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

public function definition(): array
{
    return [
        'center_id' => Center::factory(), 
        'name' => $this->faker->word() . '-' . $this->faker->numerify('##'),
        'type' => $this->faker->randomElement(['Tablet', 'Kiosk', 'Scanner', 'Screen']),
        'description' => $this->faker->sentence(),
        'is_active' => $this->faker->boolean(90), 
    ];
}
}
