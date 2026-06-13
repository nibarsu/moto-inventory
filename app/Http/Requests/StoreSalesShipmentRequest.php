<?php

namespace App\Http\Requests;

use App\Models\PartStock;
use App\Models\SalesOrder;
use App\Models\VehicleStock;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_no' => ['required', 'string', 'max:30', 'unique:sales_shipments,shipment_no'],
            'sales_order_id' => ['required', 'exists:sales_orders,id'],
            'shipment_date' => ['required', 'date'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.sales_order_item_id' => ['required', 'integer', 'exists:sales_order_items,id'],
            'items.*.quantity_shipped' => ['required', 'integer', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'items.*.remark' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $salesOrder = SalesOrder::with('items')->find($this->input('sales_order_id'));

            if (! $salesOrder) {
                return;
            }

            if ($salesOrder->status === 'cancelled') {
                $validator->errors()->add('sales_order_id', 'Cancelled sales orders cannot be shipped.');
            }

            if ($this->filled('shipment_date') && $this->date('shipment_date')?->lt($salesOrder->order_date)) {
                $validator->errors()->add('shipment_date', 'Shipment date cannot be earlier than sales order date.');
            }

            $hasPositiveQuantity = false;

            foreach ($this->input('items', []) as $index => $itemInput) {
                $salesOrderItem = $salesOrder->items->firstWhere('id', (int) ($itemInput['sales_order_item_id'] ?? 0));

                if (! $salesOrderItem) {
                    $validator->errors()->add("items.$index.sales_order_item_id", 'Selected sales order item is invalid.');
                    continue;
                }

                $quantityShipped = (int) ($itemInput['quantity_shipped'] ?? 0);
                $remainingQuantity = max(0, $salesOrderItem->quantity - $salesOrderItem->delivered_quantity);
                $availableStock = $this->currentStockQuantity(
                    $salesOrderItem->item_type,
                    $salesOrderItem->item_id,
                    $salesOrder->warehouse_id
                );

                if ($quantityShipped > 0) {
                    $hasPositiveQuantity = true;
                }

                if ($quantityShipped > $remainingQuantity) {
                    $validator->errors()->add("items.$index.quantity_shipped", 'Shipped quantity cannot exceed remaining quantity.');
                }

                if ($quantityShipped > $availableStock) {
                    $validator->errors()->add("items.$index.quantity_shipped", 'Shipped quantity cannot exceed available stock.');
                }
            }

            if (! $hasPositiveQuantity) {
                $validator->errors()->add('items', 'At least one line must have a shipped quantity greater than zero.');
            }
        });
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
}
