<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payroll extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allowances_breakdown' => 'array',
        'deductions_breakdown' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate total hours worked for a given period
     */
    public static function calculateTotalHours($employeeId, $startDate, $endDate)
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('total_hours')
            ->sum('total_hours');
    }

    /**
     * Calculate gross pay based on hourly rate and hours worked
     */
    public static function calculateGrossPay($hourlyRate, $totalHours, $allowances = [])
    {
        $basePay = $hourlyRate * $totalHours;
        $totalAllowances = collect($allowances)->sum('amount');
        return $basePay + $totalAllowances;
    }

    /**
     * Calculate total deductions
     */
    public static function calculateDeductions($grossPay, $deductions = [])
    {
        $totalDeductions = 0;

        foreach ($deductions as $deduction) {
            if ($deduction['type'] === 'percentage') {
                $totalDeductions += ($grossPay * $deduction['value']) / 100;
            } else {
                $totalDeductions += $deduction['value'];
            }
        }

        return $totalDeductions;
    }

    /**
     * Calculate net salary
     */
    public static function calculateNetSalary($grossPay, $totalDeductions)
    {
        return max(0, $grossPay - $totalDeductions);
    }
}