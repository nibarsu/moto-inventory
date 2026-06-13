<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesOrderItemRequest;
use App\Http\Requests\UpdateSalesOrderItemRequest;
use App\Models\Part;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class SalesOrderItemController extends Controller
{
    public function index(SalesOrder $salesOrder)
    {
        $items = $salesOrder->items()
            ->orderBy('id')
            ->paginate(10);

        return view('sales-order-items.index', compact('salesOrder', 'items'));
    }

    public function create(SalesOrder $salesOrder)
    {
        return view('sales-order-items.create', [
            'salesOrder' => $salesOrder,
            'parts' => Part::where('is_active', true)->orderBy('part_no')->get(),
            'vehicles' => Vehicle::where('is_active', true)->orderBy('model_code')->get(),
        ]);
    }

    public function store(StoreSalesOrderItemRequest $request, SalesOrder $salesOrder)
    {
        DB::transaction(function () use ($request, $salesOrder) {
            $validated = $request->validated();
            $itemData = $this->resolveItemData($validated['item_type'], $validated['item_id']);
            $lineTotal = $validated['quantity'] * $validated['unit_price'];

            $salesOrder->items()->create([
                'item_type' => $validated['item_type'],
                'item_id' => $validated['item_id'],
                'item_code' => $itemData['code'],
                'item_name' => $itemData['name'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'line_total' => $lineTotal,
                'remark' => $validated['remark'] ?? null,
            ]);

            $this->syncSalesOrderTotal($salesOrder);
        });

        return redirect()
            ->route('sales-orders.items.index', $salesOrder)
            ->with('success', '銷貨單明細已新增。');
    }

    public function edit(SalesOrder $salesOrder, SalesOrderItem $item)
    {
        abort_unless($item->sales_order_id === $salesOrder->id, 404);

        return view('sales-order-items.edit', [
            'salesOrder' => $salesOrder,
            'item' => $item,
            'parts' => Part::where('is_active', true)->orderBy('part_no')->get(),
            'vehicles' => Vehicle::where('is_active', true)->orderBy('model_code')->get(),
        ]);
    }

    public function update(UpdateSalesOrderItemRequest $request, SalesOrder $salesOrder, SalesOrderItem $item)
    {
        abort_unless($item->sales_order_id === $salesOrder->id, 404);

        DB::transaction(function () use ($request, $salesOrder, $item) {
            $validated = $request->validated();
            $itemData = $this->resolveItemData($validated['item_type'], $validated['item_id']);
            $lineTotal = $validated['quantity'] * $validated['unit_price'];

            $item->update([
                'item_type' => $validated['item_type'],
                'item_id' => $validated['item_id'],
                'item_code' => $itemData['code'],
                'item_name' => $itemData['name'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'line_total' => $lineTotal,
                'remark' => $validated['remark'] ?? null,
            ]);

            $this->syncSalesOrderTotal($salesOrder);
        });

        return redirect()
            ->route('sales-orders.items.index', $salesOrder)
            ->with('success', '銷貨單明細已更新。');
    }

    public function destroy(SalesOrder $salesOrder, SalesOrderItem $item)
    {
        abort_unless($item->sales_order_id === $salesOrder->id, 404);

        DB::transaction(function () use ($salesOrder, $item) {
            $item->delete();
            $this->syncSalesOrderTotal($salesOrder);
        });

        return redirect()
            ->route('sales-orders.items.index', $salesOrder)
            ->with('success', '銷貨單明細已刪除。');
    }

    private function resolveItemData(string $itemType, int $itemId): array
    {
        if ($itemType === 'part') {
            $part = Part::findOrFail($itemId);

            return [
                'code' => $part->part_no,
                'name' => $part->name,
            ];
        }

        $vehicle = Vehicle::findOrFail($itemId);

        return [
            'code' => $vehicle->model_code,
            'name' => $vehicle->name,
        ];
    }

    private function syncSalesOrderTotal(SalesOrder $salesOrder): void
    {
        $salesOrder->update([
            'total_amount' => $salesOrder->items()->sum('line_total'),
        ]);
    }
}
