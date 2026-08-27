<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Part;
use App\Models\PartStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceipt;
use App\Models\SalesOrder;
use App\Models\SalesShipment;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuickTransactionService
{
    public function createPurchase(array $validated, ?User $user): PurchaseReceipt
    {
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
        $supplier = $this->resolveSupplier($validated['supplier_name']);
        $items = $this->resolvePurchaseItems($validated['items']);

        return DB::transaction(function () use ($validated, $user, $warehouse, $supplier, $items) {
            $totalAmount = $items->sum('line_total');

            $purchaseOrder = PurchaseOrder::create([
                'po_no' => $this->generateDocumentNo(PurchaseOrder::class, 'po_no', 'PO'),
                'order_date' => $validated['transaction_date'],
                'expected_date' => $validated['transaction_date'],
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'status' => 'completed',
                'total_amount' => $totalAmount,
                'remark' => $validated['remark'] ?? null,
                'created_by' => $user?->id,
            ]);

            $receipt = PurchaseReceipt::create([
                'receipt_no' => $this->generateDocumentNo(PurchaseReceipt::class, 'receipt_no', 'PR'),
                'purchase_order_id' => $purchaseOrder->id,
                'receipt_date' => $validated['transaction_date'],
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'total_amount' => $totalAmount,
                'remark' => $validated['remark'] ?? null,
                'created_by' => $user?->id,
            ]);

            foreach ($items as $item) {
                $purchaseOrderItem = $purchaseOrder->items()->create([
                    'item_type' => 'part',
                    'item_id' => $item['part']->id,
                    'item_code' => $item['part']->part_no,
                    'item_name' => $item['part']->name,
                    'quantity' => $item['quantity'],
                    'received_quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'remark' => $item['remark'],
                ]);

                $receipt->items()->create([
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'item_type' => 'part',
                    'item_id' => $item['part']->id,
                    'item_code' => $item['part']->part_no,
                    'item_name' => $item['part']->name,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'remark' => $item['remark'],
                ]);

                $this->updateCostPrices($item['part'], $item['quantity'], $item['unit_price']);
                $this->increasePartStock($item['part'], $warehouse, $item['quantity'], $receipt, $validated['remark'] ?? null, $user?->id);
            }

            return $receipt->load(['purchaseOrder', 'supplier', 'warehouse', 'creator', 'items']);
        });
    }

    public function createSale(array $validated, ?User $user): SalesShipment
    {
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
        $customer = $this->resolveCustomer($validated['customer_name']);
        $items = $this->resolveSalesItems($validated['items'], $warehouse);

        return DB::transaction(function () use ($validated, $user, $warehouse, $customer, $items) {
            $totalAmount = $items->sum('line_total');

            $salesOrder = SalesOrder::create([
                'so_no' => $this->generateDocumentNo(SalesOrder::class, 'so_no', 'SO'),
                'order_date' => $validated['transaction_date'],
                'delivery_date' => $validated['transaction_date'],
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouse->id,
                'status' => 'completed',
                'total_amount' => $totalAmount,
                'remark' => $validated['remark'] ?? null,
                'created_by' => $user?->id,
            ]);

            $shipment = SalesShipment::create([
                'shipment_no' => $this->generateDocumentNo(SalesShipment::class, 'shipment_no', 'SS'),
                'sales_order_id' => $salesOrder->id,
                'shipment_date' => $validated['transaction_date'],
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouse->id,
                'total_amount' => $totalAmount,
                'remark' => $validated['remark'] ?? null,
                'created_by' => $user?->id,
            ]);

            foreach ($items as $item) {
                $salesOrderItem = $salesOrder->items()->create([
                    'item_type' => 'part',
                    'item_id' => $item['part']->id,
                    'item_code' => $item['part']->part_no,
                    'item_name' => $item['part']->name,
                    'quantity' => $item['quantity'],
                    'delivered_quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'remark' => $item['remark'],
                ]);

                $shipment->items()->create([
                    'sales_order_item_id' => $salesOrderItem->id,
                    'item_type' => 'part',
                    'item_id' => $item['part']->id,
                    'item_code' => $item['part']->part_no,
                    'item_name' => $item['part']->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'remark' => $item['remark'],
                ]);

                if ((float) $item['part']->sale_price === 0.0) {
                    $item['part']->update([
                        'sale_price' => $item['unit_price'],
                    ]);
                }

                $this->decreasePartStock($item['part'], $warehouse, $item['quantity'], $shipment, $validated['remark'] ?? null, $user?->id);
            }

            return $shipment->load(['salesOrder', 'customer', 'warehouse', 'creator', 'items']);
        });
    }

    public function supplierSuggestions(string $keyword): Collection
    {
        return Supplier::query()
            ->where('name', 'like', '%'.$keyword.'%')
            ->orderBy('name')
            ->limit(10)
            ->get(['name'])
            ->pluck('name');
    }

    public function customerSuggestions(string $keyword): Collection
    {
        return Customer::query()
            ->where('name', 'like', '%'.$keyword.'%')
            ->orderBy('name')
            ->limit(10)
            ->get(['name'])
            ->pluck('name');
    }

    public function productSuggestions(string $keyword): Collection
    {
        return Part::query()
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('part_no', 'like', '%'.$keyword.'%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['name', 'part_no', 'sale_price', 'last_cost_price'])
            ->map(fn (Part $part) => [
                'name' => $part->name,
                'part_no' => $part->part_no,
                'sale_price' => (float) $part->sale_price,
                'last_cost_price' => (float) $part->last_cost_price,
            ]);
    }

    private function resolvePurchaseItems(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item): array {
                $part = $this->resolvePart($item['name'], (float) $item['unit_price']);
                $quantity = (int) $item['quantity'];
                $unitPrice = round((float) $item['unit_price'], 2);

                return [
                    'part' => $part,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => round($quantity * $unitPrice, 2),
                    'remark' => $item['remark'] ?? null,
                ];
            })
            ->values();
    }

    private function resolveSalesItems(array $items, Warehouse $warehouse): Collection
    {
        return collect($items)
            ->map(function (array $item) use ($warehouse): array {
                $part = $this->resolvePart($item['name'], null);
                $quantity = (int) $item['quantity'];
                $unitPrice = round((float) $item['unit_price'], 2);
                $currentStock = (int) (PartStock::query()
                    ->where('part_id', $part->id)
                    ->where('warehouse_id', $warehouse->id)
                    ->value('quantity') ?? 0);

                if ($currentStock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ['商品「'.$part->name.'」庫存不足，目前庫存 '.$currentStock.'。'],
                    ]);
                }

                return [
                    'part' => $part,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => round($quantity * $unitPrice, 2),
                    'remark' => $item['remark'] ?? null,
                ];
            })
            ->values();
    }

    private function resolveSupplier(string $name): Supplier
    {
        $name = trim($name);

        return Supplier::firstOrCreate(
            ['name' => $name],
            [
                'code' => $this->generateCode(Supplier::class, 'code', 'SUP'),
                'is_active' => true,
            ]
        );
    }

    private function resolveCustomer(string $name): Customer
    {
        $name = trim($name);

        return Customer::firstOrCreate(
            ['name' => $name],
            [
                'code' => $this->generateCode(Customer::class, 'code', 'CUS'),
                'is_active' => true,
            ]
        );
    }

    private function resolvePart(string $name, ?float $initialCost): Part
    {
        $name = trim($name);
        $part = Part::query()->where('name', $name)->first();

        if ($part) {
            return $part;
        }

        return Part::create([
            'part_no' => $this->generateCode(Part::class, 'part_no', 'PT'),
            'barcode' => null,
            'name' => $name,
            'brand_id' => null,
            'category_id' => null,
            'unit' => '個',
            'last_cost_price' => $initialCost !== null ? round($initialCost, 2) : 0,
            'average_cost_price' => $initialCost !== null ? round($initialCost, 4) : 0,
            'sale_price' => 0,
            'safety_stock' => 0,
            'remark' => '由快速開單自動建立',
            'is_active' => true,
        ]);
    }

    private function generateCode(string $modelClass, string $column, string $prefix): string
    {
        $datePart = now()->format('Ymd');
        $counter = 1;

        do {
            $code = sprintf('%s-%s-%03d', $prefix, $datePart, $counter);
            $exists = $modelClass::query()->where($column, $code)->exists();
            $counter++;
        } while ($exists);

        return $code;
    }

    private function generateDocumentNo(string $modelClass, string $column, string $prefix): string
    {
        return $this->generateCode($modelClass, $column, $prefix);
    }

    private function updateCostPrices(Part $part, int $receivedQuantity, float $unitCost): void
    {
        $currentQuantity = (int) ($part->stocks()->sum('quantity') ?? 0);
        $currentAverageCost = (float) $part->average_cost_price;
        $newTotalQuantity = max(0, $currentQuantity) + max(0, $receivedQuantity);

        if ($newTotalQuantity === 0) {
            $averageCost = 0;
        } elseif ($currentQuantity === 0) {
            $averageCost = round($unitCost, 4);
        } else {
            $currentAmount = $currentQuantity * $currentAverageCost;
            $receivedAmount = $receivedQuantity * $unitCost;
            $averageCost = round(($currentAmount + $receivedAmount) / $newTotalQuantity, 4);
        }

        $part->update([
            'last_cost_price' => round($unitCost, 2),
            'average_cost_price' => $averageCost,
        ]);
    }

    private function increasePartStock(Part $part, Warehouse $warehouse, int $quantity, PurchaseReceipt $receipt, ?string $remark, ?int $userId): void
    {
        $stock = PartStock::firstOrCreate(
            ['part_id' => $part->id, 'warehouse_id' => $warehouse->id],
            ['quantity' => 0]
        );

        $beforeQuantity = $stock->quantity;
        $afterQuantity = $beforeQuantity + $quantity;

        $stock->update([
            'quantity' => $afterQuantity,
        ]);

        StockMovement::create([
            'item_type' => 'part',
            'item_id' => $part->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'in',
            'quantity' => $quantity,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $afterQuantity,
            'reference_type' => 'purchase_receipt',
            'reference_id' => $receipt->id,
            'remark' => $remark,
            'created_by' => $userId,
        ]);
    }

    private function decreasePartStock(Part $part, Warehouse $warehouse, int $quantity, SalesShipment $shipment, ?string $remark, ?int $userId): void
    {
        $stock = PartStock::firstOrCreate(
            ['part_id' => $part->id, 'warehouse_id' => $warehouse->id],
            ['quantity' => 0]
        );

        $beforeQuantity = $stock->quantity;
        $afterQuantity = $beforeQuantity - $quantity;

        $stock->update([
            'quantity' => $afterQuantity,
        ]);

        StockMovement::create([
            'item_type' => 'part',
            'item_id' => $part->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'out',
            'quantity' => -$quantity,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $afterQuantity,
            'reference_type' => 'sales_shipment',
            'reference_id' => $shipment->id,
            'remark' => $remark,
            'created_by' => $userId,
        ]);
    }
}
