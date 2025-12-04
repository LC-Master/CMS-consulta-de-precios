<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampaignsModel;
use App\Models\StatusModel;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use App\Models\CentersModel;
use App\Models\CampaignStoresModel;
use App\Models\CampaignLogsModel;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos los catálogos existentes
        $statuses = StatusModel::all();
        $departments = DepartmentsModel::all();
        $agreements = AgreementsModel::all();
        $centers = CentersModel::all();

        // 2. Crear 30 campañas
        // USAMOS recycle(): Esto le dice a Laravel: "Usa estos modelos existentes, no crees nuevos"
        // Esto evita el error de "Maximum retries" porque no llama al Faker de departamentos
        CampaignsModel::factory()
            ->count(30)
            ->recycle($statuses)     // Recicla estatus existentes
            ->recycle($departments)  // Recicla departamentos existentes
            ->recycle($agreements)   // Recicla acuerdos existentes
            ->create()               // Usamos create directo (ya no make) porque recycle asigna los IDs
            ->each(function ($campaign) use ($centers) {
                
                // --- CORRECCIÓN DE LA TABLA PIVOTE (CampaignStores) ---
                
                // ERROR ANTERIOR: $centers->random()->id se ejecutaba UNA VEZ y se repetía.
                // CORRECCIÓN: Tomamos X centros aleatorios ÚNICOS y los iteramos.
                
                $randomCenters = $centers->random(rand(1, 5));

                foreach ($randomCenters as $center) {
                    CampaignStoresModel::factory()->create([
                        'campaign_id' => $campaign->id,
                        'center_id' => $center->id 
                    ]);
                }

                // 3. Crear Logs para esta campaña
                CampaignLogsModel::factory()->count(rand(0, 5))->create([
                    'campaign_id' => $campaign->id
                ]);
            });
    }
}