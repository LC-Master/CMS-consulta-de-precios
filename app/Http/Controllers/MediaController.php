<?php

namespace App\Http\Controllers;

use App\Actions\Media\StoreMediaAction;
use App\Enums\CampaignStatus;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use App\Enums\MimeTypesEnum;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Media::query()->with([
            'campaigns' => function ($q) {
                $q->select('campaigns.*')->distinct();
            }
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('campaigns', function ($qCamp) use ($search) {
                        $qCamp->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('mime_type', $request->type);
        }

        return Inertia::render('Media/Index', [
            'medias' => Inertia::scroll(fn() => $query->select('id', 'name', 'duration_seconds', 'mime_type', 'size')->latest()->paginate(20)->withQueryString()),
            'filters' => $request->only(['search', 'type']),
            'mimeTypes' => MimeTypesEnum::values(),
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

            return back()->with('success', 'Archivos subidos correctamente.');
        } catch (\Throwable $e) {
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
                'campaigns' => function ($query) {
                    $query->whereHas('status', function ($q) {
                        $q->whereIn('status', [
                            CampaignStatus::ACTIVE->value,
                            CampaignStatus::DRAFT->value,
                        ]);
                    });
                }
            ]);

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
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            $path = $file->store('uploads', 'public');
            $checksum = md5_file($file->getRealPath());

            $media->update([
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'checksum' => $checksum,
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
        try {
            $isInUse = $media->campaigns()->whereHas('status', function ($query) {
                $query->whereIn('status', [
                    CampaignStatus::ACTIVE->value,
                    CampaignStatus::DRAFT->value
                ]);
            })->exists();

            if ($isInUse) {
                return back()->with('error', 'La imagen está siendo utilizada en una campaña en borrador o activa.');
            }

            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            $media->delete();

            return back()->with('success', 'Archivo eliminado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ocurrió un error al eliminar el archivo.');
        }
    }

    public function preview(Media $media)
    {
        try {
            $path = Storage::disk('public')->path($media->path);

            if (!Storage::disk('public')->exists($media->path)) {
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
            $path = Storage::disk('public')->path($media->path);

            if (!Storage::disk('public')->exists($media->path)) {
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
