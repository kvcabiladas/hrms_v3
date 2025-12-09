<!-- Create Modal -->
<div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
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
<div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="showEditModal = false">
    <div class="bg-white rounded-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Template</h3>
        <form method="POST" :action="`/payroll-manager/templates/${editingTemplate?.id}`">
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
                    <input type="number" step="0.01" name="base_allowance" :value="editingTemplate?.base_allowance"
                        required
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