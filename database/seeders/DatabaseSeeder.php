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
        User::factory(10)->create();
        
        $this->call([
            RolesPermissionsSeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            ProviderSeeder::class,
            AgreementSeeder::class,
            CampaignSeeder::class,
            MediaSeeder::class,
            CampaignAgreementSeeder::class,
            TimeLineItemSeeder::class,
            TokenSeeder::class,
            StorePlaceHolderSeeder::class,
        ]);
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        )->assignRole('supervisor');
    }
}
