@extends('layouts.hrms')

@section('title', 'Payroll Management')

@section('content')
    <div class="flex justify-end mb-6">
        <a href="{{ route('payroll.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">Run Payroll</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Month</th>
                    <th class="px-6 py-4">Basic Salary</th>
                    <th class="px-6 py-4">Net Salary</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($payrolls as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                    <td class="px-6 py-4">{{ $record->month_year }}</td>
                    <td class="px-6 py-4">${{ number_format($record->basic_salary, 2) }}</td>
                    <td class="px-6 py-4 font-bold text-green-700">${{ number_format($record->net_salary, 2) }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">{{ ucfirst($record->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection