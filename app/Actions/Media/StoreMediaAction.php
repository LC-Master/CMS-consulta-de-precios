<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreMediaAction
{
    public function execute(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $files = $request->file('files');
            $thumbnails = $request->file('thumbnails', []);
            $thumbIndex = 0;

            foreach ($files as $file) {
                $pathTemporal = $file->getRealPath();
                $getID3 = new \getID3();
                $mimeType = $file->getMimeType();
                $finalFileName = uniqid().'_'.$file->getClientOriginalName();
                $finalPath = "uploads/{$finalFileName}";

                $tempPath = $file->storeAs('uploads_tmp', $finalFileName, 'local');

                $media = Media::create([
                    'name' => $file->getClientOriginalName(),
                    'disk' => 'public',
                    'path' => $finalPath,
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'duration_seconds' => str_starts_with($mimeType, 'video/') ? round($getID3->analyze($pathTemporal)['playtime_seconds']) : null,
                    'checksum' => md5_file($file->getRealPath()),
                    'created_by' => Auth::id(),
                ]);

                DB::afterCommit(function () use ($tempPath, $finalPath) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put($finalPath, \Illuminate\Support\Facades\Storage::disk('local')->get($tempPath));
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($tempPath);
                });
                if (isset($thumbnails[$thumbIndex]) && str_starts_with($media->mime_type, 'video/')) {
                    $thumbFile = $thumbnails[$thumbIndex];
                    $thumbPath = $thumbFile->store('thumbnails', 'public');

                    $media->thumbnail()->create([
                        'path' => $thumbPath,
                        'name' => $thumbFile->getClientOriginalName(),
                        'size' => $thumbFile->getSize(),
                        'mime_type' => $thumbFile->getClientMimeType(),
                        'disk' => 'public',
                    ]);
                    $thumbIndex++;
                }
            }
        });
    }
}
