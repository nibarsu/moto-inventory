<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'code',
        'name',
        'tax_id',
        'contact_person',
        'phone',
        'mobile',
        'email',
        'address',
        'remark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }
}
