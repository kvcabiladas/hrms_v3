@extends('layouts.hrms')

@section('title', 'Employee Created')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <div class="bg-white rounded-xl shadow-lg border border-green-200 overflow-hidden">
            <div class="bg-green-600 px-8 py-6 text-white text-center">
                <h2 class="text-2xl font-bold">Employee Account Ready</h2>
                <p class="opacity-90 mt-1">Share these credentials with the employee.</p>
            </div>

            <div class="p-8 space-y-4">
                <div class="text-center pb-4 border-b border-gray-100 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ $name }}</h3>
                    <p class="font-mono text-gray-500">{{ $employee_id }}</p>
                </div>

                <!-- Username -->
                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Username</p>
                        <p class="text-lg font-mono font-bold text-gray-800 select-all">{{ $username }}</p>
                    </div>
                </div>

                <!-- Password -->
                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Temporary Password</p>
                        <p class="text-lg font-mono font-bold text-gray-800 select-all">{{ $password }}</p>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <a href="{{ route('employees.index') }}" class="block w-full py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition font-medium">
                        Done
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection