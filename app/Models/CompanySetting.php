<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'name',
    ];

    public static function current(): self
    {
        return static::query()->find(1) ?? new static([
            'name' => config('app.name', 'Moto Inventory'),
        ]);
    }
}
