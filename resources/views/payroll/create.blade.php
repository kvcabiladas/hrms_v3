@extends('layouts.hrms')

@section('title', 'Create Payroll')

@section('content')
    <div class="space-y-6" x-data="payrollCalculator()">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Create Payroll</h1>
                <p class="text-gray-600 text-sm mt-1">Generate payroll for an employee</p>
            </div>
            <a href="{{ route('payroll.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Back to Payroll
            </a>
        </div>

        <form method="POST" action="{{ route('payroll.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <!-- Left Column: Form Inputs -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Employee Selection -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Employee Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Employee*</label>
                            <select name="employee_id" required x-model="selectedEmployeeId" @change="updateEmployeeData()"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                <option value="">Choose an employee...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-hourly-rate="{{ $emp->hourly_rate ?? 0 }}"
                                        data-designation="{{ $emp->designation->name ?? 'N/A' }}">
                                        {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate</label>
                                <input type="text" x-model="'₱' + parseFloat(hourlyRate).toFixed(2)" readonly
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                <input type="text" x-model="designation" readonly
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pay Period -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Pay Period</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month/Year*</label>
                            <input type="text" name="month_year" value="{{ $currentMonth }}" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            @error('month_year')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date*</label>
                                <input type="date" name="start_date" required x-model="startDate"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date*</label>
                                <input type="date" name="end_date" required x-model="endDate"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Note: Total hours will be calculated from attendance records within
                            this period</p>
                    </div>
                </div>

                <!-- Allowances -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Allowances</h3>
                    <div class="space-y-2">
                        @forelse($allowances as $allowance)
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                <input type="checkbox" name="allowances[]" value="{{ $allowance->id }}"
                                    @change="toggleAllowance({{ $allowance->id }}, {{ $allowance->amount }})"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-3 flex-1 text-sm text-gray-900">{{ $allowance->name }}</span>
                                <span
                                    class="text-sm font-bold text-green-600">₱{{ number_format($allowance->amount, 2) }}</span>
                            </label>
                        @empty
                            <p class="text-gray-500 text-sm">No allowances available. <a href="{{ route('payroll.settings') }}"
                                    class="text-green-600 hover:underline">Add allowances</a></p>
                        @endforelse
                    </div>
                </div>

                <!-- Deductions -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Deductions</h3>
                    <div class="space-y-2">
                        @forelse($deductions as $deduction)
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                <input type="checkbox" name="deductions[]" value="{{ $deduction->id }}"
                                    @change="toggleDeduction({{ $deduction->id }}, '{{ $deduction->type }}', {{ $deduction->value }})"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-3 flex-1 text-sm text-gray-900">{{ $deduction->name }}</span>
                                <span class="text-sm font-bold text-red-600">
                                    @if($deduction->type === 'percentage')
                                        {{ $deduction->value }}%
                                    @else
                                        ₱{{ number_format($deduction->value, 2) }}
                                    @endif
                                </span>
                            </label>
                        @empty
                            <p class="text-gray-500 text-sm">No deductions available. <a href="{{ route('payroll.settings') }}"
                                    class="text-green-600 hover:underline">Add deductions</a></p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Calculation Preview -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Calculation Preview</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Hourly Rate:</span>
                            <span class="font-bold text-gray-900" x-text="'₱' + parseFloat(hourlyRate).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Hours:</span>
                            <span class="font-bold text-gray-900" x-text="totalHours + ' hrs'"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Base Pay:</span>
                            <span class="font-bold text-green-600" x-text="'₱' + basePay.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Allowances:</span>
                            <span class="font-bold text-green-600" x-text="'₱' + totalAllowances.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b-2 border-gray-200">
                            <span class="text-sm font-bold text-gray-700">Gross Pay:</span>
                            <span class="font-bold text-lg text-green-600" x-text="'₱' + grossPay.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b-2 border-gray-200">
                            <span class="text-sm text-gray-600">Deductions:</span>
                            <span class="font-bold text-red-600" x-text="'₱' + totalDeductions.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base font-bold text-gray-900">Net Salary:</span>
                            <span class="font-bold text-2xl text-green-600" x-text="'₱' + netSalary.toFixed(2)"></span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold">
                        Create Payroll
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function payrollCalculator() {
            return {
                selectedEmployeeId: '',
                hourlyRate: 0,
                designation: 'N/A',
                totalHours: 0,
                startDate: '',
                endDate: '',
                selectedAllowances: [],
                selectedDeductions: [],

                get basePay() {
                    return this.hourlyRate * this.totalHours;
                },

                get totalAllowances() {
                    return this.selectedAllowances.reduce((sum, a) => sum + a.amount, 0);
                },

                get grossPay() {
                    return this.basePay + this.totalAllowances;
                },

                get totalDeductions() {
                    return this.selectedDeductions.reduce((sum, d) => {
                        if (d.type === 'percentage') {
                            return sum + (this.grossPay * d.value / 100);
                        }
                        return sum + d.value;
                    }, 0);
                },

                get netSalary() {
                    return Math.max(0, this.grossPay - this.totalDeductions);
                },

                updateEmployeeData() {
                    const select = document.querySelector('select[name="employee_id"]');
                    const option = select.options[select.selectedIndex];

                    if (option.value) {
                        this.hourlyRate = parseFloat(option.dataset.hourlyRate) || 0;
                        this.designation = option.dataset.designation || 'N/A';
                    } else {
                        this.hourlyRate = 0;
                        this.designation = 'N/A';
                    }
                },

                toggleAllowance(id, amount) {
                    const index = this.selectedAllowances.findIndex(a => a.id === id);
                    if (index > -1) {
                        this.selectedAllowances.splice(index, 1);
                    } else {
                        this.selectedAllowances.push({ id, amount });
                    }
                },

                toggleDeduction(id, type, value) {
                    const index = this.selectedDeductions.findIndex(d => d.id === id);
                    if (index > -1) {
                        this.selectedDeductions.splice(index, 1);
                    } else {
                        this.selectedDeductions.push({ id, type, value });
                    }
                }
            }
        }
    </script>
@endsection