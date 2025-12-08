@extends('layouts.hrms')

@section('title', 'Leave Settings')

@section('content')
    <!-- BACK BUTTON -->
    <div class="mb-6">
        <a href="{{ route('leaves.index', ['tab' => 'settings']) }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Leave Management
        </a>
    </div>

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
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="selectedType = {{ json_encode($type) }}; showEditModal = true"
                                        class="text-green-600 hover:text-green-800 font-medium text-sm transition">
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
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">No leave types created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false">
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
                                <input type="checkbox" name="is_recallable" value="1" :checked="selectedType?.is_recallable"
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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false">
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
@endsection