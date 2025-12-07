@extends('layouts.hrms')

@section('title', 'Employee Profile')

@section('content')
    <!-- Banner Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 relative">
        <!-- Green Background Banner -->
        <div class="h-32 bg-gradient-to-r from-green-600 to-green-500"></div>
        
        <div class="px-8 pb-8">
            <div class="relative flex justify-center">
                <!-- Profile Picture (Centered & Overlapping) -->
                <div class="absolute -top-12 w-24 h-24 rounded-full bg-white p-1 shadow-lg">
                    <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-2xl font-bold uppercase">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                </div>
            </div>

            <div class="mt-14 text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-green-600 font-medium">{{ $employee->designation->name ?? 'No Designation' }}</p>
                <div class="flex justify-center gap-4 mt-2 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        ID: {{ $employee->employee_id }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $employee->email }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ $employee->department->name ?? 'No Dept' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggested Content: Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-bold">Joining Date</p>
            <p class="text-lg font-bold text-gray-800">{{ $employee->joining_date->format('M d, Y') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-bold">Leaves Taken</p>
            <p class="text-lg font-bold text-blue-600">{{ $employee->leaves->where('status', 'approved')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-bold">Attendance Rate</p>
            <p class="text-lg font-bold text-green-600">98%</p> <!-- Placeholder calculation -->
        </div>
    </div>

    <!-- Bottom Section: Login Credentials & Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Login Credentials (Only for HR/Admin) -->
        @if(Auth::user()->role !== 'employee')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Login Credentials</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Username</span>
                    <span class="font-mono text-sm bg-gray-50 px-2 py-1 rounded">{{ $employee->user->username ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Password Status</span>
                    @if($employee->user->temp_password)
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded font-bold">Temp: {{ $employee->user->temp_password }}</span>
                    @else
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold">Secure (Changed)</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Recent Attendance</h3>
            <table class="w-full text-sm text-left">
                <thead class="text-gray-500">
                    <tr><th>Date</th><th>Clock In</th><th>Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employee->attendance->take(5) as $att)
                    <tr>
                        <td class="py-2">{{ $att->date->format('M d') }}</td>
                        <td class="py-2 font-mono text-green-600">{{ \Carbon\Carbon::parse($att->clock_in)->format('H:i') }}</td>
                        <td class="py-2"><span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ ucfirst($att->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-2 text-center text-gray-400">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection