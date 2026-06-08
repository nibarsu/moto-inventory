<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\PartStock;
use App\Models\StockMovement;
use App\Models\Vehicle;
use App\Models\VehicleStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function index()
    {
        $partStocks = PartStock::with(['part', 'warehouse'])->get()->map(function (PartStock $stock) {
            return (object) [
                'type' => 'part',
                'type_label' => '零件',
                'code' => $stock->part?->part_no ?? '-',
                'name' => $stock->part?->name ?? '-',
                'warehouse' => $stock->warehouse?->name ?? '-',
                'quantity' => $stock->quantity,
            ];
        });

        $vehicleStocks = VehicleStock::with(['vehicle', 'warehouse'])->get()->map(function (VehicleStock $stock) {
            return (object) [
                'type' => 'vehicle',
                'type_label' => '整車',
                'code' => $stock->vehicle?->model_code ?? '-',
                'name' => $stock->vehicle?->name ?? '-',
                'warehouse' => $stock->warehouse?->name ?? '-',
                'quantity' => $stock->quantity,
            ];
        });

        $stocks = $partStocks
            ->concat($vehicleStocks)
            ->sortBy(['type', 'code', 'warehouse'])
            ->values();

        return view('stocks.index', compact('stocks'));
    }

    public function movements()
    {
        $movements = StockMovement::with(['warehouse', 'creator'])
            ->latest()
            ->paginate(20);

        $partMap = Part::whereIn('id', $movements->where('item_type', 'part')->pluck('item_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $vehicleMap = Vehicle::whereIn('id', $movements->where('item_type', 'vehicle')->pluck('item_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $movements->getCollection()->transform(function (StockMovement $movement) use ($partMap, $vehicleMap) {
            $item = $movement->item_type === 'part'
                ? $partMap->get($movement->item_id)
                : $vehicleMap->get($movement->item_id);

            $movement->item_code = $movement->item_type === 'part'
                ? ($item?->part_no ?? '-')
                : ($item?->model_code ?? '-');

            $movement->item_name = $item?->name ?? '-';
            $movement->item_type_label = $movement->item_type === 'part' ? '零件' : '整車';

            return $movement;
        });

        return view('stocks.movements', compact('movements'));
    }

    public function adjust()
    {
        return view('stocks.adjust', [
            'parts' => Part::where('is_active', true)->orderBy('part_no')->get(),
            'vehicles' => Vehicle::where('is_active', true)->orderBy('model_code')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateAdjustment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_type' => ['required', 'in:part,vehicle'],
            'item_id' => ['required', 'integer'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'adjusted_quantity' => ['required', 'integer', 'min:0'],
            'remark' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->item_type === 'part' && ! Part::whereKey($request->item_id)->exists()) {
                $validator->errors()->add('item_id', '選擇的零件不存在。');
            }

            if ($request->item_type === 'vehicle' && ! Vehicle::whereKey($request->item_id)->exists()) {
                $validator->errors()->add('item_id', '選擇的整車不存在。');
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($validated, $request) {
            $itemType = $validated['item_type'];
            $warehouseId = $validated['warehouse_id'];
            $itemId = $validated['item_id'];
            $afterQuantity = $validated['adjusted_quantity'];

            if ($itemType === 'part') {
                $stock = PartStock::firstOrCreate(
                    ['part_id' => $itemId, 'warehouse_id' => $warehouseId],
                    ['quantity' => 0]
                );
            } else {
                $stock = VehicleStock::firstOrCreate(
                    ['vehicle_id' => $itemId, 'warehouse_id' => $warehouseId],
                    ['quantity' => 0]
                );
            }

            $beforeQuantity = $stock->quantity;
            $difference = $afterQuantity - $beforeQuantity;

            $stock->update([
                'quantity' => $afterQuantity,
            ]);

            StockMovement::create([
                'item_type' => $itemType,
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'movement_type' => 'adjust',
                'quantity' => $difference,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'remark' => $validated['remark'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
        });

        return redirect()
            ->route('stocks.adjust')
            ->with('success', '庫存已調整完成。');
    }
}
