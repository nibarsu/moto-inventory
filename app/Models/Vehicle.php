<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'model_code',
        'barcode',
        'name',
        'brand_id',
        'category_id',
        'year',
        'color',
        'engine_displacement',
        'last_cost_price',
        'sale_price',
        'remark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'last_cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
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
