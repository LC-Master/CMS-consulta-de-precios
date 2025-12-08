<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Center;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Center::create([
            'name' => 'Todo',
            'code' => 'CTR-0001',
        ])
            ->count(20)
            ->create();
    }
}
