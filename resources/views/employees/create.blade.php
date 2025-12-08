@extends('layouts.hrms')

@section('title', 'Add New Employee')

@section('content')

    <!-- BACK BUTTON -->
    <div class="mb-6">
        <a href="{{ route('employees.index', ['tab' => 'employees']) }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Employee List
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
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name*</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-4 py-2 border {{ $errors->has('first_name') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500">
                        @error('first_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name*</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-2 border {{ $errors->has('last_name') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500">
                        @error('last_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email*</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone*</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-2 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500"
                            placeholder="" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address*</label>
                        <textarea name="address" required rows="3"
                            class="w-full px-4 py-2 border {{ $errors->has('address') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500 resize-none overflow-y-auto"
                            placeholder="Enter complete address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 pt-4">Job Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" x-data="{ 
                                            selectedDept: '', 
                                            positions: {{ json_encode($designations->map(function ($d) {
        return ['id' => $d->id, 'name' => $d->name, 'department_id' => $d->department_id]; })) }}
                                        }">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department*</label>
                        <select name="department_id" x-model="selectedDept" required
                            class="w-full px-4 py-2 border {{ $errors->has('department_id') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500 bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position*</label>
                        <select name="designation_id" required
                            class="w-full px-4 py-2 border {{ $errors->has('designation_id') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500 bg-white"
                            :disabled="!selectedDept">
                            <option value="">Select Position</option>
                            <template x-for="position in positions.filter(p => p.department_id == selectedDept)"
                                :key="position.id">
                                <option :value="position.id" x-text="position.name"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" x-show="!selectedDept">Please select a department first</p>
                        @error('designation_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date*</label>
                        <input type="date" name="joining_date" value="{{ old('joining_date') }}" required
                            class="w-full px-4 py-2 border {{ $errors->has('joining_date') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500">
                        @error('joining_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        </div>

                        <div>
                            <label class=" block text-sm font-medium text-gray-700 mb-2">Gender*</label>
                            <select name="gender" required
                                class="w-full px-4 py-2 border {{ $errors->has('gender') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                    <div class="flex items-center gap-4" x-data="{ showAccessCode: false }">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-yellow-700 mb-1">Enter your personal 8-digit HR
                                Access Code to confirm creation</label>
                            <div class="relative">
                                <input :type="showAccessCode ? 'text' : 'password'" name="hr_access_code" required
                                    class="w-full px-4 py-2 pr-10 border-2 {{ $errors->has('hr_access_code') ? 'border-red-500' : 'border-yellow-200' }} bg-white rounded-lg focus:outline-none focus:border-yellow-500 text-gray-800 tracking-widest font-mono placeholder-gray-300"
                                    placeholder="XXXXXXXX" maxlength="8">
                                <button type="button" @click="showAccessCode = !showAccessCode"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg x-show="!showAccessCode" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg x-show="showAccessCode" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('hr_access_code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
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