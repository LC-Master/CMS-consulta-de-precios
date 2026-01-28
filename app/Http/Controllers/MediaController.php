<?php

namespace App\Http\Controllers;

use App\Actions\Media\StoreMediaAction;
use App\Enums\CampaignStatus;
use App\Handler\Media\MediaSafeAction;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use App\Enums\MimeTypesEnum;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Media\DeleteMediaAction;
use App\Actions\Media\IndexMediaAction;


/**
 * MediaController
 * @package App\Http\Controllers
 * @author  Francisco Rojas / Ben Pulido
 * @version 1.0
 * Controlador para la gestión de archivos multimedia.
 */
class MediaController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:media.list', only: ['index']),
            new Middleware('permission:media.show', only: ['show']),
            new Middleware('permission:media.create', only: ['store']),
            new Middleware('permission:media.update', only: ['update']),
            new Middleware('permission:media.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, IndexMediaAction $indexMediaAction)
    {
        return MediaSafeAction::SafeAction(function () use ($request, $indexMediaAction) {
            $medias = $indexMediaAction->list($request);
            logger()->info('Media index loaded successfully.', ['user_id' => $request->user()?->id ?? null]);
            return Inertia::render('Media/Index', [
                'medias' => $medias,
                'filters' => $request->only(['search', 'type']),
                'mimeTypes' => MimeTypesEnum::values(),
            ]);
        }, 'Ocurrió un error al listar los archivos.');
    }


    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     return Inertia::render('Media/Create');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request, StoreMediaAction $storeMediaAction)
    {
        return MediaSafeAction::SafeAction(function () use ($request, $storeMediaAction) {
            $request->validated();
            $storeMediaAction->execute($request);
            logger()->info('Media files uploaded successfully.', ['user_id' => $request->user()->id]);
            return back()->with('success', 'Archivos subidos correctamente.');
        }, 'Error al subir los archivos.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return MediaSafeAction::SafeAction(function () use ($media) {
            $media->load([
                'campaigns' => function ($query) {
                    $query->whereHas('status', function ($subQuery) {
                        $subQuery->whereIn('status', [
                            CampaignStatus::ACTIVE->value,
                            CampaignStatus::DRAFT->value,
                        ]);
                    });
                },
                'thumbnail',
                'timeLineItems'
            ]);

            logger()->info('Media details loaded.', ['id' => $media->id]);

            return Inertia::render('Media/Show', [
                'media' => $media,
            ]);
        }, 'No se pudieron cargar los detalles del archivo.');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media, DeleteMediaAction $deleteMediaAction)
    {
        return MediaSafeAction::SafeAction(function () use ($media, $deleteMediaAction) {
            $deleteMediaAction->execute($media);
            logger()->info('Media file deleted successfully.', ['media_id' => $media->getKey()]);
            return back()->with('success', 'Archivo eliminado correctamente.');
        }, 'Ocurrió un error al eliminar el archivo.');
    }

    public function preview(Media $media)
    {
        try {
            $path = Storage::disk($media->disk)->path($media->path);

            if (!Storage::disk($media->disk)->exists($media->path)) {
                logger()->error('El archivo físico no existe en el servidor.', ['media_id' => $media->getKey()]);
                throw new FileNotFoundException('El archivo físico no existe en el servidor.');
            }

            return response()->file($path);
        } catch (\Throwable $e) {
            logger()->error('Error al previsualizar el archivo.', ['media_id' => $media->getKey(), 'error' => $e->getMessage()]);
            return null;
        }

    }
    public function download(Media $media)
    {
        return MediaSafeAction::SafeAction(function () use ($media) {
            $path = Storage::disk($media->disk)->path($media->path);

            if (!Storage::disk($media->disk)->exists($media->path)) {
                logger()->error('El archivo físico no existe en el servidor.', ['media_id' => $media->getKey()]);
                throw new FileNotFoundException('El archivo físico no existe en el servidor.');
            }

            return response()->download($path, $media->name);
        }, 'Ocurrió un error al descargar el archivo.');

    }
}
