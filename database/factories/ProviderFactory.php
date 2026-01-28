<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    public function definition(): array
    {
        // Generador simple de RIF: J-12345678-9
        $rifType = $this->faker->randomElement(['J', 'G', 'V', 'E']);
        $rifNumber = $this->faker->numberBetween(10000000, 99999999);
        $rifDigit = $this->faker->randomDigit();
        $taxId = "$rifType-$rifNumber-$rifDigit";

        return [
            'name' => $this->faker->company(), // Nombre comercial
            'legal_name' => $this->faker->company() . ' ' . $this->faker->companySuffix(), // Razón social
            'tax_id' => $taxId,
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'is_active' => $this->faker->boolean(90), // 90% de probabilidad de estar activo
        ];
    }
}