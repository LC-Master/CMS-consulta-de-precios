<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StorePlaceholderAction
{
    public function execute(Store $store, UploadedFile $file, ?UploadedFile $thumbnail = null)
    {
        return DB::transaction(function () use ($store, $file, $thumbnail) {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $finalFileName = uniqid() . '_' . $originalName;
            $finalPath = "uploads/{$finalFileName}";

            $tempPath = $file->storeAs('uploads_tmp', $finalFileName, 'local');
            $fullTempPath = Storage::disk('local')->path($tempPath);

            $getID3 = new \getID3();
            $analysis = $getID3->analyze($fullTempPath);

            $media = Media::create([
                'name' => $originalName,
                'disk' => 'public',
                'path' => $finalPath,
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
                'duration_seconds' => str_starts_with($mimeType, 'video/')
                    ? round($analysis['playtime_seconds'] ?? 0)
                    : null,
                'checksum' => md5_file($fullTempPath),
                'created_by' => Auth::id(),
            ]);

            $store->syncState()->update([
                'placeholder_id' => $media->id,
            ]);

            DB::afterCommit(function () use ($tempPath, $finalPath) {
                $local = Storage::disk('local');
                if ($local->exists($tempPath)) {
                    $stream = $local->readStream($tempPath);
                    Storage::disk('public')->writeStream($finalPath, $stream);
                    if (\is_resource($stream))
                        fclose($stream);
                    $local->delete($tempPath);
                }
            });

            return $media;
        });
    }
}