<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Define the timezone here for consistency
    // You can change 'Asia/Manila' to your specific timezone if needed
    private $timezone = 'Asia/Manila';

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            abort(403, 'Super Admins do not manage or view attendance.');
        }

        $query = Attendance::with('employee')->latest('date');

        if ($user->role === 'employee') {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->where('id', 0);
            }
        }

        $attendances = $query->paginate(15);
        
        // Calculate Stats (Using Local Timezone)
        $stats = ['present' => 0, 'late' => 0, 'hours' => 0];
        if ($user->employee) {
            $monthlyRecords = Attendance::where('employee_id', $user->employee->id)
                ->whereMonth('date', Carbon::now($this->timezone)->month) // Fix month check
                ->get();
            $stats['present'] = $monthlyRecords->count();
            $stats['late'] = $monthlyRecords->where('status', 'late')->count();
            $stats['hours'] = $monthlyRecords->sum('total_hours');
        }
            
        return view('attendance.index', compact('attendances', 'stats'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'super_admin') { abort(403); }

        $employee = Auth::user()->employee;
        if (!$employee) return back()->with('error', 'No employee profile linked.');

        // 1. GET CURRENT TIME IN LOCAL TIMEZONE
        $currentTime = Carbon::now($this->timezone);
        
        // 2. SET START TIME IN LOCAL TIMEZONE (08:00 AM)
        $startTime = Carbon::createFromTime(8, 0, 0, $this->timezone); 

        if ($currentTime->lessThan($startTime)) {
            // Optional: Show them how much time is left
            $diff = $currentTime->diffInMinutes($startTime);
            return back()->with('error', "You cannot clock in before 08:00. Please wait $diff minutes.");
        }

        $exists = Attendance::where('employee_id', $employee->id)
            ->where('date', $currentTime->toDateString())
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already clocked in today.');
        }

        // 3. Late Logic (After 08:15)
        $status = 'present';
        $lateTime = Carbon::createFromTime(8, 15, 0, $this->timezone);
        if ($currentTime->greaterThan($lateTime)) {
            $status = 'late';
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $currentTime->toDateString(),
            'clock_in' => $currentTime->toTimeString(), // Saves local time
            'status' => $status,
        ]);

        return back()->with('success', 'Clocked in successfully at ' . $currentTime->format('H:i'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        if (Auth::user()->role === 'super_admin') { abort(403); }

        $currentTime = Carbon::now($this->timezone);
        
        // 4. END TIME RESTRICTION: 17:00 (5:00 PM)
        $endTime = Carbon::createFromTime(17, 0, 0, $this->timezone); 

        if ($currentTime->lessThan($endTime)) {
            $diff = $currentTime->diffInMinutes($endTime);
            return back()->with('error', "You cannot clock out before 17:00. Shift ends in $diff minutes.");
        }

        // Calculate Duration
        $clockIn = Carbon::parse($attendance->clock_in);
        // Ensure clockIn is treated as same timezone for calc
        $totalHours = $clockIn->diffInHours($currentTime) + ($clockIn->diffInMinutes($currentTime) % 60) / 60;

        $attendance->update([
            'clock_out' => $currentTime->toTimeString(),
            'total_hours' => round($totalHours, 2),
        ]);

        return back()->with('success', 'Clocked out successfully!');
    }
}