<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Store;
use App\Enums\SyncStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorePlaceHolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relativePath = 'placeholder/placeholder.webp';

        if (!Storage::disk('public')->exists($relativePath)) {
            $this->command->error("ERROR: El archivo no existe en: storage/app/public/{$relativePath}");
            $this->command->warn("Por favor, coloca el archivo 'placeholder.webp' en la carpeta: storage/app/public/placeholder/");
            return;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        $media = Media::create([
            'disk' => 'public',
            'path' => $relativePath, 
            'name' => 'placeholder.webp',
            'mime_type' => 'image/webp',
            'size' => Storage::disk('public')->size($relativePath),
            'checksum' => md5_file($absolutePath),
            'duration_seconds' => null,
            'created_by' => 1,
        ]);

        $this->command->info("✅ Registro Media creado exitosamente (ID: {$media->id})");

        $stores = Store::all();

        if ($stores->isEmpty()) {
            $this->command->warn("⚠️ No se encontraron tiendas en la base de datos para vincular.");
            return;
        }

        $this->command->info("Vinculando {$stores->count()} tiendas al placeholder...");

        $stores->each(function (Store $store) use ($media) {
            $store->syncState()->updateOrCreate(
                ['store_id' => $store->id], 
                [
                    'placeholder_id' => $media->id,
                    'sync_status' => SyncStatusEnum::PENDING->value,
                    'url' => null,
                    'sync_started_at' => null,
                    'sync_ended_at' => null,
                    'disk' => null,
                    'uptimed_at' => null,
                    'last_synced_at' => null,
                    'last_reported_at' => now(),
                ]
            );
        });

        $this->command->info("🚀 ¡Proceso terminado! Todas las tiendas están en estado PENDING con su placeholder.");
    }
}