@extends('layouts.hrms')

@section('title', 'Employee Management')

@section('content')
    <div x-data="{ 
                                                        activeTab: new URLSearchParams(window.location.search).get('tab') || 'employees', 
                                                        searchQuery: '',
                                                        sortDept: '',
                                                        sortJob: '',
                                                        showAddDept: false,
                                                        showEditDept: false,
                                                        showAddJob: false,
                                                        showEditJob: false,
                                                        selectedDept: null,
                                                        selectedJob: null
                                                    }" x-init="
                                                        // Update URL when tab changes
                                                        $watch('activeTab', value => {
                                                            const url = new URL(window.location);
                                                            url.searchParams.set('tab', value);
                                                            window.history.pushState({}, '', url);
                                                            // Clear search query when switching tabs
                                                            searchQuery = '';
                                                        })
                                                    ">
        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <button @click="activeTab = 'employees'"
                    :class="activeTab === 'employees' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Employees List
                </button>
                <button @click="activeTab = 'attendance'"
                    :class="activeTab === 'attendance' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Employee Attendance
                </button>
                <button @click="activeTab = 'departments'"
                    :class="activeTab === 'departments' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Departments
                </button>
                <button @click="activeTab = 'jobs'"
                    :class="activeTab === 'jobs' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Jobs
                </button>
            </nav>
        </div>

        <!-- Tab 1: Employees List -->
        <div x-show="activeTab === 'employees'" style="display: none;">
            <!-- Search Bar and Sort -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex gap-4 flex-1">
                    <div class="flex-1 max-w-md">
                        <input type="text" x-model="searchQuery" placeholder="Search employees by name, email, or ID..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>
                    <select
                        onchange="window.location.href='{{ route('employees.index') }}?tab=employees&sort=' + this.value"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                        <option value="">Sort By</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>ID (Ascending)</option>
                        <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>ID (Descending)</option>
                        <option value="department_asc" {{ request('sort') == 'department_asc' ? 'selected' : '' }}>Department
                            (A-Z)</option>
                        <option value="department_desc" {{ request('sort') == 'department_desc' ? 'selected' : '' }}>
                            Department (Z-A)</option>
                    </select>
                </div>
                <a href="{{ route('employees.create') }}"
                    class="ml-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Employee
                </a>
            </div>

            <!-- Employees Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Job</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="employee in {{ json_encode($employees->items()) }}.filter(e => 
                                                                    e.first_name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                                                    e.last_name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                                                    e.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                                                    e.employee_id.toLowerCase().includes(searchQuery.toLowerCase())
                                                                )" :key="employee.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-900" x-text="employee.employee_id"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="employee.first_name + ' ' + employee.last_name">
                                </td>
                                <td class="px-6 py-4 text-gray-900" x-text="employee.email"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="employee.department?.name || 'N/A'"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="employee.designation?.name || 'N/A'"></td>
                                <td class="px-6 py-4 text-center">
                                    <a :href="`/employees/${employee.id}`"
                                        class="text-green-600 hover:text-green-800 font-medium text-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="mt-6">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>

        <!-- Tab 2: Employee Attendance -->
        <div x-show="activeTab === 'attendance'" style="display: none;">
            <!-- Date Range Filter -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap gap-4 items-end">
                    <input type="hidden" name="tab" value="attendance">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date"
                            value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-800">All Attendance Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Employee</th>
                                <th class="px-6 py-4">Department</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Clock In</th>
                                <th class="px-6 py-4">Clock Out</th>
                                <th class="px-6 py-4">Duration</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
                                $endDate = request('end_date', now()->format('Y-m-d'));
                                $attendanceRecords = \App\Models\Attendance::with(['employee.department'])
                                    ->whereBetween('date', [$startDate, $endDate])
                                    ->orderBy('date', 'desc')
                                    ->paginate(20);
                            @endphp
                            @forelse($attendanceRecords as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $record->employee->first_name }} {{ $record->employee->last_name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $record->employee->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">{{ $record->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        @if($record->total_hours)
                                            {{ $record->total_hours }} hrs
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ ucfirst($record->status) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-sm">No attendance records found for this period.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($attendanceRecords->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $attendanceRecords->links() }}
                    </div>
                @endif
            </div>
        </div>



        <!-- Tab 3: Departments -->
        <div x-show="activeTab === 'departments'" style="display: none;">
            <!-- Search and Add Button -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex gap-4 flex-1">
                    <div class="flex-1 max-w-md">
                        <input type="text" placeholder="Search departments..." x-model="searchQuery"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>
                    <select x-model="sortDept"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                        <option value="">Sort By</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="employees_desc">Most Employees</option>
                        <option value="employees_asc">Least Employees</option>
                    </select>
                </div>
                <button @click="showAddDept = true"
                    class="ml-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Department
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Department Name</th>
                            <th class="px-6 py-4">Employees</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="dept in (() => {
                                                    let filtered = {{ Js::from($departments) }}.filter(d => 
                                                        d.name.toLowerCase().includes(searchQuery.toLowerCase())
                                                    );

                                                    // Sort logic
                                                    if (sortDept === 'name_asc') {
                                                        filtered.sort((a, b) => a.name.localeCompare(b.name));
                                                    } else if (sortDept === 'name_desc') {
                                                        filtered.sort((a, b) => b.name.localeCompare(a.name));
                                                    } else if (sortDept === 'employees_desc') {
                                                        filtered.sort((a, b) => b.employees_count - a.employees_count);
                                                    } else if (sortDept === 'employees_asc') {
                                                        filtered.sort((a, b) => a.employees_count - b.employees_count);
                                                    }

                                                    return filtered;
                                                })()" :key="dept.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900" x-text="dept.name"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="(dept.employees_count || 0) + ' employees'">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="selectedDept = dept; showEditDept = true"
                                        class="text-green-600 hover:text-green-800 text-sm font-medium mr-3 transition">Edit</button>
                                    <form :action="`/departments/${dept.id}`" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Delete this department? All associated jobs will be deleted and employees will be set to N/A.')"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Add Department Modal -->
            <div x-show="showAddDept" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddDept = false">
                    </div>

                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Department</h3>

                        <form action="{{ route('departments.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                    Create Department
                                </button>
                                <button type="button" @click="showAddDept = false"
                                    class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Department Modal -->
            <div x-show="showEditDept" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditDept = false">
                    </div>

                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Department</h3>

                        <form :action="`/departments/${selectedDept?.id}`" method="POST" x-show="selectedDept">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                                <input type="text" name="name" :value="selectedDept?.name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                    Update Department
                                </button>
                                <button type="button" @click="showEditDept = false"
                                    class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Jobs -->
        <div x-show="activeTab === 'jobs'" style="display: none;">
            <!-- Search and Add Button -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex gap-4 flex-1">
                    <div class="flex-1 max-w-md">
                        <input type="text" placeholder="Search jobs..." x-model="searchQuery"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>
                    <select x-model="sortJob"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                        <option value="">Sort By</option>
                        <option value="name_asc">Job Title (A-Z)</option>
                        <option value="name_desc">Job Title (Z-A)</option>
                        <option value="department_asc">Department (A-Z)</option>
                        <option value="employees_desc">Most Employees</option>
                        <option value="employees_asc">Least Employees</option>
                    </select>
                </div>
                <button @click="showAddJob = true"
                    class="ml-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Job
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Job Title</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Employees</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="job in (() => {
                                                let filtered = {{ Js::from($designations) }}.filter(j => 
                                                    j.name.toLowerCase().includes(searchQuery.toLowerCase())
                                                );

                                                // Sort logic
                                                if (sortJob === 'name_asc') {
                                                    filtered.sort((a, b) => a.name.localeCompare(b.name));
                                                } else if (sortJob === 'name_desc') {
                                                    filtered.sort((a, b) => b.name.localeCompare(a.name));
                                                } else if (sortJob === 'department_asc') {
                                                    filtered.sort((a, b) => (a.department?.name || '').localeCompare(b.department?.name || ''));
                                                } else if (sortJob === 'employees_desc') {
                                                    filtered.sort((a, b) => (b.employees_count || 0) - (a.employees_count || 0));
                                                } else if (sortJob === 'employees_asc') {
                                                    filtered.sort((a, b) => (a.employees_count || 0) - (b.employees_count || 0));
                                                }

                                                return filtered;
                                            })()" :key="job.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-900" x-text="job.name"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="job.department?.name || 'N/A'"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="(job.employees_count || 0) + ' employees'"></td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="selectedJob = job; showEditJob = true"
                                        class="text-green-600 hover:text-green-800 text-sm font-medium mr-3 transition">Edit</button>
                                    <form :action="`/designations/${job.id}`" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Delete this job? Employees with this job will be set to N/A.')"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Add Job Modal -->
            <div x-show="showAddJob" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddJob = false">
                    </div>

                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Job</h3>

                        <form action="{{ route('designations.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department*</label>
                                <select name="department_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Job Title*</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                    Create Job
                                </button>
                                <button type="button" @click="showAddJob = false"
                                    class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Job Modal -->
            <div x-show="showEditJob" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditJob = false">
                    </div>

                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Job</h3>

                        <form :action="`/designations/${selectedJob?.id}`" method="POST" x-show="selectedJob">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                                <input type="text" name="name" :value="selectedJob?.name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select name="department_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            :selected="selectedJob?.department_id == {{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                    Update Job
                                </button>
                                <button type="button" @click="showEditJob = false"
                                    class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection