<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelExportLog extends Model
{
    protected $fillable = [
        'export_type',
        'filename',
        'row_count',
        'filter_summary',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'row_count' => 'integer',
            'filter_summary' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
