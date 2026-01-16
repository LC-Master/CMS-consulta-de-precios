<?php
namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreMediaAction
{
    /**
     * Ejecuta la subida masiva de archivos y sus miniaturas.
     */
    public function execute(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $files = $request->file('files');

            $thumbMap = [];
            foreach ($request->file('thumbnails', []) as $thumb) {
                $videoReference = str_replace('-thumb.jpg', '', $thumb->getClientOriginalName());
                $thumbMap[$videoReference] = $thumb;
            }

            $getID3 = new \getID3();

            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $finalFileName = uniqid() . '_' . $originalName;
                $finalPath = "uploads/{$finalFileName}";

                $tempPath = $file->storeAs('uploads_tmp', $finalFileName, 'local');

                $media = Media::create([
                    'name' => $originalName,
                    'disk' => 'public',
                    'path' => $finalPath,
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'duration_seconds' => str_starts_with($mimeType, 'video/')
                        ? round($getID3->analyze(Storage::disk('local')->path($tempPath))['playtime_seconds'] ?? 0)
                        : null,
                    'checksum' => md5_file(Storage::disk('local')->path($tempPath)),
                    'created_by' => Auth::id(),
                ]);

                DB::afterCommit(function () use ($tempPath, $finalPath) {
                    $local = Storage::disk('local');
                    $public = Storage::disk('public');

                    if ($local->exists($tempPath)) {
                        $stream = $local->readStream($tempPath);
                        $public->writeStream($finalPath, $stream);

                        if (\is_resource($stream))
                            fclose($stream);
                        $local->delete($tempPath);
                    }
                });

                if (isset($thumbMap[$originalName])) {
                    $thumbFile = $thumbMap[$originalName];
                    $media->thumbnail()->create([
                        'path' => $thumbFile->store('thumbnails', 'public'),
                        'name' => $thumbFile->getClientOriginalName(),
                        'size' => $thumbFile->getSize(),
                        'mime_type' => $thumbFile->getClientMimeType(),
                        'disk' => 'public',
                    ]);
                }
            }
        });
    }
}