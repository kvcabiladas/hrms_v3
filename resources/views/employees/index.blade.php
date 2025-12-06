@extends('layouts.hrms')

@section('title', 'Employee Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="relative w-full md:w-64">
            <input type="text" placeholder="Search employee..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Employee
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Added 'overflow-x-auto' here to enable scrolling -->
        <div class="overflow-x-auto">
            <!-- Added 'min-w-full' and 'whitespace-nowrap' to force width -->
            <table class="min-w-full text-left text-sm text-gray-600 whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Designation</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $employee->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $employee->employee_id }}</td>
                        <td class="px-6 py-4">{{ $employee->department->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $employee->designation->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $employee->status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('employees.show', $employee->id) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $employees->links() }}</div>
    </div>
@endsection