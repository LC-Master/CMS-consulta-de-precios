<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Store;
use App\Enums\SyncStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class StorePlaceHolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDirectory = 'placeholder';
        $allowedExtensions = ['jpeg', 'jpg', 'webp', 'png', 'gif', 'bmp', 'svg'];

        if (!Storage::disk('public')->exists($baseDirectory)) {
            $this->command->error("ERROR: El directorio no existe en: storage/app/public/{$baseDirectory}");
            $this->command->warn("Por favor, coloca un archivo de imagen dentro de: storage/app/public/{$baseDirectory}/");
            return;
        }

        $files = collect(Storage::disk('public')->allFiles($baseDirectory));
        $imagePath = $files->first(function (string $file) use ($allowedExtensions) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return \in_array($extension, $allowedExtensions, true);
        });

        if (!$imagePath) {
            $this->command->error("ERROR: No se encontró ningún archivo de imagen dentro de storage/app/public/{$baseDirectory}/");
            $this->command->warn("Por favor, coloca un archivo jpeg, jpg, webp u otro formato de imagen en la carpeta mencionada.");
            return;
        }

        $absolutePath = Storage::disk('public')->path($imagePath);
        $mimeType = File::mimeType($absolutePath);
        $fileName = pathinfo($imagePath, PATHINFO_BASENAME);

        $media = Media::create([
            'disk' => 'public',
            'path' => $imagePath,
            'name' => $fileName,
            'mime_type' => $mimeType,
            'size' => Storage::disk('public')->size($imagePath),
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