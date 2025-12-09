<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\NotificationHelper;

class LeaveController extends Controller
{
    /**
     * Display leave management page
     */
    public function index()
    {
        $user = Auth::user();
        $query = Leave::with(['employee', 'type', 'reliefOfficer'])->latest();

        if ($user->role === 'employee' && $user->employee) {
            $query->where('employee_id', $user->employee->id);
        }

        // Get all leaves for display
        $leaves = $query->get();

        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'on_leave' => Leave::where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
        ];

        $reliefOfficers = Employee::where('status', 'active')->get();
        $types = LeaveType::all();

        return view('leaves.index', compact('leaves', 'stats', 'reliefOfficers', 'types'));
    }

    /**
     * Show create leave form
     */
    public function create()
    {
        $types = LeaveType::all();
        $reliefOfficers = Employee::whereHas('user', function ($q) {
            $q->where('role', '!=', 'super_admin');
        })->where('status', 'active')->get();

        // Get leave balances for current user
        $employee = Auth::user()->employee;
        $leaveBalances = [];

        if ($employee) {
            foreach ($types as $type) {
                $balance = EmployeeLeaveBalance::getOrCreate($employee->id, $type->id);
                $leaveBalances[$type->id] = $balance;
            }
        }

        return view('leaves.create', compact('types', 'reliefOfficers', 'leaveBalances'));
    }

    /**
     * Store a new leave request
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'relief_officer_id' => 'nullable|exists:employees,id',
        ]);

        if (!Auth::user()->employee) {
            return back()->with('error', 'No employee profile linked.');
        }

        $employee = Auth::user()->employee;
        $days = Carbon::parse($request->start_date)->diffInDays($request->end_date) + 1;

        // Check leave balance
        $balance = EmployeeLeaveBalance::getOrCreate($employee->id, $request->leave_type_id);

        if ($balance->available_days < $days) {
            return back()->with('error', "Insufficient leave balance. You have {$balance->available_days} days available.");
        }

        DB::beginTransaction();
        try {
            // Create leave request
            $leave = Leave::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $days,
                'reason' => $request->reason,
                'relief_officer_id' => $request->relief_officer_id,
                'status' => 'pending',
            ]);

            // Add to pending days
            $balance->addPendingDays($days);

            // Send notification to HR
            NotificationHelper::leaveRequested($leave);

            DB::commit();
            return redirect()->route('personal.leaves')->with('success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit leave request: ' . $e->getMessage());
        }
    }

    /**
     * Update leave status (Approve/Reject/Recall)
     */
    public function update(Request $request, Leave $leave)
    {
        // RECALL LOGIC
        if ($request->has('recall')) {
            $request->validate([
                'recalled_date' => 'required|date',
            ]);

            DB::beginTransaction();
            try {
                $leave->update([
                    'status' => 'recalled',
                    'recalled_date' => $request->recalled_date,
                ]);

                // If leave was approved, restore the used days
                if ($leave->status === 'approved') {
                    $balance = EmployeeLeaveBalance::getOrCreate($leave->employee_id, $leave->leave_type_id);
                    $balance->restoreUsedDays($leave->days);
                }

                NotificationHelper::leaveRecalled($leave);

                DB::commit();
                return redirect()->route('leaves.index', ['tab' => 'history'])
                    ->with('success', 'Employee has been recalled from leave.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to recall leave: ' . $e->getMessage());
            }
        }

        // APPROVE / REJECT LOGIC
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $leave->status;
            $updateData = ['status' => $request->status];

            // If rejecting, save the rejection reason
            if ($request->status === 'rejected' && $request->has('rejection_reason')) {
                $updateData['rejection_reason'] = $request->rejection_reason;
            }

            $leave->update($updateData);

            // Update leave balance
            $balance = EmployeeLeaveBalance::getOrCreate($leave->employee_id, $leave->leave_type_id);

            if ($request->status === 'approved') {
                // Move from pending to used
                $balance->approveLeaveDays($leave->days);
                NotificationHelper::leaveApproved($leave);
            } elseif ($request->status === 'rejected') {
                // Remove from pending
                $balance->removePendingDays($leave->days);
                NotificationHelper::leaveRejected($leave);
            }

            DB::commit();
            return redirect()->route('leaves.index', ['tab' => 'history'])
                ->with('success', 'Leave request has been ' . $request->status . ' successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update leave: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a pending leave request
     */
    public function cancel(Leave $leave)
    {
        if ($leave->employee_id !== Auth::user()->employee->id) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Cannot cancel processed request.');
        }

        DB::beginTransaction();
        try {
            // Remove from pending balance
            $balance = EmployeeLeaveBalance::getOrCreate($leave->employee_id, $leave->leave_type_id);
            $balance->removePendingDays($leave->days);

            $leave->delete();

            DB::commit();
            return back()->with('success', 'Leave request cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel leave: ' . $e->getMessage());
        }
    }

    /**
     * Personal leaves view - shows only the logged-in user's leave history
     */
    public function personalLeaves()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee profile linked.');
        }

        // Get user's leave history
        $leaves = Leave::with(['type', 'reliefOfficer'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(15);

        // Get leave balances for current year
        $leaveTypes = LeaveType::all()->map(function ($type) use ($employee) {
            $balance = EmployeeLeaveBalance::getOrCreate($employee->id, $type->id);
            $type->balance = $balance;
            $type->days_used = $balance->used_days;
            $type->days_pending = $balance->pending_days;
            $type->days_available = $balance->available_days;
            return $type;
        });

        return view('personal.leaves', compact('leaves', 'leaveTypes'));
    }

    /**
     * Leave Settings (HR Only)
     */
    public function settings()
    {
        if (Auth::user()->role === 'employee') {
            abort(403);
        }

        $types = LeaveType::all();
        return view('leaves.settings', compact('types'));
    }

    /**
     * Store new leave type
     */
    public function storeType(Request $request)
    {
        if (Auth::user()->role === 'employee') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'days_allowed' => 'required|integer|min:1',
            'is_recallable' => 'nullable|boolean',
        ]);

        LeaveType::create([
            'name' => $request->name,
            'days_allowed' => $request->days_allowed,
            'is_recallable' => $request->has('is_recallable') ? 1 : 0,
        ]);

        return back()->with('success', 'Leave type created successfully.');
    }

    /**
     * Update leave type
     */
    public function updateType(Request $request, LeaveType $type)
    {
        if (Auth::user()->role === 'employee') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'days_allowed' => 'required|integer|min:1',
            'is_recallable' => 'nullable|boolean',
        ]);

        $type->update([
            'name' => $request->name,
            'days_allowed' => $request->days_allowed,
            'is_recallable' => $request->has('is_recallable') ? 1 : 0,
        ]);

        return back()->with('success', 'Leave type updated successfully.');
    }

    /**
     * Delete leave type
     */
    public function destroyType(LeaveType $type)
    {
        if (Auth::user()->role === 'employee') {
            abort(403);
        }

        // Check if any leaves are using this type
        if ($type->leaves()->count() > 0) {
            return back()->with('error', 'Cannot delete leave type that is being used.');
        }

        $type->delete();
        return back()->with('success', 'Leave type deleted successfully.');
    }

    /**
     * Show leave details
     */
    public function show(Leave $leave)
    {
        $leave->load(['employee.department', 'type', 'reliefOfficer']);
        return view('leaves.show', compact('leave'));
    }
}
