<?php

namespace App\Http\Controllers;

use App\Actions\dto\CampaignSnapshotDTO;
use App\Http\Requests\StoreHealthRequest;
use App\Models\CenterSnapshot;
use App\Models\StoreSyncState;
use Illuminate\Http\Request;
use App\Events\StoreSyncUpdated;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use App\DTOs\HealthReportDTO;
use App\DTOs\MediaErrorDTO;
use Carbon\Carbon;
use App\Notifications\StoreSyncNotification;

class CenterSnapshotController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(CampaignSnapshotDTO $campaignSnapshotDTO, Request $request)
    {
        try {
            $campaignSnapshotDTO = $campaignSnapshotDTO->execute($request->user());
            if (!empty($campaignSnapshotDTO['campaigns'])) {
                $campaignSnapshotDTO['campaigns'] = CampaignSnapshotDTO::normalize($campaignSnapshotDTO['campaigns']);
            }

            $snapShot = CenterSnapshot::updateOrCreate(
                [
                    'store_id' => $request->user()->getKey(),
                ],
                [
                    'snapshot_json' => $campaignSnapshotDTO,
                ]
            );

            return response()->json([
                'meta' => [
                    'api_version' => config('dto.dto_version'),
                    'version' => $snapShot->getAttribute('version_hash'),
                    'generated_at' => now()->toIso8601String(),
                ],
                'data' => $snapShot->snapshot_json,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate snapshot',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function health(StoreHealthRequest $request)
    {

        $report = HealthReportDTO::fromRequest($request);
        try {
            /** @var \App\Models\Store $store */
            $store = $request->user();

            StoreSyncState::updateOrCreate(
                [
                    'store_id' => $store->getKey(),
                ],
                [
                    'last_synced_at' => Carbon::parse($report->endAt ?? $report->startAt)
                        ->setTimezone('America/Caracas')
                        ->format('Y-m-d H:i:s.v'),
                    'sync_started_at' => Carbon::parse($report->startAt)
                        ->setTimezone('America/Caracas')
                        ->format('Y-m-d H:i:s.v'),
                    'sync_ended_at' => $report->endAt
                        ? Carbon::parse($report->endAt)->setTimezone('America/Caracas')->format('Y-m-d H:i:s.v')
                        : null,
                    'uptimed_at' => Carbon::parse($report->uptime)
                        ->setTimezone('America/Caracas')
                        ->format('Y-m-d H:i:s.v'),
                    'last_reported_at' => $report->reportedAt
                        ? Carbon::parse($report->reportedAt)->setTimezone('America/Caracas')->format('Y-m-d H:i:s.v')
                        : null,
                    'disk' => [
                        'size' => $report->disk->size,
                        'free' => $report->disk->free,
                        'used' => $report->disk->used,
                    ],
                    'media_count' => $report->mediaCount,
                ]
            );

            if ($report->communicationKey !== null) {
                StoreSyncState::where('store_id', $store->getKey())->update([
                    'communication_key' => $report->communicationKey,
                ]);
            }

            if (!empty($report->mediaErrors)) {
                $errorsArray = array_map(fn(MediaErrorDTO $e) => [
                    'store_id' => $store->getKey(),
                    'media_id' => $e->id,
                    'name' => $e->name,
                    'checksum' => $e->checksum,
                    'error_type' => $e->error_type,
                    'error_count' => $e->error_count,
                    'last_seen_at' => $e->last_seen_at,
                ], $report->mediaErrors);

                $store->centerMediaErrors()->upsert(
                    $errorsArray,
                    ['media_id'],
                    ['name', 'checksum', 'error_type', 'error_count', 'last_seen_at']
                );
            } else {
                $store->centerMediaErrors()->delete();
            }
            $store->syncState->processHealthReport($report);

            StoreSyncUpdated::dispatch($report->syncState, $store->name);

            StoreSyncNotification::sendToAdmins($store->name, status: $report->syncState);


            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al procesar el informe de estado de salud.', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to update health status',
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }
    public function download(Media $media)
    {
        try {
            $path = Storage::disk($media->disk)->path($media->path);

            if (!Storage::disk($media->disk)->exists($media->path)) {
                logger()->error('El archivo físico no existe en el servidor.', ['media_id' => $media->getKey()]);
                throw new FileNotFoundException('El archivo físico no existe en el servidor.');
            }

            return response()->download($path, $media->name);
        } catch (FileNotFoundException $e) {
            logger()->error('Error al descargar el archivo.', ['media_id' => $media->getKey(), 'error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to download file',
            ], 404);
        } catch (\Throwable $e) {
            logger()->error('Error al descargar el archivo.', ['media_id' => $media->getKey(), 'error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to download file',
            ], 500);
        }
    }
}
