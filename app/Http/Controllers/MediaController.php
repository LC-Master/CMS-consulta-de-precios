<?php

namespace App\Http\Controllers;

use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
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
    public function store(StoreMediaRequest $request)
    {

        dd($request);
        // $validated = $request->validated();
        // $files = $request->file('files', []);
        // $metas = [];

        // foreach ($files as $file) {
        //     $path = $file->store('uploads', 'public');
        //     $checksum = md5_file($file->getRealPath());

        //     $media = Media::create([
        //         'path' => $path,
        //         'mime_type' => $file->getClientMimeType(),
        //         'size' => $file->getSize(),
        //         'checksum' => $checksum,
        //         'name' => $file->getClientOriginalName(),
        //         'created_by' => Auth::id(),
        //     ]);

        //     $metas[] = [
        //         'id' => $media->id,
        //         'path' => $path,
        //         'url' => Storage::url($path),
        //         'mime_type' => $media->mime_type,
        //         'size' => $media->size,
        //         'checksum' => $media->checksum,
        //         'original_name' => $media->original_name,
        //         'created_by' => $media->created_by,
        //         'created_at' => $media->created_at,
        //     ];
        // }

        // return response()->json(['files' => $metas], 201);
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
}
