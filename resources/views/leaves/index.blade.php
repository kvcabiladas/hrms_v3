@extends('layouts.hrms')

@section('title', 'Leave Management')

@section('content')
    <div x-data="{ 
                                                activeTab: new URLSearchParams(window.location.search).get('tab') || 'history', 
                                                searchQuery: '',
                                                sortLeave: '',
                                                showViewModal: false,
                                                showRejectModal: false,
                                                selectedLeave: null
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
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Leave History
                </button>
                <button @click="activeTab = 'recall'"
                    :class="activeTab === 'recall' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    Leave Recall
                </button>
                @if(Auth::user()->role !== 'employee')
                    <button @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition whitespace-nowrap">
                        Leave Settings
                    </button>
                @endif
            </nav>
        </div>

        <!-- Tab 1: Leave History -->
        <div x-show="activeTab === 'history'" style="display: none;">
            <!-- Search and Sort -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex gap-4 flex-1">
                    <div class="flex-1 max-w-md">
                        <input type="text" x-model="searchQuery" placeholder="Search by employee name..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                    </div>
                    <select x-model="sortLeave"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                        <option value="">Sort By</option>
                        <option value="date_desc">Date (Newest)</option>
                        <option value="date_asc">Date (Oldest)</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="status_pending">Status (Pending First)</option>
                        <option value="status_approved">Status (Approved First)</option>
                    </select>
                </div>
            </div>

            <!-- Leave History Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Duration</th>
                            <th class="px-6 py-4">Start Date</th>
                            <th class="px-6 py-4">End Date</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="leave in (() => {
                                                            let filtered = {{ Js::from($leaves) }}.filter(l => 
                                                                (l.employee.first_name + ' ' + l.employee.last_name).toLowerCase().includes(searchQuery.toLowerCase())
                                                            );

                                                            // Sort logic
                                                            if (sortLeave === 'date_desc') {
                                                                filtered.sort((a, b) => new Date(b.start_date) - new Date(a.start_date));
                                                            } else if (sortLeave === 'date_asc') {
                                                                filtered.sort((a, b) => new Date(a.start_date) - new Date(b.start_date));
                                                            } else if (sortLeave === 'name_asc') {
                                                                filtered.sort((a, b) => (a.employee.first_name + ' ' + a.employee.last_name).localeCompare(b.employee.first_name + ' ' + b.employee.last_name));
                                                            } else if (sortLeave === 'name_desc') {
                                                                filtered.sort((a, b) => (b.employee.first_name + ' ' + b.employee.last_name).localeCompare(a.employee.first_name + ' ' + a.employee.last_name));
                                                            } else if (sortLeave === 'status_pending') {
                                                                filtered.sort((a, b) => a.status === 'pending' ? -1 : 1);
                                                            } else if (sortLeave === 'status_approved') {
                                                                filtered.sort((a, b) => a.status === 'approved' ? -1 : 1);
                                                            }

                                                            return filtered;
                                                        })()" :key="leave.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-900"
                                    x-text="leave.employee.first_name + ' ' + leave.employee.last_name"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="leave.days + ' Days'"></td>
                                <td class="px-6 py-4 text-gray-900"
                                    x-text="new Date(leave.start_date).toLocaleDateString()"></td>
                                <td class="px-6 py-4 text-gray-900" x-text="new Date(leave.end_date).toLocaleDateString()">
                                </td>
                                <td class="px-6 py-4 text-gray-900" x-text="leave.type?.name || 'General'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded" :class="{
                                                            'bg-yellow-100 text-yellow-700': leave.status === 'pending',
                                                            'bg-green-100 text-green-700': leave.status === 'approved',
                                                            'bg-red-100 text-red-700': leave.status === 'rejected'
                                                        }"
                                        x-text="leave.status.charAt(0).toUpperCase() + leave.status.slice(1)"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="selectedLeave = leave; showViewModal = true"
                                        class="text-green-600 hover:text-green-800 font-medium text-sm">
                                        View
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Leave Recall -->
        <div x-show="activeTab === 'recall'" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Leave Recall</h3>
                <p class="text-gray-600 text-sm">Recall functionality will be displayed here.</p>
                <p class="text-gray-500 text-xs mt-2">Feature coming soon</p>
            </div>
        </div>

        <!-- Tab 3: Leave Settings (HR Only) -->
        <div x-show="activeTab === 'settings'" style="display: none;">
            @if(Auth::user()->role !== 'employee')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6"
                    x-data="{ showEditModal: false, showDeleteModal: false, selectedType: null }">

                    <!-- Left: Create New Type -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                        <h3 class="font-bold text-gray-800 mb-4">Create Leave Type</h3>
                        <form action="{{ route('leaves.store_type') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Plan Name*</label>
                                <input type="text" name="name" placeholder="e.g. Maternity"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)*</label>
                                <input type="number" name="days_allowed" placeholder="e.g. 60"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_recallable" value="1"
                                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <span class="text-sm font-medium text-gray-700">Recallable Leave</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Can HR recall employees from this leave type?</p>
                            </div>
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow-sm">
                                Create Leave Type
                            </button>
                        </form>
                    </div>

                    <!-- Right: List of Types -->
                    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="font-bold text-gray-800">Manage Leave Types</h3>
                        </div>
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Leave Plan</th>
                                    <th class="px-6 py-4">Duration</th>
                                    <th class="px-6 py-4">Recallable</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($types as $type)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-gray-900">{{ $type->name }}</td>
                                        <td class="px-6 py-4 text-gray-900">{{ $type->days_allowed }} Days</td>
                                        <td class="px-6 py-4 text-gray-900">
                                            @if($type->is_recallable)
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-700">Yes</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-gray-100 text-gray-700">No</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="selectedType = {{ json_encode($type) }}; showEditModal = true"
                                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm transition">
                                                    Edit
                                                </button>
                                                <button @click="selectedType = {{ json_encode($type) }}; showDeleteModal = true"
                                                    class="text-red-600 hover:text-red-800 font-medium text-sm transition">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">No leave types created yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Edit Modal -->
                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                @click="showEditModal = false">
                            </div>

                            <div class="relative bg-white rounded-lg max-w-md w-full p-6" x-show="selectedType">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Leave Type</h3>

                                <form :action="'/leaves/types/' + selectedType?.id" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Leave Plan Name*</label>
                                        <input type="text" name="name" :value="selectedType?.name" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)*</label>
                                        <input type="number" name="days_allowed" :value="selectedType?.days_allowed" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                                    </div>
                                    <div class="mb-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_recallable" value="1"
                                                :checked="selectedType?.is_recallable"
                                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                            <span class="text-sm font-medium text-gray-700">Recallable Leave</span>
                                        </label>
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="submit"
                                            class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                            Update
                                        </button>
                                        <button type="button" @click="showEditModal = false"
                                            class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                @click="showDeleteModal = false">
                            </div>

                            <div class="relative bg-white rounded-lg max-w-md w-full p-6" x-show="selectedType">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Delete Leave Type</h3>
                                <p class="text-gray-600 mb-6">Are you sure you want to delete <strong
                                        x-text="selectedType?.name"></strong>? This action cannot be undone.</p>

                                <form :action="'/leaves/types/' + selectedType?.id" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <div class="flex gap-3">
                                        <button type="submit"
                                            class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 font-medium transition">
                                            Delete
                                        </button>
                                        <button type="button" @click="showDeleteModal = false"
                                            class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <p class="text-gray-500 font-medium">Access Restricted</p>
                    <p class="text-gray-400 text-sm mt-1">Only HR personnel can access leave settings.</p>
                </div>
            @endif
        </div>

        <!-- View Leave Modal -->
        <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showViewModal = false">
                </div>

                <div class="relative bg-white rounded-lg max-w-lg w-full p-6" x-show="selectedLeave">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Leave Request Details</h3>

                    <div class="space-y-3 mb-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Employee Name</label>
                            <p class="text-sm font-medium text-gray-900"
                                x-text="selectedLeave?.employee?.first_name + ' ' + selectedLeave?.employee?.last_name"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Duration</label>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedLeave?.days + ' Days'"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                <p class="text-sm font-medium text-gray-900"
                                    x-text="selectedLeave?.type?.name || 'General'"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Start Date</label>
                                <p class="text-sm font-medium text-gray-900"
                                    x-text="selectedLeave ? new Date(selectedLeave.start_date).toLocaleDateString() : ''">
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">End Date</label>
                                <p class="text-sm font-medium text-gray-900"
                                    x-text="selectedLeave ? new Date(selectedLeave.end_date).toLocaleDateString() : ''"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Relief Officer</label>
                            <p class="text-sm font-medium text-gray-900"
                                x-text="selectedLeave?.relief_officer ? (selectedLeave.relief_officer.first_name + ' ' + selectedLeave.relief_officer.last_name) : 'None'">
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Reason</label>
                            <p class="text-sm text-gray-700" x-text="selectedLeave?.reason || 'No reason provided'"></p>
                        </div>
                    </div>

                    @if(Auth::user()->role !== 'employee')
                        <div class="flex gap-3" x-show="selectedLeave?.status === 'pending'">
                            <form :action="'/leaves/' + selectedLeave?.id" method="POST" class="flex-1">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium transition">
                                    Approve
                                </button>
                            </form>
                            <button @click="showViewModal = false; showRejectModal = true"
                                class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 font-medium transition">
                                Reject
                            </button>
                        </div>
                    @endif

                    <button @click="showViewModal = false"
                        class="mt-3 w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRejectModal = false">
                </div>

                <div class="relative bg-white rounded-lg max-w-md w-full p-6" x-show="selectedLeave">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Leave Request</h3>

                    <form :action="'/leaves/' + selectedLeave?.id" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason*</label>
                            <textarea name="rejection_reason" rows="3" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none"
                                placeholder="Please provide a reason for rejection..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 font-medium transition">
                                Confirm Rejection
                            </button>
                            <button type="button" @click="showRejectModal = false; showViewModal = true"
                                class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection