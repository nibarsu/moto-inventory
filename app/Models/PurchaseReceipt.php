<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceipt extends Model
{
    protected $fillable = [
        'receipt_no',
        'purchase_order_id',
        'receipt_date',
        'supplier_id',
        'warehouse_id',
        'total_amount',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_id' => 'integer',
            'receipt_date' => 'date',
            'supplier_id' => 'integer',
            'warehouse_id' => 'integer',
            'total_amount' => 'decimal:2',
            'created_by' => 'integer',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(PurchaseReceiptItem::class);
    }
}
