@extends('layouts.hrms')

@section('title', 'Payroll Management')

@section('content')
    <div class="flex justify-end mb-6 gap-3">
        <!-- Settings Button -->
        <a href="{{ route('payroll.settings') }}" class="px-4 py-2 border border-gray-200 bg-white text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Settings
        </a>
        
        <!-- Run Payroll Button -->
        <a href="{{ route('payroll.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Run Payroll
        </a>
    </div>

    <!-- The Table (Same as before) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Month</th>
                    <th class="px-6 py-4 text-right">Basic Salary</th>
                    <th class="px-6 py-4 text-right">Net Salary</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($payrolls as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                    <td class="px-6 py-4">{{ $record->month_year }}</td>
                    <td class="px-6 py-4 text-right">${{ number_format($record->basic_salary, 2) }}</td>
                    <td class="px-6 py-4 font-bold text-green-700 text-right">${{ number_format($record->net_salary, 2) }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">{{ ucfirst($record->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection