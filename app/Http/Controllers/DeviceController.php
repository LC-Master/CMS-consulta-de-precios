<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Center; // Importamos Center para los dropdowns
use App\Http\Requests\Devices\StoreDeviceRequest;
use App\Http\Requests\Devices\UpdateDeviceRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Devices/Index', [
            'devices' => Inertia::scroll(fn () => Device::with(['center'])->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $centers = Center::orderBy('name')->select('id', 'name')->get();

        return Inertia::render('Devices/Create', [
            'centers' => $centers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request)
    {
        Device::create($request->validated());

        return Redirect::route('devices.index')
            ->with('success', 'Dispositivo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        // Cargamos el centro por si queremos mostrar detalles completos
        $device->load('center');

        return Inertia::render('Devices/Show', [
            'device' => $device
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        $centers = Center::orderBy('name')->select('id', 'name')->get();

        return Inertia::render('Devices/Edit', [
            'device' => $device,
            'centers' => $centers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $device->update($request->validated());

        return Redirect::route('devices.index')
            ->with('success', 'Dispositivo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return Redirect::route('devices.index')
            ->with('success', 'Dispositivo eliminado correctamente.');
    }
}