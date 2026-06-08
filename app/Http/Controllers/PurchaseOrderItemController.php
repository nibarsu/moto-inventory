<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderItemRequest;
use App\Http\Requests\UpdatePurchaseOrderItemRequest;
use App\Models\Part;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class PurchaseOrderItemController extends Controller
{
    public function index(PurchaseOrder $purchaseOrder)
    {
        $items = $purchaseOrder->items()
            ->orderBy('id')
            ->paginate(10);

        return view('purchase-order-items.index', compact('purchaseOrder', 'items'));
    }

    public function create(PurchaseOrder $purchaseOrder)
    {
        return view('purchase-order-items.create', [
            'purchaseOrder' => $purchaseOrder,
            'parts' => Part::where('is_active', true)->orderBy('part_no')->get(),
            'vehicles' => Vehicle::where('is_active', true)->orderBy('model_code')->get(),
        ]);
    }

    public function store(StorePurchaseOrderItemRequest $request, PurchaseOrder $purchaseOrder)
    {
        DB::transaction(function () use ($request, $purchaseOrder) {
            $itemData = $this->resolveItemData($request->validated()['item_type'], $request->validated()['item_id']);
            $lineTotal = $request->validated()['quantity'] * $request->validated()['unit_price'];

            $purchaseOrder->items()->create([
                'item_type' => $request->validated()['item_type'],
                'item_id' => $request->validated()['item_id'],
                'item_code' => $itemData['code'],
                'item_name' => $itemData['name'],
                'quantity' => $request->validated()['quantity'],
                'unit_price' => $request->validated()['unit_price'],
                'line_total' => $lineTotal,
                'remark' => $request->validated()['remark'] ?? null,
            ]);

            $this->syncPurchaseOrderTotal($purchaseOrder);
        });

        return redirect()
            ->route('purchase-orders.items.index', $purchaseOrder)
            ->with('success', '進貨單明細已建立。');
    }

    public function edit(PurchaseOrder $purchaseOrder, PurchaseOrderItem $item)
    {
        abort_unless($item->purchase_order_id === $purchaseOrder->id, 404);

        return view('purchase-order-items.edit', [
            'purchaseOrder' => $purchaseOrder,
            'item' => $item,
            'parts' => Part::where('is_active', true)->orderBy('part_no')->get(),
            'vehicles' => Vehicle::where('is_active', true)->orderBy('model_code')->get(),
        ]);
    }

    public function update(UpdatePurchaseOrderItemRequest $request, PurchaseOrder $purchaseOrder, PurchaseOrderItem $item)
    {
        abort_unless($item->purchase_order_id === $purchaseOrder->id, 404);

        DB::transaction(function () use ($request, $purchaseOrder, $item) {
            $itemData = $this->resolveItemData($request->validated()['item_type'], $request->validated()['item_id']);
            $lineTotal = $request->validated()['quantity'] * $request->validated()['unit_price'];

            $item->update([
                'item_type' => $request->validated()['item_type'],
                'item_id' => $request->validated()['item_id'],
                'item_code' => $itemData['code'],
                'item_name' => $itemData['name'],
                'quantity' => $request->validated()['quantity'],
                'unit_price' => $request->validated()['unit_price'],
                'line_total' => $lineTotal,
                'remark' => $request->validated()['remark'] ?? null,
            ]);

            $this->syncPurchaseOrderTotal($purchaseOrder);
        });

        return redirect()
            ->route('purchase-orders.items.index', $purchaseOrder)
            ->with('success', '進貨單明細已更新。');
    }

    public function destroy(PurchaseOrder $purchaseOrder, PurchaseOrderItem $item)
    {
        abort_unless($item->purchase_order_id === $purchaseOrder->id, 404);

        DB::transaction(function () use ($purchaseOrder, $item) {
            $item->delete();
            $this->syncPurchaseOrderTotal($purchaseOrder);
        });

        return redirect()
            ->route('purchase-orders.items.index', $purchaseOrder)
            ->with('success', '進貨單明細已刪除。');
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

    private function syncPurchaseOrderTotal(PurchaseOrder $purchaseOrder): void
    {
        $purchaseOrder->update([
            'total_amount' => $purchaseOrder->items()->sum('line_total'),
        ]);
    }
}
