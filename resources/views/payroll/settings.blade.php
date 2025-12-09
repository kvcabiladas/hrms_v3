@extends('layouts.hrms')

@section('title', 'Payroll Settings')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Payroll Settings</h1>
            <p class="text-gray-600 text-sm mt-1">Manage allowances and deductions for payroll calculations</p>
        </div>

        <!-- Allowances Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100"
            x-data="{ showAddAllowance: false, editingAllowance: null }">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Allowances</h3>
                <button @click="showAddAllowance = true"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    + Add Allowance
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($allowances as $allowance)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $allowance->name }}</td>
                                <td class="px-6 py-4 font-bold text-green-600">₱{{ number_format($allowance->amount, 2) }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $allowance->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded {{ $allowance->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $allowance->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button @click="editingAllowance = {{ $allowance->toJson() }}"
                                            class="text-green-600 hover:text-green-700 font-medium text-sm">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('payroll.allowances.destroy', $allowance->id) }}"
                                            onsubmit="return confirm('Delete this allowance?')">
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
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No allowances configured</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add Allowance Modal -->
            <div x-show="showAddAllowance" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showAddAllowance = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Allowance</h3>
                    <form method="POST" action="{{ route('payroll.allowances.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name*</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount*</label>
                                <input type="number" step="0.01" name="amount" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showAddAllowance = false"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Add Allowance
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Allowance Modal -->
            <div x-show="editingAllowance" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="editingAllowance = null">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Allowance</h3>
                    <form method="POST" :action="`{{ route('payroll.allowances.update', '') }}/${editingAllowance?.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name*</label>
                                <input type="text" name="name" :value="editingAllowance?.name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount*</label>
                                <input type="number" step="0.01" name="amount" :value="editingAllowance?.amount" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" :value="editingAllowance?.description"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" :checked="editingAllowance?.is_active"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <label class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="editingAllowance = null"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Deductions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100"
            x-data="{ showAddDeduction: false, editingDeduction: null }">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Deductions</h3>
                <button @click="showAddDeduction = true"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    + Add Deduction
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($deductions as $deduction)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $deduction->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-700">
                                        {{ ucfirst(str_replace('_', ' ', $deduction->type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-red-600">
                                    @if($deduction->type === 'percentage')
                                        {{ $deduction->value }}%
                                    @else
                                        ₱{{ number_format($deduction->value, 2) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $deduction->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded {{ $deduction->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $deduction->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button @click="editingDeduction = {{ $deduction->toJson() }}"
                                            class="text-green-600 hover:text-green-700 font-medium text-sm">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('payroll.deductions.destroy', $deduction->id) }}"
                                            onsubmit="return confirm('Delete this deduction?')">
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
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No deductions configured</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add Deduction Modal -->
            <div x-show="showAddDeduction" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="showAddDeduction = false">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Deduction</h3>
                    <form method="POST" action="{{ route('payroll.deductions.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name*</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type*</label>
                                <select name="type" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="">Select Type</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed_amount">Fixed Amount</option>
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
                            <button type="button" @click="showAddDeduction = false"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Add Deduction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Deduction Modal -->
            <div x-show="editingDeduction" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click.self="editingDeduction = null">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Deduction</h3>
                    <form method="POST" :action="`{{ route('payroll.deductions.update', '') }}/${editingDeduction?.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name*</label>
                                <input type="text" name="name" :value="editingDeduction?.name" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type*</label>
                                <select name="type" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 bg-white">
                                    <option value="percentage" :selected="editingDeduction?.type === 'percentage'">
                                        Percentage</option>
                                    <option value="fixed_amount" :selected="editingDeduction?.type === 'fixed_amount'">Fixed
                                        Amount</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Value*</label>
                                <input type="number" step="0.01" name="value" :value="editingDeduction?.value" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" :value="editingDeduction?.description"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" :checked="editingDeduction?.is_active"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <label class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="editingDeduction = null"
                                class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection