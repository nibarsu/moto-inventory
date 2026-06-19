<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payable extends Model
{
    protected $table = 'accounts_payable';

    protected $fillable = [
        'ap_no',
        'supplier_id',
        'source_type',
        'source_id',
        'ap_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'source_id' => 'integer',
            'ap_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'created_by' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
