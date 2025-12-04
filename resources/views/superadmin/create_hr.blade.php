@extends('layouts.hrms')

@section('title', 'Create HR Personnel')

@section('content')
    <div class="max-w-4xl mx-auto">
        
        <!-- DEBUG BANNER: If you see this, the file has updated successfully 
        <div class="bg-red-600 text-white text-center py-2 font-bold mb-6 rounded-lg shadow-md">
            ✅ FILE UPDATED: AUTO-GENERATION ENABLED
        </div> -->

        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
            <p class="text-sm text-purple-700">
                <strong>System Generated:</strong> Username, Password, Employee ID, Access Code, and Salary (0) will be handled automatically.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('superadmin.storeHr') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name*</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name*</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address*</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number*</label>
                        <input type="text" name="phone" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address*</label>
                        <input type="text" name="address" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>
                    
                    <!-- Joining Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                        <input type="date" name="joining_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="designation_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none">
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <a href="{{ route('superadmin.dashboard') }}" class="mr-4 px-6 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium shadow-md transition">
                        Generate & Create HR
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection