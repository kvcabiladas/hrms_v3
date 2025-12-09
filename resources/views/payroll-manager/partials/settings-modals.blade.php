<!-- Create Modal -->
<div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
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
<div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="showEditModal = false">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Payroll Rule</h3>
        <form method="POST" :action="`/payroll-manager/rules/${editingRule?.id}`">
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