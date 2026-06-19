<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarcodeScanRequest;
use App\Models\Part;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class BarcodeScanController extends Controller
{
    public function index(BarcodeScanRequest $request)
    {
        $filters = $request->validated();
        $barcode = trim($filters['barcode'] ?? '');
        $type = $filters['type'] ?? 'all';

        return view('barcode-scans.index', [
            'barcode' => $barcode,
            'type' => $type,
            'results' => $barcode === '' ? collect() : $this->searchResults($barcode, $type),
        ]);
    }

    private function searchResults(string $barcode, string $type): Collection
    {
        $results = collect();

        if (in_array($type, ['all', 'part'], true)) {
            $results = $results->concat($this->partResults($barcode));
        }

        if (in_array($type, ['all', 'vehicle'], true)) {
            $results = $results->concat($this->vehicleResults($barcode));
        }

        return $results->sortBy([
            fn (object $item) => $item->type_sort,
            fn (object $item) => $item->code,
        ])->values();
    }

    private function partResults(string $barcode): Collection
    {
        return Part::query()
            ->with(['brand', 'category'])
            ->where(function ($query) use ($barcode) {
                $query->where('barcode', $barcode)
                    ->orWhere('part_no', $barcode);
            })
            ->orderBy('part_no')
            ->get()
            ->map(function (Part $part) use ($barcode) {
                return (object) [
                    'type' => 'part',
                    'type_label' => '零件',
                    'type_sort' => 1,
                    'code' => $part->part_no,
                    'barcode' => $part->barcode ?: $part->part_no,
                    'matched_by' => $part->barcode === $barcode ? 'barcode' : 'code',
                    'name' => $part->name,
                    'brand' => $part->brand?->name ?: '-',
                    'category' => $part->category?->name ?: '-',
                    'is_active' => (bool) $part->is_active,
                    'show_url' => route('parts.show', $part),
                ];
            });
    }

    private function vehicleResults(string $barcode): Collection
    {
        return Vehicle::query()
            ->with(['brand', 'category'])
            ->where(function ($query) use ($barcode) {
                $query->where('barcode', $barcode)
                    ->orWhere('model_code', $barcode);
            })
            ->orderBy('model_code')
            ->get()
            ->map(function (Vehicle $vehicle) use ($barcode) {
                return (object) [
                    'type' => 'vehicle',
                    'type_label' => '整車',
                    'type_sort' => 2,
                    'code' => $vehicle->model_code,
                    'barcode' => $vehicle->barcode ?: $vehicle->model_code,
                    'matched_by' => $vehicle->barcode === $barcode ? 'barcode' : 'code',
                    'name' => $vehicle->name,
                    'brand' => $vehicle->brand?->name ?: '-',
                    'category' => $vehicle->category?->name ?: '-',
                    'is_active' => (bool) $vehicle->is_active,
                    'show_url' => route('vehicles.show', $vehicle),
                ];
            });
    }
}
