<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRule extends Model
{
    protected $fillable = [
        'rule_name',
        'rule_type',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
