<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        // If admin, show all. If employee, show only theirs.
        $query = Leave::with('employee')->latest();

        // Simple check: if user is not admin (id 1), filter by their employee id
        // In a real app, use Roles/Permissions
        if (Auth::id() !== 1 && Auth::user()->employee) {
            $query->where('employee_id', Auth::user()->employee->id);
        }

        $leaves = $query->paginate(10);

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $validated['employee_id'] = Auth::user()->employee->id;
        $validated['status'] = 'pending';

        Leave::create($validated);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted.');
    }

    public function update(Request $request, Leave $leave)
    {
        // Used by Admin to Approve/Reject
        $leave->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Leave status updated.');
    }
}