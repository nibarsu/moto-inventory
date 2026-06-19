<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImportLog extends Model
{
    protected $fillable = [
        'item_type',
        'original_filename',
        'total_rows',
        'created_count',
        'updated_count',
        'skipped_count',
        'status',
        'summary',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'created_count' => 'integer',
            'updated_count' => 'integer',
            'skipped_count' => 'integer',
            'summary' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
