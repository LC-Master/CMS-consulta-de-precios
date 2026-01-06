<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThumbnailRequest;
use App\Http\Requests\UpdateThumbnailRequest;
use App\Models\Thumbnail;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThumbnailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Thumbnail $thumbnail)
    {
        $path = Storage::disk('public')->path($thumbnail->getRawOriginal('path'));

        if (!Storage::disk('public')->exists($thumbnail->getRawOriginal('path'))) {
            abort(404, 'El archivo físico no existe en el servidor.');
        }

        return response()->file($path);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thumbnail $thumbnail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThumbnailRequest $request, Thumbnail $thumbnail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thumbnail $thumbnail)
    {
        //
    }
}
