<?php

namespace App\Http\Controllers;

use App\Http\Requests\AverageCostIndexRequest;
use App\Models\Part;
use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AverageCostController extends Controller
{
    public function index(AverageCostIndexRequest $request)
    {
        $filters = $request->validated();
        $type = $filters['type'] ?? 'all';
        $keyword = trim($filters['keyword'] ?? '');

        $items = collect();

        if (in_array($type, ['all', 'part'], true)) {
            $items = $items->concat($this->partItems($keyword));
        }

        if (in_array($type, ['all', 'vehicle'], true)) {
            $items = $items->concat($this->vehicleItems($keyword));
        }

        $items = $items->sortBy([
            fn (object $item) => $item->type_sort,
            fn (object $item) => $item->code,
        ])->values();

        $averageCosts = $this->paginateCollection($items, 15)->appends($request->query());

        return view('average-costs.index', [
            'averageCosts' => $averageCosts,
            'type' => $type,
            'keyword' => $keyword,
        ]);
    }

    private function partItems(string $keyword): Collection
    {
        $query = Part::query()
            ->with(['brand', 'category'])
            ->withSum('stocks as total_stock_quantity', 'quantity')
            ->orderBy('part_no');

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('part_no', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        return $query->get()->map(function (Part $part) {
            return (object) [
                'type' => 'part',
                'type_label' => '零件',
                'type_sort' => 1,
                'code' => $part->part_no,
                'name' => $part->name,
                'brand' => $part->brand?->name ?: '-',
                'category' => $part->category?->name ?: '-',
                'total_stock_quantity' => (int) ($part->total_stock_quantity ?? 0),
                'last_cost_price' => (float) $part->last_cost_price,
                'average_cost_price' => (float) $part->average_cost_price,
                'sale_price' => (float) $part->sale_price,
                'is_active' => (bool) $part->is_active,
            ];
        });
    }

    private function vehicleItems(string $keyword): Collection
    {
        $query = Vehicle::query()
            ->with(['brand', 'category'])
            ->withSum('stocks as total_stock_quantity', 'quantity')
            ->orderBy('model_code');

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('model_code', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        return $query->get()->map(function (Vehicle $vehicle) {
            return (object) [
                'type' => 'vehicle',
                'type_label' => '整車',
                'type_sort' => 2,
                'code' => $vehicle->model_code,
                'name' => $vehicle->name,
                'brand' => $vehicle->brand?->name ?: '-',
                'category' => $vehicle->category?->name ?: '-',
                'total_stock_quantity' => (int) ($vehicle->total_stock_quantity ?? 0),
                'last_cost_price' => (float) $vehicle->last_cost_price,
                'average_cost_price' => (float) $vehicle->average_cost_price,
                'sale_price' => (float) $vehicle->sale_price,
                'is_active' => (bool) $vehicle->is_active,
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
