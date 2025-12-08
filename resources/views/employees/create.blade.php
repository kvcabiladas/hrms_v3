@extends('layouts.hrms')

@section('title', 'Add New Employee')

@section('content')

    <!-- BACK BUTTON -->
    <div class="mb-6">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back
        </a>
    </div>
    <div class="max-w-4xl mx-auto">

        <!-- Error Display -->
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc list-inside text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('employees.store') }}" method="POST" x-data="{ dept: '' }">
                @csrf

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name*</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name*</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email*</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone*</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                            placeholder="" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address*</label>
                        <textarea name="address" required rows="3"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 resize-none overflow-y-auto"
                            placeholder="Enter complete address">{{ old('address') }}</textarea>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 pt-4">Job Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" x-model="dept" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                            <option value="other" class="font-bold text-blue-600 bg-blue-50">+ Others (Add New)</option>
                        </select>
                        <div x-show="dept === 'other'" class="mt-3" style="display: none;">
                            <label class="block text-xs font-bold text-blue-600 mb-1 uppercase tracking-wide">New Department
                                Name</label>
                            <input type="text" name="new_department" placeholder="Enter new department..."
                                class="w-full px-4 py-2 border-2 border-blue-200 bg-blue-50 rounded-lg focus:outline-none focus:border-blue-500 text-blue-800 placeholder-blue-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="designation_id" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                        <input type="date" name="joining_date" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary</label>
                        <input type="number" name="basic_salary" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- SECURITY CHECK SECTION -->
                <div class="mt-8 pt-6 border-t-2 border-yellow-100 bg-yellow-50 rounded-xl p-6">
                    <h4 class="text-sm font-bold text-yellow-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        HR Authorization Required
                    </h4>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-yellow-700 mb-1">Enter your personal 8-digit HR
                                Access Code to confirm creation</label>
                            <input type="password" name="hr_access_code" required
                                class="w-full px-4 py-2 border-2 border-yellow-200 bg-white rounded-lg focus:outline-none focus:border-yellow-500 text-gray-800 tracking-widest font-mono placeholder-gray-300"
                                placeholder="XXXXXXXX" maxlength="8">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="px-8 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-md transition-transform transform hover:scale-105">
                                Confirm & Create
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection