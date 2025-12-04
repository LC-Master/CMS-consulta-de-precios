<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            RolesPermissionsSeeder::class,
            // 1. Catálogos Base (Sin dependencias)
            StatusSeeder::class,
            DepartmentSeeder::class,
            AgreementSeeder::class,
            DeviceSeeder::class,
            // 2. Estructura Física (Centros y Dispositivos)
            CenterSeeder::class, // Este crea devices internamente
            
            // 3. Operaciones (Campañas, pivotes y logs)
            CampaignSeeder::class, 
        ]);
        
    }
}
