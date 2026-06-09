<?php

namespace App\Http\Requests;

use App\Models\PurchaseOrder;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_no' => ['required', 'string', 'max:30', 'unique:purchase_receipts,receipt_no'],
            'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
            'receipt_date' => ['required', 'date'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.purchase_order_item_id' => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'items.*.remark' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $purchaseOrder = PurchaseOrder::with('items')->find($this->input('purchase_order_id'));

            if (! $purchaseOrder) {
                return;
            }

            if ($purchaseOrder->status === 'cancelled') {
                $validator->errors()->add('purchase_order_id', 'Cancelled purchase orders cannot be received.');
            }

            $hasPositiveQuantity = false;

            foreach ($this->input('items', []) as $index => $itemInput) {
                $purchaseOrderItem = $purchaseOrder->items->firstWhere('id', (int) ($itemInput['purchase_order_item_id'] ?? 0));

                if (! $purchaseOrderItem) {
                    $validator->errors()->add("items.$index.purchase_order_item_id", 'Selected purchase order item is invalid.');
                    continue;
                }

                $quantityReceived = (int) ($itemInput['quantity_received'] ?? 0);
                $remainingQuantity = max(0, $purchaseOrderItem->quantity - $purchaseOrderItem->received_quantity);

                if ($quantityReceived > 0) {
                    $hasPositiveQuantity = true;
                }

                if ($quantityReceived > $remainingQuantity) {
                    $validator->errors()->add("items.$index.quantity_received", 'Received quantity cannot exceed remaining quantity.');
                }
            }

            if (! $hasPositiveQuantity) {
                $validator->errors()->add('items', 'At least one line must have a received quantity greater than zero.');
            }
        });
    }
}
