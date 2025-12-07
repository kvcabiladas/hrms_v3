<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private $timezone = 'Asia/Manila';

    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'super_admin') abort(403);

        $query = Attendance::with('employee')->latest('date');

        // Allow HR to filter by date range
        if (request('start_date') && request('end_date')) {
            $query->whereBetween('date', [request('start_date'), request('end_date')]);
        }

        if ($user->role === 'employee') {
            $user->employee ? $query->where('employee_id', $user->employee->id) : $query->where('id', 0);
        }

        $attendances = $query->paginate(15);
        
        $stats = ['present' => 0, 'late' => 0, 'hours' => 0];
        if ($user->employee) {
            $now = Carbon::now($this->timezone);
            $monthlyRecords = Attendance::where('employee_id', $user->employee->id)
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->get();
            $stats['present'] = $monthlyRecords->count();
            $stats['late'] = $monthlyRecords->where('status', 'late')->count();
            $stats['hours'] = $monthlyRecords->sum('total_hours');
        }
            
        return view('attendance.index', compact('attendances', 'stats'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) return back()->with('error', 'No employee profile linked.');

        $now = Carbon::now($this->timezone);
        
        // Simple start time restriction
        $start = Carbon::createFromTime(8, 0, 0, $this->timezone);
        if ($now->lessThan($start)) return back()->with('error', 'Cannot clock in before 8:00 AM.');

        if (Attendance::where('employee_id', $employee->id)->where('date', $now->toDateString())->exists()) {
            return back()->with('error', 'Already clocked in today.');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $now->toDateString(),
            'clock_in' => $now->toTimeString(),
            'status' => $now->gt(Carbon::createFromTime(8, 15, 0, $this->timezone)) ? 'late' : 'present',
        ]);

        return back()->with('success', 'Clocked in successfully!');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $now = Carbon::now($this->timezone);
        
        // Ensure date matches the clock-in date for calculation
        $clockIn = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->clock_in, $this->timezone);
        
        // Force calculation to be positive
        $diff = $clockIn->diffInMinutes($now);
        $hours = round($diff / 60, 2);

        $attendance->update([
            'clock_out' => $now->toTimeString(),
            'total_hours' => $hours,
        ]);

        return back()->with('success', 'Clocked out. Total hours: ' . $hours);
    }
}