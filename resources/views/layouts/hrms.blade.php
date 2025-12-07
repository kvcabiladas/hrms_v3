<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRMS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800" 
      x-data="{ 
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
      }"
      x-init="init()">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white border-r border-gray-200 flex flex-col shrink-0 transition-all duration-300 ease-in-out z-20">
            <div class="h-16 flex items-center px-6 border-b border-gray-100 justify-between">
                <div class="text-2xl font-bold text-green-600 flex items-center gap-2 overflow-hidden whitespace-nowrap">
                    <span class="bg-green-600 text-white min-w-[2rem] h-8 w-8 rounded flex items-center justify-center text-sm">H</span>
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
                        <p x-show="sidebarOpen" class="{{ $companyHeader }}">Super Admin</p>
                        <a href="{{ route('superadmin.dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('superadmin.dashboard') ? 'bg-purple-50 text-purple-700' : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Super Overview')" @mouseleave="hideTooltip()">
                            <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span x-show="sidebarOpen">Overview</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('settings.*') ? 'bg-purple-50 text-purple-700' : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Settings')" @mouseleave="hideTooltip()">
                            <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span x-show="sidebarOpen">Settings</span>
                        </a>
                    </div>
                
                {{-- SCENARIO 2: HR PERSONNEL (Split Menu) --}}
                @elseif(Auth::user()->role === 'hr')
                    
                    {{-- PERSONAL MENU --}}
                    <p x-show="sidebarOpen" class="{{ $personalHeader }}">Personal</p>
                    
                    <a href="{{ route('attendance.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('attendance.*') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}" @mouseenter="showTooltip($event, 'My Attendance')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-show="sidebarOpen">My Attendance</span>
                    </a>
                    
                    <a href="{{ route('leaves.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('leaves.*') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}" @mouseenter="showTooltip($event, 'My Leaves')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span x-show="sidebarOpen">My Leaves</span>
                    </a>

                    <a href="{{ route('payroll.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('payroll.*') ? 'bg-blue-50 text-blue-600' : $inactiveClasses }}" @mouseenter="showTooltip($event, 'My Payroll')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span x-show="sidebarOpen">My Payroll</span>
                    </a>

                    {{-- COMPANY MENU --}}
                    <p x-show="sidebarOpen" class="{{ $companyHeader }}">Company</p>

                    <a href="{{ route('dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('employees.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('employees.*') ? $activeClasses : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Employees')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span x-show="sidebarOpen">Employees</span>
                    </a>

                    <a href="{{ route('settings.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('settings.*') ? $activeClasses : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Settings')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span x-show="sidebarOpen">Settings</span>
                    </a>

                {{-- SCENARIO 3: REGULAR EMPLOYEE --}}
                @else
                    <p x-show="sidebarOpen" class="{{ $sectionHeader }}">Menu</p>
                    <a href="{{ route('dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }}" @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()">
                        <svg class="w-6 h-6 min-w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>
                    <!-- ... (Other links for employee) ... -->
                @endif
            </nav>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            <!-- Top Bar code remains same... -->
            <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 h-16 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">@yield('title')</h1>
                </div>
                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::user()->role === 'hr' ? 'HR Personnel' : 'Employee') }}</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm cursor-pointer hover:bg-green-200 transition">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>
                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-100 z-50" style="display: none;">
                        <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profile Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.duration.500ms class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Floating Tooltip -->
    <div x-show="tooltipVisible" x-cloak class="fixed z-50 px-2.5 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-md shadow-xl pointer-events-none transform -translate-y-1/2" :style="`top: ${tooltipTop}px; left: ${tooltipLeft}px;`">
        <span x-text="tooltipText"></span>
        <div class="absolute left-0 top-1/2 transform -translate-x-1 -translate-y-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
    </div>
</body>
</html>