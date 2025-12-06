@extends('layouts.hrms')

@section('title', 'Run Payroll')

@section('content')
    <form action="{{ route('payroll.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Panel: Configuration -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="font-bold text-gray-800 mb-4">1. Select Period</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payroll Month</label>
                        <input type="month" name="month" value="{{ $currentMonth }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>

                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-800 mb-1">Summary Preview</h4>
                        <div class="flex justify-between text-sm text-blue-600 mb-1">
                            <span>Employees Selected:</span>
                            <span class="font-bold" id="selected-count">0</span>
                        </div>
                        <div class="flex justify-between text-sm text-blue-600">
                            <span>Est. Total Basic:</span>
                            <span class="font-bold" id="total-payout">$0.00</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-md transition">
                            Process Payroll
                        </button>
                        <a href="{{ route('payroll.index') }}" class="block w-full py-3 text-center text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Employee List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800">2. Review Salaries</h3>
                        <div class="text-sm">
                            <label class="inline-flex items-center cursor-pointer hover:text-green-600">
                                <input type="checkbox" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500" onclick="toggleAll(this)">
                                <span class="ml-2 font-medium">Select All</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-white text-gray-700 font-medium border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 w-10"></th>
                                    <th class="px-6 py-3">Employee</th>
                                    <th class="px-6 py-3">Dept</th>
                                    <th class="px-6 py-3 text-right">Basic Salary</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($employees as $emp)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="selected_employees[]" value="{{ $emp->id }}" checked 
                                               class="emp-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4"
                                               data-row-id="{{ $emp->id }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-900">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                                        <span class="text-xs text-gray-400 font-mono">{{ $emp->employee_id }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs uppercase tracking-wide font-semibold text-gray-500">
                                        {{ $emp->department->name }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="relative max-w-[140px] ml-auto">
                                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                                            <!-- EDITABLE SALARY INPUT -->
                                            <input type="number" 
                                                   step="0.01" 
                                                   name="salaries[{{ $emp->id }}]" 
                                                   value="{{ $emp->basic_salary }}" 
                                                   class="salary-input w-full pl-6 pr-3 py-1.5 border border-gray-200 rounded text-right font-mono font-medium focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition"
                                                   oninput="updateSummary()">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.emp-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateSummary();
        }

        // Listen for checkbox changes
        document.querySelectorAll('.emp-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        // Function to calculate totals dynamically
        function updateSummary() {
            let count = 0;
            let total = 0;
            
            document.querySelectorAll('.emp-checkbox').forEach(cb => {
                if(cb.checked) {
                    count++;
                    // Find the salary input in the same row
                    // We use data-row-id to find the corresponding input, or just traverse the DOM
                    let row = cb.closest('tr');
                    let salaryInput = row.querySelector('.salary-input');
                    let val = parseFloat(salaryInput.value) || 0;
                    total += val;
                }
            });
            
            document.getElementById('selected-count').innerText = count;
            document.getElementById('total-payout').innerText = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Run initially to set correct totals
        document.addEventListener("DOMContentLoaded", function() {
            updateSummary();
        });
    </script>
@endsection