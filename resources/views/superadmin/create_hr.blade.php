@extends('layouts.hrms')

@section('title', 'Create HR Personnel')

@section('content')
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('superadmin.dashboard') }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Create HR Personnel</h2>

            <form action="{{ route('superadmin.storeHr') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" required oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" required rows="3"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none resize-none overflow-y-auto"
                            placeholder="Enter complete address"></textarea>
                    </div>

                    <!-- Joining Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                        <input type="date" name="joining_date" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="px-6 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md transition">
                        Create HR Personnel
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection