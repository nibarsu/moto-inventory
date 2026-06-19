<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairOrder extends Model
{
    protected $fillable = [
        'wo_no',
        'order_date',
        'customer_id',
        'vehicle_id',
        'plate_no',
        'mileage',
        'status',
        'complaint',
        'diagnosis',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'customer_id' => 'integer',
            'vehicle_id' => 'integer',
            'mileage' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
