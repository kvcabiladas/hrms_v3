<div x-data="{ showCreateModal: false, showEditModal: false, editingTemplate: null }">
    <!-- Search and Sort -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4 flex-1">
            <div class="flex-1 max-w-md">
                <input type="text" x-model="searchQuery" placeholder="Search by designation name..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
            </div>
            <select x-model="sortBy"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
                <option value="">Sort By</option>
                <option value="designation_asc">Designation (A-Z)</option>
                <option value="designation_desc">Designation (Z-A)</option>
                <option value="allowance_asc">Base Allowance (Low to High)</option>
                <option value="allowance_desc">Base Allowance (High to Low)</option>
                <option value="multiplier_asc">Overtime Multiplier (Low to High)</option>
                <option value="multiplier_desc">Overtime Multiplier (High to Low)</option>
            </select>
        </div>
        <button @click="showCreateModal = true"
            class="ml-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="template in (() => {
            let filtered = {{ Js::from($templates) }}.filter(t => {
                const searchLower = searchQuery.toLowerCase();
                return t.designation.name.toLowerCase().includes(searchLower);
            });

            // Sort logic
            if (sortBy === 'designation_asc') {
                filtered.sort((a, b) => a.designation.name.localeCompare(b.designation.name));
            } else if (sortBy === 'designation_desc') {
                filtered.sort((a, b) => b.designation.name.localeCompare(a.designation.name));
            } else if (sortBy === 'allowance_asc') {
                filtered.sort((a, b) => a.base_allowance - b.base_allowance);
            } else if (sortBy === 'allowance_desc') {
                filtered.sort((a, b) => b.base_allowance - a.base_allowance);
            } else if (sortBy === 'multiplier_asc') {
                filtered.sort((a, b) => a.overtime_multiplier - b.overtime_multiplier);
            } else if (sortBy === 'multiplier_desc') {
                filtered.sort((a, b) => b.overtime_multiplier - a.overtime_multiplier);
            }

            return filtered;
        })()" :key="template.id">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900" x-text="template.designation.name"></h3>
                        <p class="text-xs text-gray-500" x-text="template.designation.department?.name || 'N/A'"></p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editingTemplate = template; showEditModal = true"
                            class="text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </button>
                        <form :action="`/payroll-manager/templates/${template.id}`" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this template?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
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
                        <span class="font-bold text-green-600" x-text="'₱' + template.base_allowance.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Overtime Multiplier:</span>
                        <span class="font-bold text-blue-600" x-text="template.overtime_multiplier + 'x'"></span>
                    </div>

                    <div x-show="template.description" class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-600" x-text="template.description"></p>
                    </div>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="(() => {
            let filtered = {{ Js::from($templates) }}.filter(t => {
                const searchLower = searchQuery.toLowerCase();
                return t.designation.name.toLowerCase().includes(searchLower);
            });
            return filtered.length === 0;
        })()" class="col-span-full bg-white p-12 rounded-xl shadow-sm border border-gray-100 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <p class="text-gray-500 mb-4">No designation templates found</p>
            <button @click="showCreateModal = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                Create First Template
            </button>
        </div>
    </div>

    @include('payroll-manager.partials.templates-modals')
</div>