@extends('layouts.hrms')

@section('title', 'New Leave Request')

@section('content')

<!-- BACK BUTTON -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back
            </a>
        </div>
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h3 class="font-bold text-lg text-gray-800 mb-6">Apply for Leave</h3>
        
        <form action="{{ route('leaves.store') }}" method="POST">
            @csrf

            
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select name="leave_type_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->days_allowed }} days)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Relief Officer (Optional)</label>
                <select name="relief_officer_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    <option value="">Select Relief Officer</option>
                    @foreach($reliefOfficers as $officer)
                        <option value="{{ $officer->id }}">{{ $officer->first_name }} {{ $officer->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('leaves.index') }}" class="px-6 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-sm">Submit Request</button>
            </div>
        </form>
    </div>
@endsection