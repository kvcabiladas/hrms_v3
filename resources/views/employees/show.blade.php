@extends('layouts.hrms')

@section('title', 'Employee Profile')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-3xl mx-auto mb-4 uppercase">
                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-gray-500 text-sm mb-4">{{ $employee->designation->name ?? 'N/A' }}</p>
                <div class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 border border-green-100">
                    {{ ucfirst($employee->status) }}
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6 text-left space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Employee ID</span>
                        <span class="font-medium text-gray-800">{{ $employee->employee_id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Department</span>
                        <span class="font-medium text-gray-800">{{ $employee->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-800">{{ $employee->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Attendance</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Clock In</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employee->attendance->take(5) as $record)
                        <tr>
                            <td class="px-4 py-3">{{ $record->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}</td>
                            <td class="px-4 py-3"><span class="text-green-600 font-medium">{{ ucfirst($record->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-400">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection