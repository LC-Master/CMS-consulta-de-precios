<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Media;
use App\Models\TimeLineItem;
use Illuminate\Database\Seeder;

class TimeLineItemSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos todas las campañas y todos los medios existentes
        $campaigns = Campaign::all();
        $medias = Media::all();

        // Validamos que existan datos para evitar errores
        if ($campaigns->isEmpty() || $medias->isEmpty()) {
            $this->command->warn('No hay campañas o medios para relacionar. Ejecuta MediaSeeder y CampaignSeeder primero.');
            return;
        }

        // Recorremos cada campaña para agregarle items en su línea de tiempo
        foreach ($campaigns as $campaign) {
            
            // Decidimos al azar cuantos items tendrá esta campaña (ej. entre 3 y 8)
            $numberOfItems = rand(3, 8);

            TimeLineItem::factory()
                ->count($numberOfItems)
                ->state(function (array $attributes) use ($campaign, $medias) {
                    return [
                        'campaign_id' => $campaign->id,
                        // Asignamos un medio aleatorio de los que ya existen (evita crear nuevos)
                        'media_id' => $medias->random()->id,
                    ];
                })
                ->create();
        }
    }
}
