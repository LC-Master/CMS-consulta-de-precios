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
        // Generamos una fecha de inicio en el pasado reciente
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        
        // La fecha fin debe ser en el futuro (entre 1 y 2 años después del inicio)
        $endDate = $this->faker->dateTimeInInterval($startDate, '+2 years');

        return [
            // unique() es vital porque tu migración tiene unique en 'name'
            'name' => $this->faker->unique()->company(), 
            
            'legal_name' => $this->faker->company() . ' ' . $this->faker->companySuffix(),
            
            // Simulamos un RIF formato J-12345678-9
            'tax_id' => 'J-' . $this->faker->unique()->numerify('########-#'),
            
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->numerify('4#########'),
            
            'start_date' => $startDate,
            'end_date' => $endDate,
            
            // 80% de probabilidad de que el convenio esté activo
            'is_active' => $this->faker->boolean(80),
            
            'observations' => $this->faker->optional(0.3)->sentence(), // 30% de probabilidad de tener obs
        ];
    }
}
