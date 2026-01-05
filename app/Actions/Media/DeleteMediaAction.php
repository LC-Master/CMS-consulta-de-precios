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
    public static function execute(Media $media)
    {
        DB::transaction(function () use ($media) {
            $isInUse = $media->campaigns()->whereHas('status', function ($query) {
                $query->whereIn('status', [
                    CampaignStatus::ACTIVE->value,
                    CampaignStatus::DRAFT->value
                ]);
            })->exists();

            if ($isInUse) {
                return back()->with('error', 'La imagen está siendo utilizada en una campaña en borrador o activa.');
            }

            $media->thumbnails()->each(function ($thumbnail) {
                if (Storage::disk('public')->exists($thumbnail->path)) {
                    Storage::disk('public')->delete($thumbnail->path);
                }
                $thumbnail->delete();
            });

            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            $media->delete();
        });
    }
}