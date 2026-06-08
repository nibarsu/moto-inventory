<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_type',
        'item_id',
        'item_code',
        'item_name',
        'quantity',
        'unit_price',
        'line_total',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
