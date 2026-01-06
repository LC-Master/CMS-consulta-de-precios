<?php

namespace App\Http\Controllers;

use App\Actions\Media\StoreMediaAction;
use App\Enums\CampaignStatus;
use App\Handler\Media\MediaSafeAction;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use App\Enums\MimeTypesEnum;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Media\DeleteMediaAction;
use App\Actions\Media\IndexMediaAction;
class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, IndexMediaAction $indexMediaAction)
    {
        try {
            $medias = $indexMediaAction->list($request);
            logger()->info('Media index loaded successfully.', ['user_id' => $request->user()?->id ?? null]);
            return Inertia::render('Media/Index', [
                'medias' => $medias,
                'filters' => $request->only(['search', 'type']),
                'mimeTypes' => MimeTypesEnum::values(),
            ]);
        } catch (\Throwable $e) {
            logger()->critical('Error loading media index: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al cargar los archivos.');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Media/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request, StoreMediaAction $storeMediaAction)
    {
        try {
            $request->validated();
            $storeMediaAction->execute($request);
            logger()->info('Media files uploaded successfully.', ['user_id' => $request->user()->id]);
            return back()->with('success', 'Archivos subidos correctamente.');
        } catch (\Throwable $e) {
            logger()->error('Error uploading media files: ' . $e->getMessage());
            return back()->with('error', 'Error al subir los archivos.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        try {
            $media->load([
                'campaigns' => function ($q) {
                    $q->select('campaigns.*')->distinct()
                        ->whereHas('status', function ($q2) {
                            $q2->whereIn('status', [
                                CampaignStatus::ACTIVE->value,
                                CampaignStatus::DRAFT->value,
                            ]);
                        });
                }
            ]);

            logger()->info('Media details loaded successfully.', ['media_id' => $media->id]);
            return Inertia::render('Media/Show', [
                'media' => $media,
            ]);
        } catch (\Throwable $e) {
            logger()->error('Error loading media details: ' . $e->getMessage(), ['media_id' => $media->id]);
            return back()->with('error', 'Ocurrió un error al cargar el archivo.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        return Inertia::render('Media/Edit', [
            'media' => $media,
            'url' => Storage::url($media->path),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                if (Storage::disk($media->disk)->exists($media->path)) {
                    Storage::disk($media->disk)->delete($media->path);
                }

                $path = $file->store('uploads', 'public');

                $media->update([
                    'path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'checksum' => md5_file($file->getRealPath()),
                ]);

                return to_route('media.index')
                    ->with('success', 'Archivo reemplazado correctamente.');
            }

            return to_route('media.index')
                ->with('info', 'No se realizaron cambios.');
        } catch (\Throwable $e) {
            logger()->error('Error updating media file: ' . $e->getMessage(), ['media_id' => $media->id]);
            return back()->with('error', 'Ocurrió un error al actualizar el archivo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media, DeleteMediaAction $deleteMediaAction)
    {
        MediaSafeAction::MediaSafeAction(function () use ($media, $deleteMediaAction) {
            $deleteMediaAction->execute($media);
            logger()->info('Media file deleted successfully.', ['media_id' => $media->id]);
            return back()->with('success', 'Archivo eliminado correctamente.');
        });
    }

    public function preview(Media $media)
    {
        try {
            $path = Storage::disk($media->disk)->path($media->path);

            if (!Storage::disk($media->disk)->exists($media->path)) {
                logger()->error('El archivo físico no existe en el servidor.', ['media_id' => $media->id]);
                abort(404, 'El archivo físico no existe en el servidor.');
            }

            return response()->file($path);
        } catch (\Throwable $e) {
            logger()->error('Error generating media preview: ' . $e->getMessage(), ['media_id' => $media->id]);
            abort(500, 'Ocurrió un error al generar la vista previa del archivo.');
        }
    }
    public function download(Media $media)
    {
        try {
            $path = Storage::disk($media->disk)->path($media->path);

            if (!Storage::disk($media->disk)->exists($media->path)) {
                logger()->error('El archivo físico no existe en el servidor.', ['media_id' => $media->id]);
                abort(404, 'El archivo físico no existe en el servidor.');
            }

            return response()->download($path, $media->name);
        } catch (\Throwable $e) {
            logger()->error('Error downloading media file: ' . $e->getMessage(), ['media_id' => $media->id]);
            abort(500, 'Ocurrió un error al descargar el archivo.');
        }
    }
}
