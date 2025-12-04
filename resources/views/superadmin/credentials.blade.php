@extends('layouts.hrms')

@section('title', 'HR Personnel Created')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-lg border border-green-200 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-green-600 px-8 py-6 text-white text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold">HR Account Created!</h2>
            <p class="opacity-90 mt-1">Please copy the credentials below.</p>
        </div>

        <div class="p-8 space-y-6">
            <!-- Name & ID -->
            <div class="text-center pb-6 border-b border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Employee Name</p>
                <h3 class="text-xl font-bold text-gray-800">{{ $name }}</h3>
                <div class="mt-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-mono font-bold">{{ $employee_id }}</span>
                </div>
            </div>

            <!-- Credentials Grid -->
            <div class="grid grid-cols-1 gap-4">
                
                <!-- Username -->
                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Username</p>
                        <p class="text-lg font-mono font-bold text-gray-800">{{ $username }}</p>
                    </div>
                </div>

                <!-- Password -->
                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Temporary Password</p>
                        <p class="text-lg font-mono font-bold text-gray-800">{{ $password }}</p>
                    </div>
                </div>

                <!-- Access Code -->
                <div class="bg-yellow-50 p-4 rounded-lg flex justify-between items-center border border-yellow-200">
                    <div>
                        <p class="text-xs text-yellow-600 uppercase font-bold tracking-wider">HR Access Code</p>
                        <p class="text-2xl font-mono font-bold text-yellow-800">{{ $access_code }}</p>
                    </div>
                </div>
            </div>

            <!-- Done Button -->
            <div class="text-center pt-4">
                <a href="{{ route('superadmin.dashboard') }}" class="block w-full py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection