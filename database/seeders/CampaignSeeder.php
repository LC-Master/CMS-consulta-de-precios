<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampaignsModel;
use App\Models\StatusModel;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use App\Models\CentersModel;
use App\Models\CampaignLogsModel;
use App\Models\CampaignStoresModel;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Obtenemos los IDs existentes para no crear nuevos y llenar la BD de basura
    $statuses = StatusModel::all();
    $departments = DepartmentsModel::all();
    $agreements = AgreementsModel::all();
    $centers = CentersModel::all();

    // Crear 50 campañas
    CampaignsModel::factory()
        ->count(30)
        ->make() // Usamos make() para generar los datos pero interceptarlos antes de guardar
        ->each(function ($campaign) use ($statuses, $departments, $agreements, $centers) {
            
            // Asignar relaciones aleatorias existentes
            $campaign->status_id = $statuses->random()->id;
            $campaign->department_id = $departments->random()->id;
            $campaign->agreement_id = $agreements->random()->id;
            $campaign->save();

            // En lugar de attach, usamos el factory directo de la tabla intermedia
    CampaignStoresModel::factory()->count(rand(1, 5))->create([
        'campaign_id' => $campaign->id,
        // Aquí tomamos un ID de centro al azar
        'center_id' => $centers->random()->id 
    ]);

            // 2. Crear Logs para esta campaña
            CampaignLogsModel::factory()->count(rand(0, 5))->create([
                'campaign_id' => $campaign->id
            ]);
        });
}
}
