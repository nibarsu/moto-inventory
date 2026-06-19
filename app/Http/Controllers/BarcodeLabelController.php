<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarcodeLabelIndexRequest;
use App\Http\Requests\BarcodeLabelPrintRequest;
use App\Models\Part;
use App\Models\Vehicle;
use App\Support\Code39Barcode;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class BarcodeLabelController extends Controller
{
    public function index(BarcodeLabelIndexRequest $request)
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

        return view('barcode-labels.index', [
            'items' => $this->paginateCollection($items, 15)->appends($request->query()),
            'type' => $type,
            'keyword' => $keyword,
        ]);
    }

    public function print(BarcodeLabelPrintRequest $request)
    {
        $validated = $request->validated();
        $requestedItems = collect($validated['items']);

        $parts = Part::query()
            ->whereIn('id', $requestedItems->where('type', 'part')->pluck('id')->unique())
            ->get()
            ->keyBy('id');

        $vehicles = Vehicle::query()
            ->whereIn('id', $requestedItems->where('type', 'vehicle')->pluck('id')->unique())
            ->get()
            ->keyBy('id');

        $labels = collect();

        foreach ($requestedItems as $item) {
            $model = $item['type'] === 'part'
                ? $parts->get($item['id'])
                : $vehicles->get($item['id']);

            if (! $model) {
                continue;
            }

            $code = $item['type'] === 'part' ? $model->part_no : $model->model_code;
            $barcodeValue = trim($model->barcode ?: $code);

            try {
                $barcode = Code39Barcode::normalize($barcodeValue);
                $svg = Code39Barcode::svg($barcode);
            } catch (InvalidArgumentException) {
                continue;
            }

            $quantity = (int) $item['quantity'];

            for ($copy = 0; $copy < $quantity; $copy++) {
                $labels->push((object) [
                    'type' => $item['type'],
                    'type_label' => $item['type'] === 'part' ? '零件' : '整車',
                    'code' => $code,
                    'name' => $model->name,
                    'barcode' => $barcode,
                    'svg' => $svg,
                ]);
            }
        }

        if ($labels->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => '找不到可列印的條碼資料，請確認商品條碼或代碼內容。',
            ]);
        }

        return view('barcode-labels.print', [
            'labels' => $labels,
            'printedAt' => now(),
        ]);
    }

    private function partItems(string $keyword): Collection
    {
        $query = Part::query()
            ->where('is_active', true)
            ->orderBy('part_no');

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('part_no', 'like', "%{$keyword}%")
                    ->orWhere('barcode', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        return $query->get()->map(function (Part $part) {
            return (object) [
                'id' => $part->id,
                'type' => 'part',
                'type_label' => '零件',
                'type_sort' => 1,
                'code' => $part->part_no,
                'barcode' => $part->barcode ?: $part->part_no,
                'name' => $part->name,
            ];
        });
    }

    private function vehicleItems(string $keyword): Collection
    {
        $query = Vehicle::query()
            ->where('is_active', true)
            ->orderBy('model_code');

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('model_code', 'like', "%{$keyword}%")
                    ->orWhere('barcode', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        return $query->get()->map(function (Vehicle $vehicle) {
            return (object) [
                'id' => $vehicle->id,
                'type' => 'vehicle',
                'type_label' => '整車',
                'type_sort' => 2,
                'code' => $vehicle->model_code,
                'barcode' => $vehicle->barcode ?: $vehicle->model_code,
                'name' => $vehicle->name,
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
