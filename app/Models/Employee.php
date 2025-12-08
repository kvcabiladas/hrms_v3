<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'joining_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // When deleting an employee, also delete related records
        static::deleting(function ($employee) {
            // Delete related attendance records
            $employee->attendance()->delete();

            // Delete related leave records
            $employee->leaves()->delete();

            // Delete related documents
            $employee->documents()->delete();

            // Delete related onboarding tasks
            $employee->onboardingTasks()->delete();

            // Note: Payroll records are typically kept for historical/audit purposes
            // so we don't delete them
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function onboardingTasks()
    {
        return $this->hasMany(OnboardingTask::class);
    }
}