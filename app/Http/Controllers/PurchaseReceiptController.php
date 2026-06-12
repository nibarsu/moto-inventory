<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseReceiptRequest;
use App\Http\Requests\UpdatePurchaseReceiptRequest;
use App\Models\Part;
use App\Models\PartStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceipt;
use App\Models\StockMovement;
use App\Models\Vehicle;
use App\Models\VehicleStock;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptController extends Controller
{
    public function index()
    {
        $purchaseReceipts = PurchaseReceipt::with(['purchaseOrder', 'supplier', 'warehouse'])
            ->orderByDesc('receipt_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('purchase-receipts.index', compact('purchaseReceipts'));
    }

    public function create(Request $request)
    {
        $purchaseOrders = $this->eligiblePurchaseOrders();
        $selectedPurchaseOrder = null;
        $remainingItems = collect();

        if ($request->filled('purchase_order_id')) {
            $selectedPurchaseOrder = $purchaseOrders->firstWhere('id', (int) $request->integer('purchase_order_id'));

            if ($selectedPurchaseOrder) {
                $remainingItems = $selectedPurchaseOrder->items
                    ->filter(fn (PurchaseOrderItem $item) => $this->remainingQuantity($item) > 0)
                    ->values();
            }
        }

        return view('purchase-receipts.create', [
            'purchaseOrders' => $purchaseOrders,
            'selectedPurchaseOrder' => $selectedPurchaseOrder,
            'remainingItems' => $remainingItems,
            'defaultReceiptNo' => $this->generateReceiptNo(),
        ]);
    }

    public function store(StorePurchaseReceiptRequest $request)
    {
        $purchaseOrder = PurchaseOrder::with(['items', 'supplier', 'warehouse'])->findOrFail($request->integer('purchase_order_id'));

        $receipt = DB::transaction(function () use ($request, $purchaseOrder) {
            $receipt = PurchaseReceipt::create([
                'receipt_no' => $request->validated()['receipt_no'],
                'purchase_order_id' => $purchaseOrder->id,
                'receipt_date' => $request->validated()['receipt_date'],
                'supplier_id' => $purchaseOrder->supplier_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'total_amount' => 0,
                'remark' => $request->validated()['remark'] ?? null,
                'created_by' => $request->user()?->id,
            ]);

            $totalAmount = 0;

            foreach ($request->validated()['items'] as $itemInput) {
                $purchaseOrderItem = $purchaseOrder->items->firstWhere('id', (int) $itemInput['purchase_order_item_id']);
                $quantityReceived = (int) $itemInput['quantity_received'];

                if (! $purchaseOrderItem || $quantityReceived <= 0) {
                    continue;
                }

                $unitCost = (float) $itemInput['unit_cost'];
                $lineTotal = round($quantityReceived * $unitCost, 2);

                $receipt->items()->create([
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'item_type' => $purchaseOrderItem->item_type,
                    'item_id' => $purchaseOrderItem->item_id,
                    'item_code' => $purchaseOrderItem->item_code,
                    'item_name' => $purchaseOrderItem->item_name,
                    'quantity' => $quantityReceived,
                    'unit_cost' => $unitCost,
                    'line_total' => $lineTotal,
                    'remark' => $itemInput['remark'] ?? null,
                ]);

                $purchaseOrderItem->increment('received_quantity', $quantityReceived);
                $this->updateCostPrices($purchaseOrderItem->item_type, $purchaseOrderItem->item_id, $quantityReceived, $unitCost);
                $this->increaseStock($purchaseOrderItem->item_type, $purchaseOrderItem->item_id, $purchaseOrder->warehouse_id, $quantityReceived, $receipt, $request->validated()['remark'] ?? null, $request->user()?->id);

                $totalAmount += $lineTotal;
            }

            $receipt->update([
                'total_amount' => $totalAmount,
            ]);

            $this->syncPurchaseOrderStatus($purchaseOrder->fresh('items'));

            return $receipt;
        });

        return redirect()
            ->route('purchase-receipts.show', $receipt)
            ->with('success', '進貨入庫已完成。');
    }

    public function show(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load(['purchaseOrder', 'supplier', 'warehouse', 'creator', 'items.purchaseOrderItem']);

        return view('purchase-receipts.show', compact('purchaseReceipt'));
    }

    public function edit(PurchaseReceipt $purchaseReceipt)
    {
        return view('purchase-receipts.edit', compact('purchaseReceipt'));
    }

    public function update(UpdatePurchaseReceiptRequest $request, PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->update($request->validated());

        return redirect()
            ->route('purchase-receipts.show', $purchaseReceipt)
            ->with('success', '進貨單資料已更新。');
    }

    private function eligiblePurchaseOrders(): Collection
    {
        return PurchaseOrder::with(['supplier', 'warehouse', 'items'])
            ->whereIn('status', ['draft', 'confirmed', 'completed'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get()
            ->filter(function (PurchaseOrder $purchaseOrder) {
                return $purchaseOrder->items->contains(fn (PurchaseOrderItem $item) => $this->remainingQuantity($item) > 0);
            })
            ->values();
    }

    private function generateReceiptNo(): string
    {
        $prefix = 'PR-'.now()->format('Ymd');
        $count = PurchaseReceipt::where('receipt_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }

    private function remainingQuantity(PurchaseOrderItem $item): int
    {
        return max(0, $item->quantity - $item->received_quantity);
    }

    private function increaseStock(string $itemType, int $itemId, int $warehouseId, int $quantityReceived, PurchaseReceipt $receipt, ?string $receiptRemark, ?int $userId): void
    {
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
        $afterQuantity = $beforeQuantity + $quantityReceived;

        $stock->update([
            'quantity' => $afterQuantity,
        ]);

        StockMovement::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'movement_type' => 'in',
            'quantity' => $quantityReceived,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $afterQuantity,
            'reference_type' => 'purchase_receipt',
            'reference_id' => $receipt->id,
            'remark' => $receiptRemark,
            'created_by' => $userId,
        ]);
    }

    private function updateCostPrices(string $itemType, int $itemId, int $quantityReceived, float $unitCost): void
    {
        if ($itemType === 'part') {
            $part = Part::withSum('stocks as total_stock_quantity', 'quantity')->findOrFail($itemId);
            $averageCost = $this->calculateAverageCost(
                (int) ($part->total_stock_quantity ?? 0),
                (float) $part->average_cost_price,
                $quantityReceived,
                $unitCost
            );

            $part->update([
                'last_cost_price' => $unitCost,
                'average_cost_price' => $averageCost,
            ]);

            return;
        }

        $vehicle = Vehicle::withSum('stocks as total_stock_quantity', 'quantity')->findOrFail($itemId);
        $averageCost = $this->calculateAverageCost(
            (int) ($vehicle->total_stock_quantity ?? 0),
            (float) $vehicle->average_cost_price,
            $quantityReceived,
            $unitCost
        );

        $vehicle->update([
            'last_cost_price' => $unitCost,
            'average_cost_price' => $averageCost,
        ]);
    }

    private function calculateAverageCost(int $currentQuantity, float $currentAverageCost, int $receivedQuantity, float $receivedUnitCost): float
    {
        $currentQuantity = max(0, $currentQuantity);
        $receivedQuantity = max(0, $receivedQuantity);
        $newTotalQuantity = $currentQuantity + $receivedQuantity;

        if ($newTotalQuantity === 0) {
            return 0;
        }

        if ($currentQuantity === 0) {
            return round($receivedUnitCost, 4);
        }

        $currentAmount = $currentQuantity * $currentAverageCost;
        $receivedAmount = $receivedQuantity * $receivedUnitCost;

        return round(($currentAmount + $receivedAmount) / $newTotalQuantity, 4);
    }

    private function syncPurchaseOrderStatus(PurchaseOrder $purchaseOrder): void
    {
        $hasItems = $purchaseOrder->items->isNotEmpty();
        $isCompleted = $hasItems && $purchaseOrder->items->every(
            fn (PurchaseOrderItem $item) => $item->received_quantity >= $item->quantity
        );

        $purchaseOrder->update([
            'status' => $isCompleted ? 'completed' : 'confirmed',
        ]);
    }
}
