@extends('layouts.hrms')

@section('title', 'Leave Request Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('leaves.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Leave Management
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Leave Request Details</h2>

            <!-- Employee Info -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Employee</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->employee->first_name }}
                        {{ $leave->employee->last_name }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Department</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->employee->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Leave Type</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->type->name ?? 'General' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->days }} Days</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Start Date</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->start_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">End Date</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $leave->end_date->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Reason -->
            <div class="mb-6">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Reason</label>
                <p class="text-sm text-gray-900 mt-2 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $leave->reason }}
                </p>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status</label>
                <p class="text-sm mt-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        @if($leave->status === 'approved') bg-green-100 text-green-700
                        @elseif($leave->status === 'rejected') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700
                        @endif">
                        {{ ucfirst($leave->status) }}
                    </span>
                </p>
            </div>

            <!-- Actions (if pending and user is HR) -->
            @if($leave->status === 'pending' && Auth::user()->role !== 'employee')
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST" class="flex-1">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit"
                            class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm">
                            Approve Leave
                        </button>
                    </form>
                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST" class="flex-1">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit"
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition shadow-sm">
                            Reject Leave
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection