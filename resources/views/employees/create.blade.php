@extends('layouts.hrms')

@section('title', 'Add New Employee')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('employees.store') }}" method="POST" x-data="{ dept: '' }">
                @csrf
                
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    
                    <!-- Phone: Numbers Only -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500" placeholder="09123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" name="address" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 pt-4">Job Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Department Dropdown with "Others" Logic -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" x-model="dept" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                            <option value="other" class="font-bold text-blue-600 bg-blue-50">+ Others (Add New)</option>
                        </select>
                        
                        <!-- Hidden Input that appears when "Others" is selected -->
                        <div x-show="dept === 'other'" class="mt-3" style="display: none;">
                            <label class="block text-xs font-bold text-blue-600 mb-1 uppercase tracking-wide">New Department Name</label>
                            <input type="text" name="new_department" placeholder="Enter new department..." class="w-full px-4 py-2 border-2 border-blue-200 bg-blue-50 rounded-lg focus:outline-none focus:border-blue-500 text-blue-800 placeholder-blue-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="designation_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                        <input type="date" name="joining_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary</label>
                        <input type="number" name="basic_salary" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <a href="{{ route('employees.index') }}" class="px-6 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Create Employee</button>
                </div>
            </form>
        </div>
    </div>
@endsection
```

### **Final Step: Re-seed Database**
To get your specific departments (HR, IT, Sales, etc.) loaded, run:

```bash
php artisan migrate:fresh --seed