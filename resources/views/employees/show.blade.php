@extends('layouts.hrms')

@section('title', 'Employee Profile')

@section('content')
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('employees.index') }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Employees
        </a>
    </div>

    <!-- Banner Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 relative" x-data="{ showEditModal: false }">
        <!-- Green Background Banner -->
        <div class="h-32 bg-gradient-to-r from-green-600 to-green-500"></div>

        <div class="px-8 pb-8">
            <div class="relative flex justify-center">
                <!-- Profile Picture (Centered & Overlapping) -->
                <div class="absolute -top-12 w-24 h-24 rounded-full bg-white p-1 shadow-lg">
                    <div
                        class="w-full h-full rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-2xl font-bold uppercase">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                </div>
            </div>

            <div class="mt-14 text-center relative">
                <div class="flex items-center justify-center gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                    @if(Auth::user()->role === 'hr' || Auth::user()->role === 'super_admin')
                        <button @click="showEditModal = true"
                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    @endif
                </div>
                <p class="text-green-600 font-medium">{{ $employee->designation->name ?? 'No Designation' }}</p>
                <div class="flex flex-wrap justify-center gap-4 mt-3 text-sm text-gray-600">
                    <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                            </path>
                        </svg>
                        <strong>ID:</strong> {{ $employee->employee_id }}
                    </span>
                    <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ $employee->email }}
                    </span>
                    <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        {{ $employee->department->name ?? 'N/A' }}
                    </span>
                    <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        {{ $employee->phone }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-data="{ action: 'update' }">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>

                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Employee Information</h3>

                    <form :action="action === 'terminate' ? '{{ route('employees.update', $employee->id) }}' : '{{ route('employees.update', $employee->id) }}'" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" x-model="action">
                        
                        <div class="space-y-4">
                            <div x-show="action === 'update'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                                <input type="date" name="joining_date" value="{{ $employee->joining_date->format('Y-m-d') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div x-show="action === 'update'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select name="department_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                                    @foreach(\App\Models\Department::all() as $dept)
                                        <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="action === 'update'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                <select name="designation_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                                    @foreach(\App\Models\Designation::all() as $desig)
                                        <option value="{{ $desig->id }}" {{ $employee->designation_id == $desig->id ? 'selected' : '' }}>
                                            {{ $desig->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="action === 'terminate'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Termination Reason*</label>
                                <textarea name="termination_reason" rows="3" :required="action === 'terminate'"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none"
                                    placeholder="Please provide reason for termination..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">HR Access Code* (8 digits)</label>
                                <input type="password" name="access_code" required maxlength="8" pattern="\d{8}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none"
                                    placeholder="Enter 8-digit access code">
                                <p class="text-xs text-gray-500 mt-1">Required to verify this action</p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="submit" x-show="action === 'update'"
                                class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                Save Changes
                            </button>
                            <button type="submit" x-show="action === 'terminate'"
                                class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 font-medium transition">
                                Confirm Termination
                            </button>
                            <button type="button" @click="action = action === 'update' ? 'terminate' : 'update'" x-show="action === 'update'"
                                class="px-4 bg-red-100 text-red-700 py-2 rounded-lg hover:bg-red-200 font-medium transition">
                                Terminate
                            </button>
                            <button type="button" @click="action = 'update'" x-show="action === 'terminate'"
                                class="px-4 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium transition">
                                Back
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Joining Date</p>
                    <p class="text-lg font-bold text-gray-800">{{ $employee->joining_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Leaves Taken</p>
                    <p class="text-lg font-bold text-green-600">
                        {{ $employee->leaves->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Attendance</p>
                    <p class="text-lg font-bold text-purple-600">{{ $employee->attendance->count() }} days</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Login Credentials & Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Login Credentials (Only for HR/Admin) -->
        @if(Auth::user()->role !== 'employee')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 19.464a4.335 4.335 0 00-.77.77l-1.414-1.414a2.5 2.5 0 010-3.536l6.364-6.364L10 7H7a2 2 0 00-2 2v6h2v2h2v2h2">
                        </path>
                    </svg>
                    Login Credentials
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Username</span>
                        <span
                            class="font-mono text-sm bg-gray-50 px-3 py-1 rounded">{{ $employee->user->username ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Password</span>
                        @if($employee->user && $employee->user->temp_password)
                            <div class="flex items-center gap-2">
                                <span class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded font-mono" id="tempPassword">
                                    {{ $employee->user->temp_password }}</span>
                                <button onclick="copyTempPassword('{{ $employee->user->temp_password }}')" 
                                    class="text-gray-600 hover:text-green-600 transition" title="Copy password">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        @elseif($employee->user)
                            <span class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded">Secure (Changed)</span>
                        @else
                            <span class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">No Account</span>
                        @endif
                    </div>
                    <script>
                        function copyTempPassword(text) {
                            navigator.clipboard.writeText(text).then(function() {
                                alert('Password copied to clipboard!');
                            }, function(err) {
                                console.error('Could not copy text: ', err);
                            });
                        }
                    </script>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Role</span>
                        <span
                            class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded font-bold uppercase">{{ $employee->user->role ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Attendance with Date Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Attendance
                </h3>
                <span class="text-xs text-gray-500">Last 7 days</span>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="text-gray-500 text-xs">
                    <tr>
                        <th class="pb-2">Date</th>
                        <th class="pb-2">Clock In</th>
                        <th class="pb-2">Clock Out</th>
                        <th class="pb-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employee->attendance->sortByDesc('date')->take(7) as $att)
                        <tr>
                            <td class="py-2">{{ $att->date->format('M d') }}</td>
                            <td class="py-2 font-mono text-green-600">{{ \Carbon\Carbon::parse($att->clock_in)->format('H:i') }}
                            </td>
                            <td class="py-2 font-mono text-red-500">
                                {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '--:--' }}</td>
                            <td class="py-2">
                                <span
                                    class="text-xs px-2 py-1 rounded {{ $att->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection