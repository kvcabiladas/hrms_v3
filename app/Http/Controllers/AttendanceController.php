<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        // Shows the list of all attendance records
        $attendances = Attendance::with('employee')
            ->latest('date')
            ->paginate(15);
            
        return view('attendance.index', compact('attendances'));
    }

    public function store(Request $request)
    {
        // Logic for "Clock In" button
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return back()->with('error', 'No employee profile linked to this user.');
        }

        // Check if already clocked in today
        $exists = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already clocked in today.');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now(),
            'clock_in' => now(),
            'status' => 'present',
        ]);

        return back()->with('success', 'Clocked in successfully!');
    }

    public function update(Request $request, Attendance $attendance)
    {
        // Logic for "Clock Out" button
        $attendance->update([
            'clock_out' => now(),
        ]);

        return back()->with('success', 'Clocked out successfully!');
    }
}