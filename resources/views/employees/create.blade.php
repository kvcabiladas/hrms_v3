@extends('layouts.hrms')

@section('title', 'Add New Employee')

@section('content')
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('employees.index') }}" class="flex items-center text-gray-500 hover:text-green-600 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="text" name="phone" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label><input type="text" name="employee_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label><input type="date" name="joining_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="active">Active</option>
                            <option value="probation">Probation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                        <select name="designation_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Salary</label><input type="number" name="basic_salary" required class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Save Employee</button>
            </form>
        </div>
    </div>
@endsection