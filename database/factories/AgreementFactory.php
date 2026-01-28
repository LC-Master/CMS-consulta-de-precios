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
        return [
            'name' => $this->faker->unique()->company(), 
            'legal_name' => $this->faker->company() . ' ' . $this->faker->companySuffix(),
            'tax_id' => 'J-' . $this->faker->unique()->numerify('########-#'),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->numerify('4#########'),
            'is_active' => $this->faker->boolean(80),
            'observations' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
