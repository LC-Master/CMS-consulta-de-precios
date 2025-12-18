<?php

namespace App\Http\Controllers;

use App\Actions\Media\StoreMediaAction;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Media/Index', [
            'medias' => Inertia::scroll(fn () => Media::paginate()),
        ]);
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

            return session()->flash('success', 'Archivos subidos correctamente.');
        } catch (\Throwable $e) {
            return session()->flash('error', 'Error al subir los archivos.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return Inertia::render('Media/Show', [
            'media' => $media,
            'url' => Storage::url($media->path),
        ]);
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
        // Solo actualizamos si el usuario subió un archivo nuevo para reemplazar el anterior
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // 1. Eliminar archivo viejo
            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            // 2. Subir archivo nuevo
            $path = $file->store('uploads', 'public');
            $checksum = md5_file($file->getRealPath());

            // 3. Actualizar BD
            $media->update([
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'checksum' => $checksum,
                // 'created_by' usualmente no se cambia en update
            ]);

            return Redirect::route('media.index')
                ->with('success', 'Archivo reemplazado correctamente.');
        }

        return Redirect::route('media.index')
            ->with('info', 'No se realizaron cambios.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        // 1. Eliminar el archivo físico
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        // 2. Eliminar el registro de la BD
        $media->delete();

        return Redirect::route('media.index')
            ->with('success', 'Archivo eliminado correctamente.');
    }

    public function preview(Media $media)
    {
        $path = Storage::disk('public')->path($media->path);

        if (! Storage::disk('public')->exists($media->path)) {
            abort(404, 'El archivo físico no existe en el servidor.');
        }

        return response()->file($path);
    }
}
