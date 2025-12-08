@extends('layouts.hrms')

@section('title', 'Payroll Rules')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Payroll Rules</h1>
                <p class="text-gray-600 text-sm mt-1">Manage global payroll calculation rules</p>
            </div>
            <button @click="showCreateModal = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                + Add New Rule
            </button>
        </div>

        <!-- Rules List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100"
            x-data="{ showCreateModal: false, showEditModal: false, editingRule: null }">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Rule Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $rule->rule_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-700">
                                        {{ ucfirst(str_replace('_', ' ', $rule->rule_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    @if($rule->rule_type === 'percentage')
                                        {{ $rule->value }}%
                                    @elseif($rule->rule_type === 'fixed_amount')
                                        ₱{{ number_format($rule->value, 2) }}
                                    @else
                                        {{ $rule->value }}x
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $rule->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded {{ $rule->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button @click="editingRule = {{ $rule->toJson() }}; showEditModal = true"
                                            class="text-green-600 hover:text-green-700 font-medium text-sm">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('payroll-manager.rules.destroy', $rule->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this rule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No payroll rules configured yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Create Modal -->
            <div x-show="showCreateModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showCreateModal = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Payroll Rule</h3>
                    <form method="POST" action="{{ route('payroll-manager.rules.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rule Name*</label>
                                <input type="text" name="rule_name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rule Type*</label>
                                <select name="rule_type" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="">Select Type</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed_amount">Fixed Amount</option>
                                    <option value="multiplier">Multiplier</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Value*</label>
                                <input type="number" step="0.01" name="value" required
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
                                Create Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Modal -->
            <div x-show="showEditModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showEditModal = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Payroll Rule</h3>
                    <form method="POST" :action="`{{ route('payroll-manager.rules.update', '') }}/${editingRule?.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rule Name*</label>
                                <input type="text" name="rule_name" :value="editingRule?.rule_name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rule Type*</label>
                                <select name="rule_type" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="percentage" :selected="editingRule?.rule_type === 'percentage'">
                                        Percentage
                                    </option>
                                    <option value="fixed_amount" :selected="editingRule?.rule_type === 'fixed_amount'">Fixed
                                        Amount</option>
                                    <option value="multiplier" :selected="editingRule?.rule_type === 'multiplier'">
                                        Multiplier
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Value*</label>
                                <input type="number" step="0.01" name="value" :value="editingRule?.value" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" :value="editingRule?.description"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" :checked="editingRule?.is_active"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <label class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Update Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection