<?php

namespace App\Http\Controllers;

use App\Actions\dto\CampaignSnapshotDTO;
use App\Models\CenterSnapshot;
use App\Models\StoreSyncState;
use Illuminate\Http\Request;
use App\Events\StoreSyncUpdated;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use App\DTOs\HealthReportDTO;
use App\DTOs\MediaErrorDTO;

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
    public function health(Request $request)
    {
        $request->validate([
            'syncState' => ['required', 'string', 'in:pending,syncing,success,failed,stale'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date'],
            'disk.size' => ['required', 'numeric'],
            'disk.free' => ['required', 'numeric'],
            'disk.used' => ['required', 'numeric'],
            'dtoChanged' => ['required', 'boolean'],
            'uptime' => ['required', 'decimal:1,100'],
            'mediaCount' => ['required', 'integer'],
            'reported_at' => ['nullable', 'date'],
            'mediaError' => ['sometimes', 'array'],
            'mediaError.*.id' => ['required_with:mediaError', 'string'],
            'mediaError.*.name' => ['required_with:mediaError', 'string'],
            'mediaError.*.checksum' => ['required_with:mediaError', 'string'],
            'mediaError.*.errorType' => ['required_with:mediaError', 'string'],
            'mediaError.*.errorCount' => ['required_with:mediaError', 'integer'],
            'mediaError.*.lastErrorAt' => ['required_with:mediaError', 'date'],
        ]);

        \Log::info('point 1', $request->all());

        $report = HealthReportDTO::fromRequest($request);

        try {
            \Log::info('point 2', $request->all());

            /** @var \App\Models\Store $store */
            $store = $request->user();

            StoreSyncState::updateOrCreate(
                [
                    'store_id' => $store->getKey(),
                ],
                [
                    'sync_status' => $report->syncState,
                    'last_synced_at' => $report->endAt ?? $report->startAt,
                    'sync_started_at' => $report->startAt,
                    'sync_ended_at' => $report->endAt,
                    'disk' => json_encode([
                        'size' => $report->disk->size,
                        'free' => $report->disk->free,
                        'used' => $report->disk->used,
                    ]),
                    'uptimed_at' => $report->uptime,
                    'media_count' => $report->mediaCount,
                    'last_reported_at' => $report->reportedAt,
                ]
            );
            \Log::info('point 3', $request->all());

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
            \Log::info('point 4', $request->all());

            StoreSyncUpdated::dispatch('Health check recibida');

            \Log::info('point 5', $request->all());


            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
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
