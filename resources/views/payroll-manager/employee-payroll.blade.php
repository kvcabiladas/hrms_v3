@extends('layouts.hrms')

@section('title', 'Employee Payroll Details')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <a href="{{ route('payroll-manager.employees') }}"
            class="inline-flex items-center text-green-600 hover:text-green-700 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Employees
        </a>

        <!-- Employee Header -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-2xl">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}
                        </h1>
                        <p class="text-gray-600">{{ $employee->employee_id }} • {{ $employee->designation->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Information & Template -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Salary -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Basic Salary</h3>
                <div class="mb-4">
                    <p class="text-3xl font-bold text-green-600">₱{{ number_format($employee->basic_salary ?? 0, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Current monthly basic salary</p>
                </div>

                <!-- Update Salary Form -->
                <form method="POST" action="{{ route('payroll-manager.employee.update-salary', $employee->id) }}"
                    class="mt-4" x-data="{ showForm: false }">
                    @csrf
                    @method('PUT')

                    <button type="button" @click="showForm = !showForm"
                        class="text-green-600 hover:text-green-700 font-medium text-sm mb-3">
                        <span x-show="!showForm">Update Basic Salary</span>
                        <span x-show="showForm">Cancel</span>
                    </button>

                    <div x-show="showForm" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Basic Salary</label>
                            <input type="number" name="basic_salary" step="0.01" required
                                value="{{ $employee->basic_salary ?? 0 }}"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            @error('basic_salary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Update Salary
                        </button>
                    </div>
                </form>
            </div>

            <!-- Designation Template (if exists) -->
            @if($template)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Designation Template</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Base Allowance:</span>
                            <span class="font-bold text-gray-900">₱{{ number_format($template->base_allowance, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Overtime Multiplier:</span>
                            <span class="font-bold text-gray-900">{{ $template->overtime_multiplier }}x</span>
                        </div>
                        @if($template->benefits)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-sm font-medium text-gray-700 mb-2">Additional Benefits:</p>
                                <div class="space-y-1">
                                    @foreach($template->benefits as $benefit => $value)
                                        <p class="text-sm text-gray-600">• {{ ucfirst($benefit) }}: {{ $value }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Designation Template</h3>
                    <p class="text-gray-500 text-sm">No template configured for this designation.</p>
                    <a href="{{ route('payroll-manager.templates') }}"
                        class="inline-block mt-3 text-green-600 hover:text-green-700 font-medium text-sm">
                        Configure Template →
                    </a>
                </div>
            @endif
        </div>

        <!-- Payroll History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Payroll History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Basic Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Allowances</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Net Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payrollHistory as $payroll)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ $payroll->month_year }}</td>
                                <td class="px-6 py-4 text-gray-900">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                <td class="px-6 py-4 text-gray-900">₱{{ number_format($payroll->allowances ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-gray-900">₱{{ number_format($payroll->deductions ?? 0, 2) }}</td>
                                <td class="px-6 py-4 font-bold text-green-600">₱{{ number_format($payroll->net_salary, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $payroll->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No payroll history yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payrollHistory->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payrollHistory->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection