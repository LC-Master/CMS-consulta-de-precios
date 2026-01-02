<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        
        $endDate = $this->faker->dateTimeInInterval($startDate, '+2 years');

        return [
            'name' => $this->faker->unique()->company(), 
            'legal_name' => $this->faker->company() . ' ' . $this->faker->companySuffix(),
            'tax_id' => 'J-' . $this->faker->unique()->numerify('########-#'),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->numerify('4#########'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $this->faker->boolean(80),
            'observations' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
