@extends('layouts.hrms')

@section('title', 'My Attendance')

@section('content')

    <!-- 1. Monthly Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Days Present</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['present'] }} <span
                        class="text-xs font-normal text-gray-400">this month</span></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Late Arrivals</p>
                <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
            </div>
            <div class="flex-1 text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Hours</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['hours'], 1) }} <span
                        class="text-xs font-normal text-gray-400">hrs</span></h3>
            </div>
        </div>
    </div>

    <!-- 2. Clock In/Out Action Area -->
    <div
        class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl shadow-lg p-8 mb-8 text-white flex flex-col md:flex-row justify-between items-center">
        <div class="mb-6 md:mb-0">
            <h2 class="text-2xl font-bold mb-1">Current Time (24h)</h2>
            <p class="text-gray-400 text-sm">{{ now()->format('l, F j, Y') }}</p>
            <!-- 24-Hour Digital Clock -->
            <div class="text-5xl font-mono font-bold mt-4 tracking-widest" id="liveClock">00:00:00</div>
        </div>

        <div>
            @php
                // Check if user has an active session today
                $todayRecord = $attendances->first(fn($a) => $a->date->isToday());
            @endphp

            @if(!$todayRecord)
                <!-- Not Clocked In Yet -->
                <form action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <button
                        class="px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-full font-bold text-lg shadow-lg transform transition hover:scale-105 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        CLOCK IN
                    </button>
                </form>
            @elseif(!$todayRecord->clock_out)
                <!-- Clocked In, Needs to Clock Out -->
                <div class="text-center">
                    <p class="text-green-400 text-sm mb-3 font-bold uppercase tracking-wide">
                        <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-1"></span> Shift Active
                    </p>
                    <form action="{{ route('attendance.update', $todayRecord->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button
                            class="px-8 py-4 bg-red-500 hover:bg-red-600 text-white rounded-full font-bold text-lg shadow-lg transform transition hover:scale-105 flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            CLOCK OUT
                        </button>
                    </form>
                </div>
            @else
                <!-- Completed for the day -->
                <div class="px-8 py-4 bg-gray-700 rounded-full font-bold text-gray-300 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Shift Completed
                </div>
            @endif
        </div>
    </div>

    <!-- 3. Attendance History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ 
                sortBy: '', 
                startDate: '', 
                endDate: '' 
            }">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">My Attendance History</h3>
            </div>

            <!-- Filter Controls -->
            <div class="flex gap-3 items-center">
                <div class="flex gap-2 items-center">
                    <label class="text-sm text-gray-600">From:</label>
                    <input type="date" x-model="startDate"
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                </div>
                <div class="flex gap-2 items-center">
                    <label class="text-sm text-gray-600">To:</label>
                    <input type="date" x-model="endDate"
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                </div>
                <select x-model="sortBy"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                    <option value="">Sort By</option>
                    <option value="date_desc">Date (Newest First)</option>
                    <option value="date_asc">Date (Oldest First)</option>
                    <option value="hours_desc">Hours (High to Low)</option>
                    <option value="hours_asc">Hours (Low to High)</option>
                </select>
            </div>
        </div>
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Clock In</th>
                    <th class="px-6 py-4">Clock Out</th>
                    <th class="px-6 py-4">Duration</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $record->date->format('M d, Y') }}</td>

                        <!-- 24-HOUR FORMAT (H:i) -->
                        <td class="px-6 py-4 font-mono text-green-600">
                            {{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 font-mono text-red-500">
                            {{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '--:--' }}
                        </td>

                        <td class="px-6 py-4">
                            @if($record->total_hours)
                                <span class="font-bold text-gray-800">{{ $record->total_hours }}</span> hrs
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-medium 
                                                    {{ $record->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    </div>

    <!-- Live Clock Script (24-Hour) -->
    <script>
        function updateClock() {
            const now = new Date();
            // Force 24-hour format (e.g., 14:30:00 instead of 2:30:00 PM)
            const timeString = now.toLocaleTimeString('en-GB', { hour12: false });
            document.getElementById('liveClock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock(); // Run immediately
    </script>
@endsection