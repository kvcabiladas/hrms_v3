@extends('layouts.hrms')

@section('title', 'My Leaves')

@section('content')
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-bold text-gray-800">My Leave Requests</h2>
        </div>

        <!-- Request Leave Button -->
        <a href="{{ route('leaves.create') }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Request Leave
        </a>
    </div>

    <!-- Leave Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($leaveTypes as $type)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-gray-800">{{ $type->name }}</h3>
                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-medium">{{ $type->days_allowed }}
                        days/year</span>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Used</span>
                        <span class="font-bold text-gray-800">{{ $type->days_used ?? 0 }} / {{ $type->days_allowed }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full"
                            style="width: {{ min(100, (($type->days_used ?? 0) / $type->days_allowed) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- My Leave History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-800">My Leave History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4">Start Date</th>
                        <th class="px-6 py-4">End Date</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Reason</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leaves as $leave)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-xs font-bold">{{ $leave->days }}
                                            Days</span>
                                    </td>
                                    <td class="px-6 py-4">{{ $leave->start_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $leave->end_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 border border-gray-200 rounded text-xs text-gray-600">{{ $leave->type->name ?? 'General' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 truncate max-w-xs" title="{{ $leave->reason }}">
                                        {{ Str::limit($leave->reason, 30) }}</td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold 
                                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' :
                        ($leave->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            ($leave->status === 'recalled' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700')) }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if($leave->status === 'pending')
                                            <form action="{{ route('leaves.cancel', $leave->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                @csrf @method('PUT')
                                                <button
                                                    class="text-xs bg-gray-100 text-gray-600 border border-gray-300 px-3 py-1 rounded hover:bg-gray-200 hover:text-red-600 font-medium transition">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 bg-gray-50">No leave requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $leaves->links() }}</div>
    </div>
@endsection