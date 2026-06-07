<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['brand', 'category'])
            ->orderBy('model_code')
            ->paginate(10);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create', [
            'brands' => $this->brands(),
            'categories' => $this->vehicleCategories(),
        ]);
    }

    public function store(StoreVehicleRequest $request)
    {
        Vehicle::create($request->validated());

        return redirect()
            ->route('vehicles.index')
            ->with('success', '整車商品已建立。');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['brand', 'category']);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'brands' => $this->brands(),
            'categories' => $this->vehicleCategories(),
        ]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());

        return redirect()
            ->route('vehicles.index')
            ->with('success', '整車商品已更新。');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', '整車商品已刪除。');
    }

    private function brands()
    {
        return Brand::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function vehicleCategories()
    {
        return Category::where('type', 'vehicle')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
