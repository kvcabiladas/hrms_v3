<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveBalance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
    ];

    /**
     * Get the employee that owns the leave balance
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the leave type
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Calculate available days
     */
    public function calculateAvailable()
    {
        $this->available_days = $this->total_days - $this->used_days - $this->pending_days;
        return $this->available_days;
    }

    /**
     * Get or create leave balance for an employee and leave type
     */
    public static function getOrCreate($employeeId, $leaveTypeId, $year = null)
    {
        // Validate inputs
        if (!$employeeId || !$leaveTypeId) {
            throw new \Exception("Employee ID and Leave Type ID are required");
        }

        $year = $year ?? date('Y');

        // Get the leave type to determine total days
        $leaveType = LeaveType::find($leaveTypeId);
        if (!$leaveType) {
            throw new \Exception("Leave type not found");
        }

        $balance = self::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
            ],
            [
                'total_days' => $leaveType->days_allowed,
                'used_days' => 0,
                'pending_days' => 0,
                'available_days' => $leaveType->days_allowed,
            ]
        );

        // Recalculate available days
        $balance->calculateAvailable();
        $balance->save();

        return $balance;
    }

    /**
     * Deduct days when leave is approved
     */
    public function approveLeaveDays($days)
    {
        $this->pending_days = max(0, $this->pending_days - $days);
        $this->used_days += $days;
        $this->calculateAvailable();
        $this->save();
    }

    /**
     * Add days to pending when leave is requested
     */
    public function addPendingDays($days)
    {
        $this->pending_days += $days;
        $this->calculateAvailable();
        $this->save();
    }

    /**
     * Remove days from pending when leave is rejected or cancelled
     */
    public function removePendingDays($days)
    {
        $this->pending_days = max(0, $this->pending_days - $days);
        $this->calculateAvailable();
        $this->save();
    }

    /**
     * Restore days when approved leave is cancelled
     */
    public function restoreUsedDays($days)
    {
        $this->used_days = max(0, $this->used_days - $days);
        $this->calculateAvailable();
        $this->save();
    }
}
