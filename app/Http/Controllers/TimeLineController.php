<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\TimeLineItem;
use App\Http\Requests\StoreTimeLineItemRequest;
use App\Http\Requests\UpdateTimeLineItemRequest;

class TimeLineController extends Controller
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
        return Inertia::render('Timeline/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeLineItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeLineItem $timeLineItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeLineItem $timeLineItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeLineItemRequest $request, TimeLineItem $timeLineItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeLineItem $timeLineItem)
    {
        //
    }
}
