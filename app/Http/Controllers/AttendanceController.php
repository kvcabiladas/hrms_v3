<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index()
    {
        $user = Auth::user();

        // BLOCK SUPER ADMIN
        if ($user->role === 'super_admin') {
            abort(403, 'Super Admins do not manage or view attendance.');
        }

        $query = Attendance::with('employee')->latest('date');

        // IF User is a regular Employee, show ONLY their own data
        if ($user->role === 'employee') {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->where('id', 0); // Show empty if no profile
            }
        }

        // HR sees everyone by default (no filter needed)

        $attendances = $query->paginate(15);
            
        return view('attendance.index', compact('attendances'));
    }

    /**
     * Store a newly created resource (Clock In).
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'super_admin') { abort(403); }

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return back()->with('error', 'No employee profile linked to this user account.');
        }

        // 1. TIME RESTRICTION: Cannot clock in before 8:00 AM
        $currentTime = now();
        $startTime = Carbon::createFromTime(8, 0, 0); 

        if ($currentTime->lessThan($startTime)) {
            return back()->with('error', 'You cannot clock in before 8:00 AM.');
        }

        // 2. Check duplicate
        $exists = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already clocked in today.');
        }

        // 3. Late Logic (After 8:15 AM)
        $status = 'present';
        $lateTime = Carbon::createFromTime(8, 15, 0);
        if ($currentTime->greaterThan($lateTime)) {
            $status = 'late';
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now(),
            'clock_in' => now(),
            'status' => $status,
        ]);

        return back()->with('success', 'Clocked in successfully!');
    }

    /**
     * Update the specified resource (Clock Out).
     */
    public function update(Request $request, Attendance $attendance)
    {
        if (Auth::user()->role === 'super_admin') { abort(403); }

        // 1. TIME RESTRICTION: Cannot clock out before 5:00 PM
        $currentTime = now();
        $endTime = Carbon::createFromTime(17, 0, 0); 

        if ($currentTime->lessThan($endTime)) {
            return back()->with('error', 'You cannot clock out before 5:00 PM.');
        }

        $attendance->update([
            'clock_out' => now(),
        ]);

        return back()->with('success', 'Clocked out successfully!');
    }
}