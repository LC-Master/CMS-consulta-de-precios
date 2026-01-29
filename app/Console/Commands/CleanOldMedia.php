<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use App\Enums\CampaignStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class CleanOldMedia extends Command
{

    protected $signature = 'media:clean-old';

    protected $description = 'Elimina archivos media y thumbnails de más de 3 meses que no estén asociados a campañas Activas o Borradores.';

    public function handle()
    {
        $this->info('Iniciando limpieza de archivos antiguos...');

        // Definir la fecha de corte (3 meses atrás)
        $cutOffDate = now()->subMonths(3)->startOfDay();

        // (no se debe borrar si está en estos)
        $protectedStatuses = [
            CampaignStatus::DRAFT->value,
            CampaignStatus::ACTIVE->value,
        ];

        $mediasToDelete = Media::where('created_at', '<=', $cutOffDate)
            ->whereDoesntHave('campaigns', function (Builder $query) use ($protectedStatuses) {
                $query->whereNull('deleted_at')
                      ->whereHas('status', function (Builder $q) use ($protectedStatuses) {
                          $q->whereIn('status', $protectedStatuses);
                      });
            })
            ->with('thumbnail') // Cargamos el thumbnail para borrarlo también
            ->get();

        $count = $mediasToDelete->count();

        if ($count === 0) {
            $this->info('No hay archivos antiguos elegibles para eliminar.');
            return;
        }

        $this->info("Se encontraron {$count} archivos elegibles para eliminación.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $deletedCount = 0;
        $errorsCount = 0;

        foreach ($mediasToDelete as $media) {
            try {
                if ($media->path && Storage::disk('public')->exists($media->path)) {
                    Storage::disk('public')->delete($media->path);
                }

                if ($media->thumbnail && $media->thumbnail->path && Storage::disk('public')->exists($media->thumbnail->path)) {
                    Storage::disk('public')->delete($media->thumbnail->path);
                    $media->thumbnail->delete();
                }

                $media->campaigns()->detach();

                $media->delete();

                $deletedCount++;
            } catch (\Exception $e) {
                Log::error("Error eliminando media ID {$media->id}: " . $e->getMessage());
                $errorsCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Proceso finalizado. Eliminados: {$deletedCount}. Errores: {$errorsCount}.");
        
        Log::info("Limpieza de media ejecutada. Eliminados: {$deletedCount}. Errores: {$errorsCount}.");
    }
}
