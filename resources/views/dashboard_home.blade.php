@extends('layouts.hrms')

@section('title', 'Dashboard Overview')

@section('content')
    <!-- Date/Time Display -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ now()->format('l, F j, Y • g:i A') }}
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Total Employees -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Employees</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1 text-right">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-lg flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Present Today</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-1 text-right">{{ $presentToday }}</h3>
                </div>
            </div>
        </div>

        <!-- Late Today -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Late Arrivals</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-1 text-right">{{ $lateToday ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <!-- On Leave -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 text-gray-600 rounded-lg flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01">
                        </path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">On Leave</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1 text-right">{{ $onLeaveToday ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- 2. GROWTH CHART (Left Side - 2/3 Width) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800">Recruitment Growth</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ date('Y') }}</span>
            </div>
            <div class="relative h-72 w-full">
                <!-- Data Container: Stores PHP data in HTML attributes to avoid JS syntax errors -->
                <canvas id="employeeChart" data-labels="{{ json_encode($chartLabels) }}"
                    data-counts="{{ json_encode($chartData) }}">
                </canvas>
            </div>
        </div>

        <!-- 3. ACTION REQUIRED (Right Side - 1/3 Width) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Pending Requests</h3>
                <span
                    class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingLeavesCount }}</span>
            </div>

            @if(isset($recentLeaves) && $recentLeaves->count() > 0)
                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                    @foreach($recentLeaves as $leave)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-800">{{ $leave->employee->first_name }}
                                        {{ $leave->employee->last_name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $leave->type }}</p>
                                </div>
                                <a href="{{ route('leaves.show', $leave->id) }}"
                                    class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('leaves.index') }}" class="block text-center text-sm text-blue-600 hover:underline mt-4">View
                    all requests</a>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 py-8">
                    <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">No pending requests</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('employeeChart');
            const ctx = canvas.getContext('2d');

            // Read data from HTML attributes (Clean separation of PHP and JS)
            const labels = JSON.parse(canvas.dataset.labels);
            const data = JSON.parse(canvas.dataset.counts);

            const employeeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Employees',
                        data: data,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgba(16, 185, 129, 1)',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" } },
                            grid: { color: '#f3f4f6', borderDash: [5, 5] }
                        },
                        x: {
                            ticks: { font: { family: "'Inter', sans-serif" } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection