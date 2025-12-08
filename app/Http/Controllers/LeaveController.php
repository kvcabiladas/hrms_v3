<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\NotificationHelper;

class LeaveController extends Controller
{
    // ... (Keep index, settings, storeType, create, store methods AS IS) ...
    // Just copy-paste the previous versions of those methods if you overwrote them.
    // Below is the FIXED update method:

    public function update(Request $request, Leave $leave)
    {
        // 1. RECALL LOGIC
        if ($request->has('recall')) {
            $request->validate([
                'recalled_date' => 'required|date',
            ]);

            $leave->update([
                'status' => 'recalled',
                'recalled_date' => $request->recalled_date,
            ]);

            // Send notification
            NotificationHelper::leaveRecalled($leave);

            return redirect()->route('leaves.index', ['tab' => 'history'])->with('success', 'Employee has been recalled from leave.');
        }

        // 2. APPROVE / REJECT LOGIC
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $updateData = ['status' => $request->status];

        // If rejecting, save the rejection reason
        if ($request->status === 'rejected' && $request->has('rejection_reason')) {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        $leave->update($updateData);

        // Send notification based on status
        if ($request->status === 'approved') {
            NotificationHelper::leaveApproved($leave);
        } elseif ($request->status === 'rejected') {
            NotificationHelper::leaveRejected($leave);
        }

        return redirect()->route('leaves.index', ['tab' => 'history'])->with('success', 'Leave request has been ' . $request->status . '.');
    }

    /**
     * Display the specified leave request
     */
    public function show(Leave $leave)
    {
        $leave->load(['employee.department', 'type', 'reliefOfficer']);
        return view('leaves.show', compact('leave'));
    }

    // ... (Keep index, settings, etc from previous responses) ...

    // RE-INCLUDING OTHER METHODS HERE JUST IN CASE YOU NEED THE FULL FILE:
    public function index()
    {
        $user = Auth::user();
        $query = Leave::with(['employee', 'type', 'reliefOfficer'])->latest();

        if ($user->role === 'employee' && $user->employee) {
            $query->where('employee_id', $user->employee->id);
        }

        $leaves = $query->paginate(10);

        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'on_leave' => Leave::where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
        ];

        $reliefOfficers = Employee::where('status', 'active')->get();

        return view('leaves.index', compact('leaves', 'stats', 'reliefOfficers'));
    }

    public function settings()
    {
        if (Auth::user()->role === 'employee')
            abort(403);
        $types = LeaveType::all();
        return view('leaves.settings', compact('types'));
    }

    public function storeType(Request $request)
    {
        if (Auth::user()->role === 'employee')
            abort(403);

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

    public function updateType(Request $request, LeaveType $type)
    {
        if (Auth::user()->role === 'employee')
            abort(403);

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

    public function destroyType(LeaveType $type)
    {
        if (Auth::user()->role === 'employee')
            abort(403);

        // Check if any leaves are using this type
        if ($type->leaves()->count() > 0) {
            return back()->with('error', 'Cannot delete leave type that is being used.');
        }

        $type->delete();
        return back()->with('success', 'Leave type deleted successfully.');
    }

    public function create()
    {
        $types = \App\Models\LeaveType::all();

        // Fetch employees where the linked User is NOT a super_admin
        $reliefOfficers = \App\Models\Employee::whereHas('user', function ($q) {
            $q->where('role', '!=', 'super_admin');
        })->where('status', 'active')->get();

        return view('leaves.create', compact('types', 'reliefOfficers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'relief_officer_id' => 'nullable|exists:employees,id',
        ]);

        if (!Auth::user()->employee)
            return back()->with('error', 'No employee profile linked.');

        Leave::create([
            'employee_id' => Auth::user()->employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => Carbon::parse($request->start_date)->diffInDays($request->end_date) + 1,
            'reason' => $request->reason,
            'relief_officer_id' => $request->relief_officer_id,
            'status' => 'pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted.');
    }

    public function cancel(Leave $leave)
    {
        if ($leave->employee_id !== Auth::user()->employee->id)
            abort(403);
        if ($leave->status !== 'pending')
            return back()->with('error', 'Cannot cancel processed request.');
        $leave->delete();
        return back()->with('success', 'Request cancelled.');
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

        // Get leave types with usage statistics
        $leaveTypes = LeaveType::all()->map(function ($type) use ($employee) {
            $usedDays = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->sum('days');

            $type->days_used = $usedDays;
            return $type;
        });

        return view('personal.leaves', compact('leaves', 'leaveTypes'));
    }
}