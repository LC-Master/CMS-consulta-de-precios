<?php

namespace Database\Seeders;

use App\Models\Center;
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

        Center::create([
            'name' => 'Todo',
            'code' => 'CTR-0001',
        ]);
        $this->call([
            RolesPermissionsSeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            AgreementSeeder::class,
            DeviceSeeder::class,
            CampaignSeeder::class,
            MediaSeeder::class,
            TimeLineItemSeeder::class,
            TokenSeeder::class,
        ]);
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'status' => 1,
                'email_verified_at' => now(),
            ]
        )->assignRole('admin');
    }
}
