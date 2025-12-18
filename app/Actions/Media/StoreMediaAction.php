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
                $path = $file->store('uploads', 'public');
                $media = Media::create([
                    'name' => $file->getClientOriginalName(),
                    'disk' => 'public',
                    'path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'duration_seconds' => null,
                    'checksum' => md5_file($file->getRealPath()),
                    'created_by' => Auth::id(),
                ]);
                if (isset($thumbnails[$thumbIndex]) && str_starts_with($media->mime_type, 'video/')) {
                    $thumbFile = $thumbnails[$thumbIndex];
                    $thumbPath = $thumbFile->store('thumbnails', 'public');

                    $media->thumbnails()->create([
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
