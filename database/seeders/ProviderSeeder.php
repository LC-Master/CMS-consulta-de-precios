<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 15 proveedores aleatorios
        Provider::factory(15)->create();

        Provider::factory()->create([
            'name' => 'Proveedor de Prueba',
            'legal_name' => 'Inversiones Prueba, C.A.',
            'tax_id' => 'J-00000000-1',
            'is_active' => true,
        ]);
    }
}