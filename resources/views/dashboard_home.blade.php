@extends('layouts.hrms')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Stat Card 1 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Employees</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Present Today</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $presentToday }}</h3>
                </div>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Leaves</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $pendingLeaves }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl p-8 text-white shadow-lg">
        <h2 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="opacity-90">Here is what is happening in your company today.</p>
        
        <div class="mt-6 flex gap-3">
            <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-white text-green-600 font-medium rounded-lg hover:bg-green-50 transition">Add Employee</a>
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-green-700 text-white font-medium rounded-lg hover:bg-green-800 transition">View Attendance</a>
        </div>
    </div>
@endsection