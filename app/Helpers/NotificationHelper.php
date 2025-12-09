<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    /**
     * Create a notification
     */
    public static function create($userId, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Leave approved notification
     */
    public static function leaveApproved($leave)
    {
        if (!$leave->employee || !$leave->employee->user_id) {
            return null;
        }

        return self::create(
            $leave->employee->user_id,
            'leave_approved',
            'Leave Approved',
            "Your {$leave->type->name} from {$leave->start_date->format('M d')} to {$leave->end_date->format('M d')} has been approved.",
            ['leave_id' => $leave->id]
        );
    }

    /**
     * Leave rejected notification
     */
    public static function leaveRejected($leave)
    {
        if (!$leave->employee || !$leave->employee->user_id) {
            return null;
        }

        return self::create(
            $leave->employee->user_id,
            'leave_rejected',
            'Leave Rejected',
            "Your {$leave->type->name} request has been rejected.",
            ['leave_id' => $leave->id]
        );
    }

    /**
     * Leave requested notification (sent to HR)
     */
    public static function leaveRequested($leave)
    {
        // Get all HR users and super admins
        $hrUsers = \App\Models\User::whereIn('role', ['hr', 'super_admin'])->get();

        foreach ($hrUsers as $hrUser) {
            self::create(
                $hrUser->id,
                'leave_requested',
                'New Leave Request',
                "{$leave->employee->first_name} {$leave->employee->last_name} has requested {$leave->days} days of {$leave->type->name} leave.",
                ['leave_id' => $leave->id]
            );
        }

        return true;
    }

    /**
     * Leave recalled notification
     */
    public static function leaveRecalled($leave)
    {
        if (!$leave->employee || !$leave->employee->user_id) {
            return null;
        }

        return self::create(
            $leave->employee->user_id,
            'leave_recalled',
            'Leave Recalled',
            "Your leave has been recalled. Please contact HR for details.",
            ['leave_id' => $leave->id]
        );
    }

    /**
     * Payroll posted notification
     */
    public static function payrollPosted($payroll)
    {
        if (!$payroll->employee || !$payroll->employee->user_id) {
            return null;
        }

        return self::create(
            $payroll->employee->user_id,
            'payroll_posted',
            'Payroll Posted',
            "Your payroll for {$payroll->month_year} is now available. Net Pay: ₱" . number_format($payroll->net_salary, 2),
            ['payroll_id' => $payroll->id]
        );
    }
}
