<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiptItem extends Model
{
    protected $fillable = [
        'purchase_receipt_id',
        'purchase_order_item_id',
        'item_type',
        'item_id',
        'item_code',
        'item_name',
        'quantity',
        'unit_cost',
        'line_total',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'purchase_receipt_id' => 'integer',
            'purchase_order_item_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}
