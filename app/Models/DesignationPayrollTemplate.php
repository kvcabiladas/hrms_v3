<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignationPayrollTemplate extends Model
{
    protected $fillable = [
        'designation_id',
        'base_allowance',
        'overtime_multiplier',
        'benefits',
        'description',
    ];

    protected $casts = [
        'base_allowance' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'benefits' => 'array',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
