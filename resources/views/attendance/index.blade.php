@extends('layouts.hrms')

@section('title', 'Attendance Log')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf
            <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Clock In Today
            </button>
        </form>
        <div class="text-sm text-gray-500">
            Current Server Time: <span class="font-mono font-bold text-gray-800">{{ now()->format('h:i A') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Clock In</th>
                    <th class="px-6 py-4">Clock Out</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($attendances as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                    <td class="px-6 py-4">{{ $record->date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 font-mono text-green-600">{{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-4 font-mono text-red-500">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('h:i A') : '--:--' }}</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600">{{ ucfirst($record->status) }}</span></td>
                    <td class="px-6 py-4 text-right">
                        @if(!$record->clock_out && $record->date->isToday())
                        <form action="{{ route('attendance.update', $record->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button class="text-red-600 hover:underline text-xs font-bold">Clock Out</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">{{ $attendances->links() }}</div>
    </div>
@endsection