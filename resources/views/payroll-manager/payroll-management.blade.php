@extends('layouts.hrms')

@section('title', 'Payroll Management')

@section('content')
    <div x-data="{ 
                            activeTab: new URLSearchParams(window.location.search).get('tab') || 'employees', 
                            searchQuery: '',
                            sortBy: '',
                            showRunPayrollModal: new URLSearchParams(window.location.search).get('action') === 'run-payroll'
                        }" x-init="
                            // Update URL when tab changes
                            $watch('activeTab', value => {
                                const url = new URL(window.location);
                                url.searchParams.set('tab', value);
                                window.history.pushState({}, '', url);
                                // Clear search and sort when switching tabs
                                searchQuery = '';
                                sortBy = '';
                            });

                            // Close modal when clicking outside or pressing escape
                            $watch('showRunPayrollModal', value => {
                                if (!value) {
                                    // Remove action parameter from URL when modal closes
                                    const url = new URL(window.location);
                                    url.searchParams.delete('action');
                                    window.history.pushState({}, '', url);
                                }
                            });
                        ">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Payroll Management</h1>
            <p class="text-gray-600 text-sm mt-1">Manage employees, settings, and designation templates</p>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <button @click="activeTab = 'employees'"
                    :class="activeTab === 'employees' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Employee List
                </button>
                <button @click="activeTab = 'settings'"
                    :class="activeTab === 'settings' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Payroll Settings
                </button>
                <button @click="activeTab = 'templates'"
                    :class="activeTab === 'templates' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Designation Templates
                </button>
            </nav>
        </div>

        <!-- Tab 1: Employee List -->
        <div x-show="activeTab === 'employees'" style="display: none;">
            @include('payroll-manager.partials.employees-tab')
        </div>

        <!-- Tab 2: Payroll Settings -->
        <div x-show="activeTab === 'settings'" style="display: none;">
            @include('payroll-manager.partials.settings-tab')
        </div>

        <!-- Tab 3: Designation Templates -->
        <div x-show="activeTab === 'templates'" style="display: none;">
            @include('payroll-manager.partials.templates-tab')
        </div>

        <!-- Run Payroll Modal -->
        <div x-show="showRunPayrollModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="showRunPayrollModal = false" @keydown.escape.window="showRunPayrollModal = false">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Run Payroll</h3>
                        <p class="text-sm text-gray-600 mt-1">Process payroll for all employees</p>
                    </div>
                    <button @click="showRunPayrollModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll-manager.run-payroll') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">This will process payroll for all active employees</p>
                                    <p class="text-blue-700">The system will calculate salaries based on attendance records,
                                        hourly rates, and configured rules.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Month/Year Selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Month*</label>
                                <select name="month" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="">Select Month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year*</label>
                                <select name="year" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="">Select Year</option>
                                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Summary Stats -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Payroll Summary</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ $employees->where('status', 'active')->count() }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Active Employees</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ $employees->count() }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Total Employees</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-purple-600">
                                        ₱{{ number_format($employees->where('status', 'active')->sum('hourly_rate') * 160, 2) }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">Est. Total (160h)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="include_allowances" value="1" checked
                                    class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <div>
                                    <p class="font-medium text-gray-800">Include Allowances</p>
                                    <p class="text-sm text-gray-600">Apply designation-based allowances from templates</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="include_deductions" value="1" checked
                                    class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <div>
                                    <p class="font-medium text-gray-800">Include Deductions</p>
                                    <p class="text-sm text-gray-600">Apply configured payroll deductions</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="send_notifications" value="1"
                                    class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <div>
                                    <p class="font-medium text-gray-800">Send Notifications</p>
                                    <p class="text-sm text-gray-600">Notify employees via email when payroll is processed
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Warning -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    <strong>Important:</strong> This action will create payroll records for all active
                                    employees.
                                    Make sure all attendance data is accurate before proceeding.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" @click="showRunPayrollModal = false"
                            class="flex-1 px-4 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Process Payroll
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection