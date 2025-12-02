<?php

namespace Database\Seeders;

use App\Models\StatusModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusModel::create(['status_name' => 'Borrador']);
        StatusModel::create(['status_name' => 'Activo']);
        StatusModel::create(['status_name' => 'Finalizado']);
    }
}
