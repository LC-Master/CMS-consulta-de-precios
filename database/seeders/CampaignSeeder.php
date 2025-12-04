<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Department;
use App\Models\Agreement;
use App\Models\Center;
use App\Models\CampaignStore;
use App\Models\CampaignLog;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos los catálogos existentes
        $statuses = Status::all();
        $departments = Department::all();
        $agreements = Agreement::all();
        $centers = Center::all();

        // 2. Crear 30 campañas
        // USAMOS recycle(): Esto le dice a Laravel: "Usa estos modelos existentes, no crees nuevos"
        // Esto evita el error de "Maximum retries" porque no llama al Faker de departamentos
        Campaign::factory()
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
                    CampaignStore::factory()->create([
                        'campaign_id' => $campaign->id,
                        'center_id' => $center->id 
                    ]);
                }

                // 3. Crear Logs para esta campaña
                CampaignLog::factory()->count(rand(0, 5))->create([
                    'campaign_id' => $campaign->id
                ]);
            });
    }
}