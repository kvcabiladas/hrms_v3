@extends('layouts.hrms')

@section('title', 'Employee Management')

@section('content')
    <div x-data="{ 
        activeTab: 'employees', 
        searchQuery: '',
        showAddDept: false,
        showEditDept: false,
        showAddJob: false,
        showEditJob: false,
        selectedDept: null,
        selectedJob: null
    }">
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
            <!-- Search Bar -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex-1 max-w-md">
                    <input type="text" x-model="searchQuery" placeholder="Search employees by name, email, or ID..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
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
                        @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $employee->employee_id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $employee->first_name }}
                                    {{ $employee->last_name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $employee->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        {{ $employee->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs font-medium">
                                        {{ $employee->designation->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('employees.show', $employee->id) }}"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">View</a>
                                        <a href="{{ route('employees.edit', $employee->id) }}"
                                            class="text-green-600 hover:text-green-800 text-xs font-medium transition">Edit</a>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this employee?')"
                                                class="text-red-600 hover:text-red-800 text-xs font-medium transition">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">No employees found</p>
                                </td>
                            </tr>
                        @endforelse
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Employee Attendance Overview</h3>
                <p class="text-gray-600 text-sm">Attendance tracking and analytics will be displayed here.</p>
                <p class="text-gray-500 text-xs mt-2">Note: Copy content from hr/attendance.blade.php for full functionality
                </p>
            </div>
        </div>

        

        <!-- Tab 3: Departments -->
        <div x-show="activeTab === 'departments'" style="display: none;">
            <div class="mb-6">
                <button @click="showAddDept = true"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-2">
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
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($departments as $dept)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $dept->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold">
                                        {{ $dept->employees_count ?? 0 }} employees
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="selectedDept = {{ json_encode($dept) }}; showEditDept = true"
                                        class="text-green-600 hover:text-green-800 text-xs font-medium mr-3 transition">Edit</button>
                                    <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Delete this department? All associated jobs will be deleted and employees will be set to N/A.')"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm">No departments found</p>
                                </td>
                            </tr>
                        @endforelse
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
            <div class="mb-6">
                <button @click="showAddJob = true"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-2">
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
                        @forelse($designations as $job)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $job->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        {{ $job->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs font-bold">
                                        {{ $job->employees_count ?? 0 }} employees
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="selectedJob = {{ json_encode($job) }}; showEditJob = true"
                                        class="text-green-600 hover:text-green-800 text-xs font-medium mr-3 transition">Edit</button>
                                    <form action="{{ route('designations.destroy', $job->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Delete this job? Employees with this job will be set to N/A.')"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm">No jobs found</p>
                                </td>
                            </tr>
                        @endforelse
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select name="department_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
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