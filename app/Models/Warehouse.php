<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'contact_person',
        'phone',
        'remark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function partStocks(): HasMany
    {
        return $this->hasMany(PartStock::class);
    }

    public function vehicleStocks(): HasMany
    {
        return $this->hasMany(VehicleStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseReceipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function salesShipments(): HasMany
    {
        return $this->hasMany(SalesShipment::class);
    }
}
