<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryReportIndexRequest;
use App\Models\PartStock;
use App\Models\VehicleStock;
use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InventoryReportController extends Controller
{
    public function index(InventoryReportIndexRequest $request)
    {
        $filters = $request->validated();
        $type = $filters['type'] ?? 'all';
        $warehouseId = $filters['warehouse_id'] ?? null;
        $isActive = $filters['is_active'] ?? 'all';
        $keyword = trim($filters['keyword'] ?? '');

        $items = collect();

        if (in_array($type, ['all', 'part'], true)) {
            $items = $items->concat($this->partItems($keyword, $warehouseId, $isActive));
        }

        if (in_array($type, ['all', 'vehicle'], true)) {
            $items = $items->concat($this->vehicleItems($keyword, $warehouseId, $isActive));
        }

        $items = $items->sortBy([
            fn (object $item) => $item->type_sort,
            fn (object $item) => $item->code,
            fn (object $item) => $item->warehouse,
        ])->values();

        $inventoryReports = $this->paginateCollection($items, 15)->appends($request->query());

        return view('inventory-reports.index', [
            'inventoryReports' => $inventoryReports,
            'type' => $type,
            'warehouseId' => $warehouseId ? (string) $warehouseId : '',
            'isActive' => $isActive,
            'keyword' => $keyword,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    private function partItems(string $keyword, ?int $warehouseId, string $isActive): Collection
    {
        $query = PartStock::query()
            ->with(['part.brand', 'part.category', 'warehouse'])
            ->orderBy('part_id')
            ->orderBy('warehouse_id');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($keyword !== '') {
            $query->whereHas('part', function ($builder) use ($keyword) {
                $builder->where('part_no', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        if ($isActive !== 'all') {
            $query->whereHas('part', fn ($builder) => $builder->where('is_active', $isActive === '1'));
        }

        return $query->get()->map(function (PartStock $stock) {
            $part = $stock->part;
            $averageCost = (float) ($part?->average_cost_price ?? 0);
            $salePrice = (float) ($part?->sale_price ?? 0);

            return (object) [
                'type' => 'part',
                'type_label' => '零件',
                'type_sort' => 1,
                'code' => $part?->part_no ?? '-',
                'name' => $part?->name ?? '-',
                'brand' => $part?->brand?->name ?: '-',
                'category' => $part?->category?->name ?: '-',
                'warehouse' => $stock->warehouse?->name ?: '-',
                'quantity' => (int) $stock->quantity,
                'average_cost_price' => $averageCost,
                'sale_price' => $salePrice,
                'stock_cost_amount' => round($stock->quantity * $averageCost, 2),
                'stock_sale_amount' => round($stock->quantity * $salePrice, 2),
                'is_active' => (bool) ($part?->is_active ?? false),
            ];
        });
    }

    private function vehicleItems(string $keyword, ?int $warehouseId, string $isActive): Collection
    {
        $query = VehicleStock::query()
            ->with(['vehicle.brand', 'vehicle.category', 'warehouse'])
            ->orderBy('vehicle_id')
            ->orderBy('warehouse_id');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($keyword !== '') {
            $query->whereHas('vehicle', function ($builder) use ($keyword) {
                $builder->where('model_code', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        if ($isActive !== 'all') {
            $query->whereHas('vehicle', fn ($builder) => $builder->where('is_active', $isActive === '1'));
        }

        return $query->get()->map(function (VehicleStock $stock) {
            $vehicle = $stock->vehicle;
            $averageCost = (float) ($vehicle?->average_cost_price ?? 0);
            $salePrice = (float) ($vehicle?->sale_price ?? 0);

            return (object) [
                'type' => 'vehicle',
                'type_label' => '整車',
                'type_sort' => 2,
                'code' => $vehicle?->model_code ?? '-',
                'name' => $vehicle?->name ?? '-',
                'brand' => $vehicle?->brand?->name ?: '-',
                'category' => $vehicle?->category?->name ?: '-',
                'warehouse' => $stock->warehouse?->name ?: '-',
                'quantity' => (int) $stock->quantity,
                'average_cost_price' => $averageCost,
                'sale_price' => $salePrice,
                'stock_cost_amount' => round($stock->quantity * $averageCost, 2),
                'stock_sale_amount' => round($stock->quantity * $salePrice, 2),
                'is_active' => (bool) ($vehicle?->is_active ?? false),
            ];
        });
    }

    private function paginateCollection(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }
}
