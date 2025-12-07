@extends('layouts.hrms')

@section('title', 'Leave Settings')

@section('content')
 <!-- BACK BUTTON -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back
            </a>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: Create New Type -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h3 class="font-bold text-gray-800 mb-4">Create Leave Type</h3>
            <form action="{{ route('leaves.store_type') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Leave Plan Name</label>
                    <input type="text" name="name" placeholder="e.g. Maternity" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                    <input type="number" name="days_allowed" placeholder="e.g. 60" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow-sm">
                    Create
                </button>
            </form>
        </div>

        <!-- Right: List of Types -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Manage Leave Settings</h3>
            </div>
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Leave Plan</th>
                        <th class="px-6 py-4">Duration(s)</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($types as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $type->name }}</td>
                        <td class="px-6 py-4">{{ $type->days_allowed }} Days</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-blue-600 font-bold text-xs border border-blue-200 bg-blue-50 px-3 py-1 rounded hover:bg-blue-100">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection