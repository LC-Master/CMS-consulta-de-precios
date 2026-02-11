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

    protected $description = 'if there are media files that are older than 3 months and not associated with active or draft campaigns, they will be deleted from storage and the database. This command is intended to run monthly to keep the media storage clean and optimized.';

    public function handle()
    {
        $this->info('Starting cleanup of old media files...');

        $cutOffDate = now()->subMonths(3)->startOfDay();

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
            ->with('thumbnail')
            ->get();

        $count = $mediasToDelete->count();

        if ($count === 0) {
            $this->info('No old media files eligible for deletion.');
            return;
        }

        $this->info("Found {$count} media files eligible for deletion.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $deletedCount = 0;
        $errorsCount = 0;
        /** @var \App\Models\Media $media */
        foreach ($mediasToDelete as $media) {
            try {
                if ($media->path && Storage::disk('public')->exists($media->path)) {
                    Storage::disk('public')->delete($media->path);
                }

                if ($media?->thumbnail && Storage::disk('public')->exists($media->thumbnail->path)) {
                    Storage::disk('public')->delete($media->thumbnail->path);
                    $media->thumbnail->delete();
                }

                $media->campaigns()->detach();

                $media->delete();

                $deletedCount++;
            } catch (\Exception $e) {
                Log::error("Error deleting media ID {$media->id}: " . $e->getMessage());
                $errorsCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Cleanup process completed. Deleted: {$deletedCount}. Errors: {$errorsCount}.");

        Log::info("Media cleanup executed. Deleted: {$deletedCount}. Errors: {$errorsCount}.");
    }
}
