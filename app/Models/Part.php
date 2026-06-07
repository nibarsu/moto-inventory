<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = [
        'part_no',
        'barcode',
        'name',
        'brand_id',
        'category_id',
        'unit',
        'last_cost_price',
        'sale_price',
        'safety_stock',
        'remark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'safety_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
