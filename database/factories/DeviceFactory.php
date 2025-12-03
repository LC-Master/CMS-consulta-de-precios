<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CentersModel; // Importar modelo

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
        'center_id' => CentersModel::factory(), 
        'device_name' => $this->faker->word() . '-' . $this->faker->numerify('##'),
        'device_type' => $this->faker->randomElement(['Tablet', 'Kiosk', 'Scanner', 'Screen']),
        'description' => $this->faker->sentence(),
        'isactive' => $this->faker->boolean(90), // 90% de probabilidad de ser true
    ];
}
}
