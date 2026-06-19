<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    protected $table = 'accounts_receivable';

    protected $fillable = [
        'ar_no',
        'customer_id',
        'source_type',
        'source_id',
        'ar_date',
        'due_date',
        'total_amount',
        'received_amount',
        'balance_amount',
        'status',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'source_id' => 'integer',
            'ar_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'created_by' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
