<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'code',
        'name',
        'english_name',
        'remark',
        'is_active',
    ];
}