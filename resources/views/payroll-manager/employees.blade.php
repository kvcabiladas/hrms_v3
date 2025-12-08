@extends('layouts.hrms')

@section('title', 'Employee Payroll Management')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Employee Payroll Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage employee salaries and payroll information</p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('payroll-manager.employees') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name or employee ID..."
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                </div>
                <div class="w-64">
                    <select name="department"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    Search
                </button>
            </form>
        </div>

        <!-- Employee List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Designation</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Basic Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Payrolls</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold">
                                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $employee->first_name }}
                                                {{ $employee->last_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $employee->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $employee->employee_id }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $employee->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $employee->designation->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-bold text-green-600">₱{{ number_format($employee->basic_salary ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $employee->payrolls->count() }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('payroll-manager.employee.payroll', $employee->id) }}"
                                        class="text-green-600 hover:text-green-700 font-medium text-sm">
                                        View Payroll
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No employees found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection