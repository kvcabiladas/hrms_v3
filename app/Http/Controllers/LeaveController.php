<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    // 1. Dashboard
    public function index()
    {
        $user = Auth::user();
        $query = Leave::with(['employee', 'type', 'reliefOfficer'])->latest();

        // Employees only see their own history
        if ($user->role === 'employee' && $user->employee) {
            $query->where('employee_id', $user->employee->id);
        }

        $leaves = $query->paginate(10);
        
        // Stats
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

    // 2. Settings View
    public function settings()
    {
        if (Auth::user()->role === 'employee') abort(403);
        $types = LeaveType::all();
        return view('leaves.settings', compact('types'));
    }

    // 3. Save New Leave Type
    public function storeType(Request $request)
    {
        if (Auth::user()->role === 'employee') abort(403);
        
        $request->validate([
            'name' => 'required|string', 
            'days_allowed' => 'required|integer'
        ]);
        
        LeaveType::create($request->only(['name', 'days_allowed']));
        return back()->with('success', 'Leave type created.');
    }

    // 4. Create Application View
    public function create()
    {
        $types = LeaveType::all();
        $reliefOfficers = Employee::where('status', 'active')->get();
        return view('leaves.create', compact('types', 'reliefOfficers'));
    }

    // 5. Store Application
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'relief_officer_id' => 'nullable|exists:employees,id',
        ]);

        if (!Auth::user()->employee) return back()->with('error', 'No employee profile linked.');

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

    // 6. Update Status (Approve/Reject) OR Recall
    public function update(Request $request, Leave $leave)
    {
        // RECALL LOGIC
        if ($request->has('recall')) {
            $request->validate(['recalled_date' => 'required|date']);

            $leave->update([
                'status' => 'recalled',
                'recalled_date' => $request->recalled_date,
            ]);
            return back()->with('success', 'Employee has been recalled.');
        }

        // APPROVE/REJECT LOGIC
        $leave->update(['status' => $request->status]);
        return back()->with('success', 'Leave status updated.');
    }

    public function cancel(Leave $leave)
    {
        // Security: Ensure the user owns this leave request
        if ($leave->employee_id !== Auth::user()->employee->id) {
            abort(403, 'Unauthorized');
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'You can only cancel pending requests.');
        }

        $leave->delete(); // Or you can set status to 'cancelled' if you want to keep record

        return back()->with('success', 'Leave request cancelled successfully.');
    }
}