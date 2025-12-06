@extends('layouts.hrms')

@section('title', 'Leave Management')

@section('content')
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        
        <!-- Navigation Tabs -->
        <div class="flex bg-gray-100 p-1 rounded-lg">
            @if(Auth::user()->role !== 'employee')
                <a href="{{ route('leaves.settings') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Leave Settings</a>
            @endif
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition cursor-not-allowed" disabled>Leave Recall</button>
            <button class="px-4 py-2 bg-white shadow-sm rounded-md text-sm font-bold text-gray-800">Leave History</button>
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition cursor-not-allowed" disabled>Relief Officers</button>
        </div>

        <!-- Request Button -->
        <a href="{{ route('leaves.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Request
        </a>
    </div>

    <!-- Ongoing Leave Applications Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ showRecallModal: false, selectedLeave: null }">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-800">Ongoing Leave Applications</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Name(s)</th>
                        <th class="px-6 py-4">Duration(s)</th>
                        <th class="px-6 py-4">Start Date</th>
                        <th class="px-6 py-4">End Date</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Reason(s)</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-xs font-bold">{{ $leave->days }} Days</span>
                        </td>
                        <td class="px-6 py-4">{{ $leave->start_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $leave->end_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 border border-gray-200 rounded text-xs text-gray-600">{{ $leave->type->name ?? 'General' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 truncate max-w-xs" title="{{ $leave->reason }}">{{ Str::limit($leave->reason, 20) }}</td>
                        
                        <td class="px-6 py-4 text-center">
                            
                            <!-- 1. HR RECALL BUTTON (Only for HR on Approved Leaves) -->
                            @if($leave->status === 'approved' && Auth::user()->role !== 'employee')
                                <button @click="showRecallModal = true; selectedLeave = {{ json_encode([
                                    'id' => $leave->id,
                                    'name' => $leave->employee->first_name . ' ' . $leave->employee->last_name,
                                    'dept' => $leave->employee->department->name ?? 'N/A',
                                    'start' => $leave->start_date->format('Y-m-d'),
                                    'end' => $leave->end_date->format('Y-m-d'),
                                    'days_remaining' => \Carbon\Carbon::now()->diffInDays($leave->end_date, false) > 0 ? \Carbon\Carbon::now()->diffInDays($leave->end_date) : 0,
                                    'relief' => $leave->reliefOfficer ? $leave->reliefOfficer->first_name . ' ' . $leave->reliefOfficer->last_name : 'None'
                                ]) }}" 
                                class="bg-red-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 transition shadow-sm border border-red-600">
                                    Recall
                                </button>

                            <!-- 2. APPROVE/REJECT (For Pending - HR Only) -->
                            @elseif($leave->status === 'pending' && Auth::user()->role !== 'employee')
                                <div class="flex justify-center gap-2">
                                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-700">Approve</button>
                                    </form>
                                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-bold hover:bg-gray-300">Reject</button>
                                    </form>
                                </div>
                            
                            <!-- 3. CANCEL BUTTON (For Pending - Employee Only) [NEWLY ADDED] -->
                            @elseif($leave->status === 'pending' && Auth::user()->role === 'employee')
                                <form action="{{ route('leaves.cancel', $leave->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                    @csrf @method('PUT')
                                    <button class="text-xs bg-gray-100 text-gray-600 border border-gray-300 px-3 py-1 rounded hover:bg-gray-200 hover:text-red-600 font-medium transition">
                                        Cancel Request
                                    </button>
                                </form>

                            <!-- 4. STATUS BADGES (Default View) -->
                            @else
                                <span class="px-2 py-1 rounded text-xs font-bold 
                                    {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : 
                                      ($leave->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                      ($leave->status === 'recalled' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            @endif

                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 bg-gray-50">No active leave applications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $leaves->links() }}</div>

        <!-- ========================== -->
        <!-- RECALL MODAL (Alpine.js) -->
        <!-- ========================== -->
        <div x-show="showRecallModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Overlay -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showRecallModal = false">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <div class="bg-white p-6">
                        <div class="flex items-center mb-6">
                            <div class="rounded-full bg-red-100 p-3 mr-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Initiate Leave Recall</h3>
                                <p class="text-sm text-gray-500">This will notify the employee to return to work.</p>
                            </div>
                        </div>

                        <!-- Dynamic Form Action -->
                        <form x-bind:action="'/leaves/' + selectedLeave?.id" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="recall" value="true">

                            <div class="space-y-4">
                                <!-- Read Only Info -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Employee</label>
                                    <input type="text" x-bind:value="selectedLeave?.name" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-medium">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Department</label>
                                        <input type="text" x-bind:value="selectedLeave?.dept" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Remaining Days</label>
                                        <input type="text" x-bind:value="selectedLeave?.days_remaining" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-bold text-red-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Original End</label>
                                        <input type="text" x-bind:value="selectedLeave?.end" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                    </div>
                                    
                                    <!-- ACTION FIELD -->
                                    <div>
                                        <label class="block text-xs font-bold text-green-700 uppercase tracking-wider mb-1">New Return Date</label>
                                        <input type="date" name="recalled_date" required class="w-full px-4 py-2 border-2 border-green-500 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 shadow-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Relief Officer</label>
                                    <input type="text" x-bind:value="selectedLeave?.relief" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                </div>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <button type="button" @click="showRecallModal = false" class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-lg font-bold text-sm hover:bg-red-700 shadow-md transition transform hover:scale-105">
                                    Confirm Recall
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection