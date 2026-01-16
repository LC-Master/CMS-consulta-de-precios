<?php

namespace App\Actions\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Enums\CampaignStatus;
use App\Models\Media;

class DeleteMediaAction
{
    /**
     * Create a new class instance.
     */
    public function execute(Media $media)
    {
        $isInUse = $media->campaigns()->whereHas('status', function ($query) {
            $query->whereIn('status', [
                CampaignStatus::ACTIVE->value,
                CampaignStatus::DRAFT->value
            ]);
        })->exists();

        if ($isInUse) {
            throw new \App\Exceptions\MediaInUseException();
        }

        DB::transaction(function () use ($media) {
            $thumbnail = $media->thumbnail;
            $thumbnailPath = $thumbnail?->path;
            $mediaPath = $media->path;
            $mediaDisk = $media->disk;

            $media->timeLineItems()->delete();
            $thumbnail?->delete();
            $media->delete();

            if ($thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            Storage::disk($mediaDisk)->delete($mediaPath);
        });
    }
}