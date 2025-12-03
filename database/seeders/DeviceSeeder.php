<?php

namespace Database\Seeders;

use App\Models\DevicesModel;
use Database\Factories\DeviceFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DevicesModel::factory()->count(20)->create();
    }
}
