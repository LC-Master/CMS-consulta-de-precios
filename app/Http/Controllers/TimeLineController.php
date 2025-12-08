<?php

namespace App\Http\Controllers;

use App\Models\TimeLineItem;
use App\Models\Campaign;
use App\Models\Media;    
use App\Http\Requests\Timeline\StoreTimeLineItemRequest;
use App\Http\Requests\Timeline\UpdateTimeLineItemRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class TimeLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return Inertia::render('TimeLine/Index', [
            'timelineitem' => Inertia::scroll(fn () => TimeLineItem::with(['campaign','media'])->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('TimeLine/Create', [
            'campaigns' => Campaign::orderBy('title')->get(['id', 'title']), 
            'media' => Media::orderBy('id', 'desc')->get(['id', 'path', 'mime_type']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeLineItemRequest $request)
    {
        TimeLineItem::create($request->validated());

        return Redirect::route('timeline-items.index')
            ->with('success', 'Item programado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeLineItem $timeLineItem)
    {
        $timeLineItem->load(['campaign', 'media']);

        return Inertia::render('TimeLine/Show', [
            'timeLineItem' => $timeLineItem
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeLineItem $timeLineItem)
    {
        return Inertia::render('TimeLine/Edit', [
            'timeLineItem' => $timeLineItem,
            'campaigns' => Campaign::orderBy('name')->get(['id', 'name']),
            'media' => Media::orderBy('id', 'desc')->get(['id', 'path', 'mime_type']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeLineItemRequest $request, TimeLineItem $timeLineItem)
    {
        $timeLineItem->update($request->validated());

        return Redirect::route('timeline.index')
            ->with('success', 'Programación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeLineItem $timeLineItem)
    {
        $timeLineItem->delete();

        return Redirect::route('timeline.index')
            ->with('success', 'Item eliminado de la línea de tiempo.');
    }
}
