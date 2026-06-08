<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'item_type',
        'item_id',
        'warehouse_id',
        'movement_type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'reference_type',
        'reference_id',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'quantity' => 'integer',
            'before_quantity' => 'integer',
            'after_quantity' => 'integer',
            'reference_id' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
