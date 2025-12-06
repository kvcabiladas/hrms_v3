@extends('layouts.hrms')

@section('title', 'My Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- 1. Attendance Status Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Today's Status</p>
                    @if($attendanceToday)
                        <h3 class="text-xl font-bold text-green-600 mt-1">Present</h3>
                        <p class="text-xs text-gray-500">Clocked in at {{ \Carbon\Carbon::parse($attendanceToday->clock_in)->format('H:i') }}</p>
                    @else
                        <h3 class="text-xl font-bold text-gray-400 mt-1">Not Clocked In</h3>
                        <a href="{{ route('attendance.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">Go to Clock In</a>
                    @endif
                </div>
                <div class="p-3 {{ $attendanceToday ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-400' }} rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- 2. My Leave Balance / Pending -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Leave Requests</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $pendingLeaves }} Pending</h3>
                    <a href="{{ route('leaves.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">Apply for Leave</a>
                </div>
                <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- 3. Last Payroll -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Last Salary</p>
                    @if($lastPayroll)
                        <h3 class="text-xl font-bold text-gray-800 mt-1">${{ number_format($lastPayroll->net_salary, 2) }}</h3>
                        <p class="text-xs text-gray-500">{{ $lastPayroll->month_year }}</p>
                    @else
                        <h3 class="text-xl font-bold text-gray-400 mt-1">--</h3>
                        <p class="text-xs text-gray-500">No records yet</p>
                    @endif
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('attendance.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition">
                <div class="bg-green-100 text-green-600 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <span class="font-medium text-gray-700">Time In / Out</span>
            </a>
            
            <a href="{{ route('leaves.create') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition">
                <div class="bg-yellow-100 text-yellow-600 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                <span class="font-medium text-gray-700">File Leave</span>
            </a>

            <a href="{{ route('payroll.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition">
                <div class="bg-blue-100 text-blue-600 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <span class="font-medium text-gray-700">View Payslips</span>
            </a>
        </div>
    </div>
@endsection