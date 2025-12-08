@extends('layouts.hrms')

@section('title', 'Designation Payroll Templates')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Designation Payroll Templates</h1>
                <p class="text-gray-600 text-sm mt-1">Configure payroll templates for specific job designations</p>
            </div>
            <button @click="showCreateModal = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                + Add New Template
            </button>
        </div>

        <!-- Templates List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            x-data="{ showCreateModal: false, showEditModal: false, editingTemplate: null }">
            @forelse($templates as $template)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $template->designation->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $template->designation->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="editingTemplate = {{ $template->toJson() }}; showEditModal = true"
                                class="text-green-600 hover:text-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('payroll-manager.templates.destroy', $template->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Base Allowance:</span>
                            <span class="font-bold text-green-600">₱{{ number_format($template->base_allowance, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Overtime Multiplier:</span>
                            <span class="font-bold text-blue-600">{{ $template->overtime_multiplier }}x</span>
                        </div>

                        @if($template->benefits && count($template->benefits) > 0)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Additional Benefits:</p>
                                <div class="space-y-1">
                                    @foreach($template->benefits as $benefit => $value)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">{{ ucfirst($benefit) }}:</span>
                                            <span class="text-xs font-medium text-gray-900">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($template->description)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-xs text-gray-600">{{ $template->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-xl shadow-sm border border-gray-100 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="text-gray-500">No designation templates configured yet</p>
                    <button @click="showCreateModal = true"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Create First Template
                    </button>
                </div>
            @endforelse

            <!-- Create Modal -->
            <div x-show="showCreateModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showCreateModal = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Template</h3>
                    <form method="POST" action="{{ route('payroll-manager.templates.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Designation*</label>
                                <select name="designation_id" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">
                                            {{ $designation->name }} ({{ $designation->department->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Base Allowance*</label>
                                <input type="number" step="0.01" name="base_allowance" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Overtime Multiplier*</label>
                                <input type="number" step="0.01" name="overtime_multiplier" value="1.5" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showCreateModal = false"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Create Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Modal -->
            <div x-show="showEditModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showEditModal = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Template</h3>
                    <form method="POST"
                        :action="`{{ route('payroll-manager.templates.update', '') }}/${editingTemplate?.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                <input type="text" :value="editingTemplate?.designation?.name" disabled
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Base Allowance*</label>
                                <input type="number" step="0.01" name="base_allowance"
                                    :value="editingTemplate?.base_allowance" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Overtime Multiplier*</label>
                                <input type="number" step="0.01" name="overtime_multiplier"
                                    :value="editingTemplate?.overtime_multiplier" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" :value="editingTemplate?.description"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Update Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection