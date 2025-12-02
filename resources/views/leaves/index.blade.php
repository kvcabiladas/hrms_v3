@extends('layouts.hrms')

@section('title', 'My Time Off')

@section('content')
    <div class="flex justify-end mb-6">
        <a href="{{ route('leaves.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Request Time Off
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Leave Type</th>
                    <th class="px-6 py-4">Date Range</th>
                    <th class="px-6 py-4">Days</th>
                    <th class="px-6 py-4">Reason</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $leave->type }}</td>
                    <td class="px-6 py-4">
                        {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} Days</td>
                    <td class="px-6 py-4 text-gray-500 truncate max-w-xs">{{ $leave->reason }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                            {{ $leave->status === 'approved' ? 'bg-green-50 text-green-600' : 
                              ($leave->status === 'pending' ? 'bg-yellow-50 text-yellow-600' : 'bg-red-50 text-red-600') }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No leave requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
    </div>
@endsection