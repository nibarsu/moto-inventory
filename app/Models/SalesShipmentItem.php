<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesShipmentItem extends Model
{
    protected $fillable = [
        'sales_shipment_id',
        'sales_order_item_id',
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
            'sales_shipment_id' => 'integer',
            'sales_order_item_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function salesShipment(): BelongsTo
    {
        return $this->belongsTo(SalesShipment::class);
    }

    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class);
    }
}
