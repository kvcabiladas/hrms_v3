@extends('layouts.hrms')

@section('title', 'Employee Profile')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Sidebar: Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-3xl mx-auto mb-4 uppercase">
                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-gray-500 text-sm mb-4">{{ $employee->designation->name ?? 'N/A' }}</p>
                <div class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 border border-green-100">
                    {{ ucfirst($employee->status) }}
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6 text-left space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Employee ID</span>
                        <span class="font-medium text-gray-800">{{ $employee->employee_id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Department</span>
                        <span class="font-medium text-gray-800">{{ $employee->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-800 text-xs">{{ $employee->email }}</span>
                    </div>
                </div>
            </div>

            <!-- CREDENTIALS CARD (Only visible to HR/Admin) -->
            @if(Auth::user()->role !== 'employee') 
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Login Credentials</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Username</p>
                        <div class="font-mono text-sm bg-gray-50 p-2 rounded border border-gray-200 text-gray-700 flex justify-between items-center">
                            <span>{{ $employee->user->username ?? 'N/A' }}</span>
                            @if($employee->user && $employee->user->username)
                                <button onclick="copyToClipboard('{{ $employee->user->username }}')" class="text-gray-400 hover:text-blue-600 transition" title="Copy Username">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Password Status</p>
                        @if($employee->user && $employee->user->temp_password)
                            <!-- Show Temp Password with Copy -->
                            <div class="flex items-center justify-between bg-yellow-50 p-2 rounded border border-yellow-200">
                                <span class="font-mono text-sm text-yellow-800 font-bold">{{ $employee->user->temp_password }}</span>
                                <div class="flex items-center gap-2">
                                    <button onclick="copyToClipboard('{{ $employee->user->temp_password }}')" class="text-yellow-600 hover:text-yellow-800 transition" title="Copy Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                    <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded font-bold">Temp</span>
                                </div>
                            </div>
                            <p class="text-xs text-red-500 mt-2">
                                * Visible until user changes it.
                            </p>
                        @else
                            <!-- Secure State -->
                            <div class="flex items-center gap-2 bg-green-50 p-2 rounded border border-green-200 text-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-medium">Secure (Changed by User)</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Side: Attendance & Stats -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Attendance</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Clock In</th>
                            <th class="px-4 py-2">Clock Out</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employee->attendance->take(5) as $record)
                        <tr>
                            <td class="px-4 py-3">{{ $record->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-green-600 font-mono">{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}</td>
                            <td class="px-4 py-3 text-red-500 font-mono">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '--:--' }}</td>
                            <td class="px-4 py-3"><span class="text-gray-600 font-medium">{{ ucfirst($record->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-3 text-center text-gray-400">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Copy Script -->
    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert("Copied to clipboard: " + text);
                });
            } else {
                // Fallback for older browsers
                let textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert("Copied to clipboard: " + text);
            }
        }
    </script>
@endsection