<div x-data="{ showCreateModal: false, showEditModal: false, editingRule: null }">
    <!-- Search and Sort -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4 flex-1">
            <div class="flex-1 max-w-md">
                <input type="text" x-model="searchQuery" placeholder="Search by rule name..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
            </div>
            <select x-model="sortBy"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                <option value="">Sort By</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="type_asc">Type (A-Z)</option>
                <option value="type_desc">Type (Z-A)</option>
                <option value="value_asc">Value (Low to High)</option>
                <option value="value_desc">Value (High to Low)</option>
                <option value="status_active">Status (Active First)</option>
                <option value="status_inactive">Status (Inactive First)</option>
            </select>
        </div>
        <button @click="showCreateModal = true"
            class="ml-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Rule
        </button>
    </div>

    <!-- Payroll Rules Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Rule Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Value</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="rule in (() => {
                        let filtered = {{ Js::from($rules) }}.filter(r => {
                            const searchLower = searchQuery.toLowerCase();
                            return r.rule_name.toLowerCase().includes(searchLower);
                        });

                        // Sort logic
                        if (sortBy === 'name_asc') {
                            filtered.sort((a, b) => a.rule_name.localeCompare(b.rule_name));
                        } else if (sortBy === 'name_desc') {
                            filtered.sort((a, b) => b.rule_name.localeCompare(a.rule_name));
                        } else if (sortBy === 'type_asc') {
                            filtered.sort((a, b) => a.rule_type.localeCompare(b.rule_type));
                        } else if (sortBy === 'type_desc') {
                            filtered.sort((a, b) => b.rule_type.localeCompare(a.rule_type));
                        } else if (sortBy === 'value_asc') {
                            filtered.sort((a, b) => a.value - b.value);
                        } else if (sortBy === 'value_desc') {
                            filtered.sort((a, b) => b.value - a.value);
                        } else if (sortBy === 'status_active') {
                            filtered.sort((a, b) => b.is_active - a.is_active);
                        } else if (sortBy === 'status_inactive') {
                            filtered.sort((a, b) => a.is_active - b.is_active);
                        }

                        return filtered;
                    })()" :key="rule.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="rule.rule_name"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-700"
                                    x-text="rule.rule_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                <span x-show="rule.rule_type === 'percentage'" x-text="rule.value + '%'"></span>
                                <span x-show="rule.rule_type === 'fixed_amount'"
                                    x-text="'₱' + rule.value.toFixed(2)"></span>
                                <span x-show="rule.rule_type === 'multiplier'" x-text="rule.value + 'x'"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900" x-text="rule.description || 'N/A'"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded"
                                    :class="rule.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                    x-text="rule.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="editingRule = rule; showEditModal = true"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        Edit
                                    </button>
                                    <form :action="`/payroll-manager/rules/${rule.id}`" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this rule?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="(() => {
            let filtered = {{ Js::from($rules) }}.filter(r => {
                const searchLower = searchQuery.toLowerCase();
                return r.rule_name.toLowerCase().includes(searchLower);
            });
            return filtered.length === 0;
        })()" class="px-6 py-12 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                </path>
            </svg>
            <p class="text-sm">No payroll rules found</p>
        </div>
    </div>

    @include('payroll-manager.partials.settings-modals')
</div>