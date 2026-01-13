<?php

namespace Database\Factories;

use App\Models\Agreement;
use App\Models\Department;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            'title' => $this->faker->sentence(),
            'start_at' => $startDate,
            'end_at' => $this->faker->dateTimeBetween($startDate, '+3 months'),
            'status_id' => Status::factory(),
            'department_id' => Department::factory(),
            'agreement_id' => Agreement::factory(),

            /*'user_id' => $this->faker->numberBetween(1, 10),
            'updated_by' => $this->faker->optional()->numberBetween(1, 10), */

            'user_id' => User::factory(), 
            'updated_by' => User::factory(),
        ];
    }
}
