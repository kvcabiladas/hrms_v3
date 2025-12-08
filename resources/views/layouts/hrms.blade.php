<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRMS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800" x-data="{ 
          sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' || localStorage.getItem('sidebarOpen') === null,
          tooltipVisible: false,
          tooltipText: '',
          tooltipTop: 0,
          tooltipLeft: 0,
          
          init() {
              this.$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
          },
          
          showTooltip(event, text) {
              if (this.sidebarOpen) return;
              const rect = event.currentTarget.getBoundingClientRect();
              this.tooltipText = text;
              this.tooltipTop = rect.top + (rect.height / 2);
              this.tooltipLeft = rect.right + 10;
              this.tooltipVisible = true;
          },
          
          hideTooltip() {
              this.tooltipVisible = false;
          }
      }" x-init="init()">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-gray-200 flex flex-col shrink-0 transition-all duration-300 ease-in-out z-20">
            <div class="h-16 flex items-center px-6 border-b border-gray-100 justify-between">
                <div
                    class="text-2xl font-bold text-green-600 flex items-center gap-2 overflow-hidden whitespace-nowrap">
                    <span
                        class="bg-green-600 text-white min-w-[2rem] h-8 w-8 rounded flex items-center justify-center text-sm">H</span>
                    <span x-show="sidebarOpen" class="transition-opacity duration-300 delay-100">HRMS</span>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto overflow-x-visible scrollbar-hide">

                @php
                    $linkClasses = "flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors duration-200 whitespace-nowrap relative";
                    $activeClasses = "bg-green-50 text-green-700 font-medium";
                    $inactiveClasses = "text-gray-600 hover:bg-gray-50";

                    // Specific styles for sections
                    $sectionHeader = "px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 transition-opacity";
                    $personalHeader = "px-3 text-xs font-bold text-blue-500 uppercase tracking-wider mb-2 mt-4 transition-opacity";
                    $companyHeader = "px-3 text-xs font-bold text-purple-500 uppercase tracking-wider mb-2 mt-4 transition-opacity";
                @endphp

                {{-- SCENARIO 1: SUPER ADMIN --}}
                @if(Auth::user()->role === 'super_admin')
                    <div class="mb-6">
                        <p x-show="sidebarOpen" class="{{ $sectionHeader }}">Super Admin</p>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('superadmin.dashboard') ? $activeClasses : $inactiveClasses }}"
                            @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                            <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            <span x-show="sidebarOpen">Dashboard</span>
                        </a>
                    </div>

                    {{-- SCENARIO 2: HR PERSONNEL (Split Menu) --}}
                @elseif(Auth::user()->role === 'hr')

                    {{-- COMPANY MENU FIRST --}}
                    <p x-show="sidebarOpen" class="{{ $companyHeader }}">Company</p>

                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('employees.index') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('employees.*') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Employees')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Employees</span>
                    </a>

                    <a href="{{ route('leaves.index') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('leaves.*') && !request()->routeIs('personal.leaves') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Leave Management')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Leave Management</span>
                    </a>

                    {{-- PERSONAL MENU SECOND --}}
                    <p x-show="sidebarOpen" class="{{ $personalHeader }}">Personal</p>

                    <a href="{{ route('personal.attendance') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.attendance') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Attendance')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen">My Attendance</span>
                    </a>

                    <a href="{{ route('personal.leaves') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.leaves') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Leaves')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Leaves</span>
                    </a>

                    <a href="{{ route('personal.payroll') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.payroll') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Payroll')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Payroll</span>
                    </a>

                    {{-- SCENARIO 3: ACCOUNTANT/PAYROLL MANAGER --}}
                @elseif(Auth::user()->role === 'accountant')

                    {{-- COMPANY MENU FIRST --}}
                    <p x-show="sidebarOpen" class="{{ $companyHeader }}">Company</p>

                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('payroll.index') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('payroll.*') && !request()->routeIs('personal.payroll') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Payroll Management')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Payroll Management</span>
                    </a>

                    {{-- PERSONAL MENU SECOND --}}
                    <p x-show="sidebarOpen" class="{{ $personalHeader }}">Personal</p>

                    <a href="{{ route('personal.attendance') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.attendance') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Attendance')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen">My Attendance</span>
                    </a>

                    <a href="{{ route('personal.leaves') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.leaves') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Leaves')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Leaves</span>
                    </a>

                    <a href="{{ route('personal.payroll') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.payroll') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Payroll')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Payroll</span>
                    </a>

                    {{-- SCENARIO 4: REGULAR EMPLOYEE --}}
                @else
                    <p x-show="sidebarOpen" class="{{ $sectionHeader }}">Menu</p>

                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('personal.attendance') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.attendance') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Attendance')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen">My Attendance</span>
                    </a>

                    <a href="{{ route('personal.leaves') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.leaves') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Leaves')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Leaves</span>
                    </a>

                    <a href="{{ route('personal.payroll') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('personal.payroll') ? $activeClasses : $inactiveClasses }}"
                        @mouseenter="showTooltip($event, 'My Payroll')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen">My Payroll</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            <!-- Top Bar code remains same... -->
            <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 h-16 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">@yield('title')</h1>
                </div>
                <!-- Notification Bell -->
                <div class="relative mr-4" x-data="{ showNotifications: false }">
                    <button @click="showNotifications = !showNotifications" @click.outside="showNotifications = false"
                        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="showNotifications" x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden flex flex-col"
                        style="display: none;">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-bold text-gray-900">Notifications</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->unreadNotifications()->count() }}
                                unread</p>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            @forelse(auth()->user()->notifications()->limit(10)->get() as $notification)
                                <div
                                    class="p-4 border-b border-gray-100 hover:bg-gray-50 transition {{ $notification->read ? 'bg-white' : 'bg-blue-50' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            @if($notification->type === 'leave_approved')
                                                <div
                                                    class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @elseif($notification->type === 'leave_rejected')
                                                <div
                                                    class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @elseif($notification->type === 'payroll_posted')
                                                <div
                                                    class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-400 mt-2">
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="text-sm">No notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 uppercase">
                                {{ Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::user()->role === 'hr' ? 'HR Personnel' : 'Employee') }}
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm cursor-pointer hover:bg-green-200 transition">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>
                    <div x-show="open"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-100 z-50"
                        style="display: none;">
                        <a href="{{ route('settings.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Floating Toast Notifications (Upper-Right) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            class="fixed top-6 right-6 z-50 max-w-md w-full sm:w-96 bg-white rounded-lg shadow-2xl border-l-4 border-green-500 p-4 flex items-start gap-3"
            style="display: none;">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-semibold text-gray-900">Success!</p>
                <p class="text-sm text-gray-600 mt-1">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            class="fixed top-6 right-6 z-50 max-w-md w-full sm:w-96 bg-white rounded-lg shadow-2xl border-l-4 border-red-500 p-4 flex items-start gap-3"
            style="display: none;">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-semibold text-gray-900">Error!</p>
                <p class="text-sm text-gray-600 mt-1">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Floating Tooltip -->
    <div x-show="tooltipVisible" x-cloak
        class="fixed z-50 px-2.5 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-md shadow-xl pointer-events-none transform -translate-y-1/2"
        :style="`top: ${tooltipTop}px; left: ${tooltipLeft}px;`">
        <span x-text="tooltipText"></span>
        <div class="absolute left-0 top-1/2 transform -translate-x-1 -translate-y-1/2 w-2 h-2 bg-gray-900 rotate-45">
        </div>
    </div>
</body>

</html>