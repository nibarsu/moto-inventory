<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesShipmentRequest;
use App\Http\Requests\UpdateSalesShipmentRequest;
use App\Models\PartStock;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesShipment;
use App\Models\StockMovement;
use App\Models\VehicleStock;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesShipmentController extends Controller
{
    public function index()
    {
        $salesShipments = SalesShipment::with(['salesOrder', 'customer', 'warehouse'])
            ->orderByDesc('shipment_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('sales-shipments.index', compact('salesShipments'));
    }

    public function create(Request $request)
    {
        $salesOrders = $this->eligibleSalesOrders();
        $selectedSalesOrder = null;
        $remainingItems = collect();

        if ($request->filled('sales_order_id')) {
            $selectedSalesOrder = $salesOrders->firstWhere('id', (int) $request->integer('sales_order_id'));

            if ($selectedSalesOrder) {
                $remainingItems = $selectedSalesOrder->items
                    ->filter(fn (SalesOrderItem $item) => $this->remainingQuantity($item) > 0)
                    ->map(function (SalesOrderItem $item) use ($selectedSalesOrder) {
                        $item->current_stock = $this->currentStockQuantity(
                            $item->item_type,
                            $item->item_id,
                            $selectedSalesOrder->warehouse_id
                        );

                        return $item;
                    })
                    ->values();
            }
        }

        return view('sales-shipments.create', [
            'salesOrders' => $salesOrders,
            'selectedSalesOrder' => $selectedSalesOrder,
            'remainingItems' => $remainingItems,
            'defaultShipmentNo' => $this->generateShipmentNo(),
        ]);
    }

    public function store(StoreSalesShipmentRequest $request)
    {
        $salesOrder = SalesOrder::with(['items', 'customer', 'warehouse'])->findOrFail($request->integer('sales_order_id'));

        $salesShipment = DB::transaction(function () use ($request, $salesOrder) {
            $salesShipment = SalesShipment::create([
                'shipment_no' => $request->validated()['shipment_no'],
                'sales_order_id' => $salesOrder->id,
                'shipment_date' => $request->validated()['shipment_date'],
                'customer_id' => $salesOrder->customer_id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'total_amount' => 0,
                'remark' => $request->validated()['remark'] ?? null,
                'created_by' => $request->user()?->id,
            ]);

            $totalAmount = 0;

            foreach ($request->validated()['items'] as $itemInput) {
                $salesOrderItem = $salesOrder->items->firstWhere('id', (int) $itemInput['sales_order_item_id']);
                $quantityShipped = (int) $itemInput['quantity_shipped'];

                if (! $salesOrderItem || $quantityShipped <= 0) {
                    continue;
                }

                $unitPrice = (float) $itemInput['unit_price'];
                $lineTotal = round($quantityShipped * $unitPrice, 2);

                $salesShipment->items()->create([
                    'sales_order_item_id' => $salesOrderItem->id,
                    'item_type' => $salesOrderItem->item_type,
                    'item_id' => $salesOrderItem->item_id,
                    'item_code' => $salesOrderItem->item_code,
                    'item_name' => $salesOrderItem->item_name,
                    'quantity' => $quantityShipped,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'remark' => $itemInput['remark'] ?? null,
                ]);

                $salesOrderItem->increment('delivered_quantity', $quantityShipped);
                $this->decreaseStock(
                    $salesOrderItem->item_type,
                    $salesOrderItem->item_id,
                    $salesOrder->warehouse_id,
                    $quantityShipped,
                    $salesShipment,
                    $request->validated()['remark'] ?? null,
                    $request->user()?->id
                );

                $totalAmount += $lineTotal;
            }

            $salesShipment->update([
                'total_amount' => $totalAmount,
            ]);

            $this->syncSalesOrderStatus($salesOrder->fresh('items'));

            return $salesShipment;
        });

        return redirect()
            ->route('sales-shipments.show', $salesShipment)
            ->with('success', '銷貨出庫單已建立。');
    }

    public function show(SalesShipment $salesShipment)
    {
        $salesShipment->load(['salesOrder', 'customer', 'warehouse', 'creator', 'items.salesOrderItem']);

        return view('sales-shipments.show', compact('salesShipment'));
    }

    public function edit(SalesShipment $salesShipment)
    {
        return view('sales-shipments.edit', compact('salesShipment'));
    }

    public function update(UpdateSalesShipmentRequest $request, SalesShipment $salesShipment)
    {
        $salesShipment->update($request->validated());

        return redirect()
            ->route('sales-shipments.show', $salesShipment)
            ->with('success', '銷貨出庫單已更新。');
    }

    private function eligibleSalesOrders(): Collection
    {
        return SalesOrder::with(['customer', 'warehouse', 'items'])
            ->whereIn('status', ['draft', 'confirmed', 'completed'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get()
            ->filter(function (SalesOrder $salesOrder) {
                return $salesOrder->items->contains(fn (SalesOrderItem $item) => $this->remainingQuantity($item) > 0);
            })
            ->values();
    }

    private function generateShipmentNo(): string
    {
        $prefix = 'SS-'.now()->format('Ymd');
        $count = SalesShipment::where('shipment_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }

    private function remainingQuantity(SalesOrderItem $item): int
    {
        return max(0, $item->quantity - $item->delivered_quantity);
    }

    private function currentStockQuantity(string $itemType, int $itemId, int $warehouseId): int
    {
        if ($itemType === 'part') {
            return (int) (PartStock::where('part_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->value('quantity') ?? 0);
        }

        return (int) (VehicleStock::where('vehicle_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->value('quantity') ?? 0);
    }

    private function decreaseStock(string $itemType, int $itemId, int $warehouseId, int $quantityShipped, SalesShipment $shipment, ?string $shipmentRemark, ?int $userId): void
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

        if ($quantityShipped > $beforeQuantity) {
            throw new \RuntimeException('Insufficient stock for shipment.');
        }

        $afterQuantity = $beforeQuantity - $quantityShipped;

        $stock->update([
            'quantity' => $afterQuantity,
        ]);

        StockMovement::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'movement_type' => 'out',
            'quantity' => -$quantityShipped,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $afterQuantity,
            'reference_type' => 'sales_shipment',
            'reference_id' => $shipment->id,
            'remark' => $shipmentRemark,
            'created_by' => $userId,
        ]);
    }

    private function syncSalesOrderStatus(SalesOrder $salesOrder): void
    {
        $hasItems = $salesOrder->items->isNotEmpty();
        $isCompleted = $hasItems && $salesOrder->items->every(
            fn (SalesOrderItem $item) => $item->delivered_quantity >= $item->quantity
        );

        $salesOrder->update([
            'status' => $isCompleted ? 'completed' : 'confirmed',
        ]);
    }
}
