<?php

namespace App\DTOs;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Enums\SyncStatusEnum;
use Illuminate\Http\Request;

readonly class DiskInfo
{
    public function __construct(
        public float $size,
        public float $free,
        public float $used
    ) {
    }
}
readonly class MediaErrorDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $checksum,
        public string $error_type,
        public int $error_count,
        public Carbon $last_seen_at,
    ) {
    }
}
readonly class HealthReportDTO
{
    public function __construct(
        public DiskInfo $disk,
        public Carbon $startAt,
        public ?string $communicationKey,
        public ?Carbon $endAt,
        public SyncStatusEnum $syncState,
        public bool $dtoChanged,
        public string $uptime,
        public int $mediaCount,
        public array $mediaErrors,
        public Carbon $reportedAt,
    ) {
    }
    public static function fromRequest(Request $request): self
    {
        $data = $request->all();

        $disk = new DiskInfo(
            size: (float) ($data['disk']['size'] ?? 0),
            free: (float) ($data['disk']['free'] ?? 0),
            used: (float) ($data['disk']['used'] ?? 0)
        );

        $mediaErrors = array_map(fn($error) => new MediaErrorDTO(
            id: $error['id'],
            name: $error['name'],
            checksum: $error['checksum'],
            error_type: $error['errorType'] ?? 'unknown',
            error_count: (int) ($error['errorCount'] ?? 1),
            last_seen_at: Carbon::parse($error['lastErrorAt'] ?? now()),
        ), $data['mediaError'] ?? []);

        return new self(
            disk: $disk,
            startAt: Carbon::parse($data['start_at']),
            communicationKey: $data['communicationKey'],
            endAt: isset($data['end_at']) ? Carbon::parse($data['end_at']) : null,
            syncState: SyncStatusEnum::from($data['syncState']),
            dtoChanged: (bool) ($data['dtoChanged'] ?? false),
            uptime: self::getUptimeFormatted($data['uptime'] ?? 0),
            mediaCount: (int) ($data['mediaCount'] ?? 0),
            mediaErrors: $mediaErrors,
            reportedAt: Carbon::parse($data['reported_at'] ?? now()),
        );
    }
    public static function getUptimeFormatted($uptime): string
    {
        return now()->subSeconds((float) $uptime)->toIso8601String();
    }
}
