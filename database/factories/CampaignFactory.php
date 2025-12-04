<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\StatusModel;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $startDate = $this->faker->dateTimeBetween('now', '+1 month');
    
    return [
        'campaign_name' => $this->faker->catchPhrase(),
        'start_at' => $startDate,
        // La fecha fin siempre será después de la fecha inicio
        'end_at' => $this->faker->dateTimeBetween($startDate, '+3 months'),
        'status_id' => StatusModel::factory(),
        'department_id' => DepartmentsModel::factory(),
        'agreement_id' => AgreementsModel::factory(),
        'created_by' => $this->faker->numberBetween(1, 10), // IDs de usuario simulados
        'updated_by' => $this->faker->optional()->numberBetween(1, 10),
    ];
}
}
