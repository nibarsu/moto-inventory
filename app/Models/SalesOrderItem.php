<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'item_type',
        'item_id',
        'item_code',
        'item_name',
        'quantity',
        'delivered_quantity',
        'unit_price',
        'line_total',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'sales_order_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'integer',
            'delivered_quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class, 'item_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'item_id');
    }

    public function shipmentItems(): HasMany
    {
        return $this->hasMany(SalesShipmentItem::class);
    }
}
