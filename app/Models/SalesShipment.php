<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesShipment extends Model
{
    protected $fillable = [
        'shipment_no',
        'sales_order_id',
        'shipment_date',
        'customer_id',
        'warehouse_id',
        'total_amount',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sales_order_id' => 'integer',
            'shipment_date' => 'date',
            'customer_id' => 'integer',
            'warehouse_id' => 'integer',
            'total_amount' => 'decimal:2',
            'created_by' => 'integer',
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesShipmentItem::class);
    }
}
