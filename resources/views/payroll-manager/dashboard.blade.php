@extends('layouts.hrms')

@section('title', 'Payroll Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Payroll Dashboard</h1>
                <p class="text-gray-600 text-sm mt-1">Overview of payroll analytics and insights</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Current Period</p>
                <p class="text-lg font-bold text-green-600">{{ $currentMonth }}</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Monthly Payroll -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Monthly Payroll</p>
                        <p class="text-lg font-bold text-green-600">₱{{ number_format($totalMonthlyPayroll, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Yearly Payroll -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Yearly Total</p>
                        <p class="text-lg font-bold text-blue-600">₱{{ number_format($totalYearlyPayroll, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Payrolls -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-yellow-50 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Pending</p>
                        <p class="text-lg font-bold text-yellow-600">{{ $pendingPayrolls }}</p>
                    </div>
                </div>
            </div>

            <!-- Paid This Month -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Paid</p>
                        <p class="text-lg font-bold text-purple-600">{{ $paidPayrolls }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trend Chart -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">6-Month Payroll Trend</h3>
                <canvas id="monthlyTrendChart" height="250"></canvas>
            </div>

            <!-- Department Breakdown -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Department Payroll Breakdown</h3>
                <div class="space-y-3">
                    @foreach($departmentPayroll as $dept)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $dept['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $dept['employee_count'] }} employees</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">₱{{ number_format($dept['total_salary'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Payroll Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Recent Payroll Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Basic Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Net Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentPayrolls as $payroll)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-900">
                                    {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $payroll->month_year }}</td>
                                <td class="px-6 py-4 text-gray-900">₱{{ number_format($payroll->basic_salary, 2) }}</td>
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
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No payroll transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Trend Chart
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyTrend, 'month')) !!},
                datasets: [{
                    label: 'Total Payroll',
                    data: {!! json_encode(array_column($monthlyTrend, 'total')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection