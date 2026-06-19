<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'record_no',
        'service_date',
        'customer_id',
        'vehicle_id',
        'repair_order_id',
        'plate_no',
        'mileage',
        'service_type',
        'next_service_date',
        'next_service_mileage',
        'service_content',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'customer_id' => 'integer',
            'vehicle_id' => 'integer',
            'repair_order_id' => 'integer',
            'mileage' => 'integer',
            'next_service_date' => 'date',
            'next_service_mileage' => 'integer',
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

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
