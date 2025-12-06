@extends('layouts.hrms')

@section('title', 'Payroll Settings')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" 
         x-data="{ 
            allowances: {{ $allowances->map(function($a){ return ['id' => $a->id, 'name' => $a->name, 'value_type' => $a->value_type, 'value' => $a->value]; })->toJson() }},
            deductions: {{ $deductions->map(function($d){ return ['id' => $d->id, 'name' => $d->name, 'value_type' => $d->value_type, 'value' => $d->value]; })->toJson() }},
            showModal: false,
            modalType: 'allowance', // 'allowance' or 'deduction'
            modalMode: 'add',       // 'add' or 'edit'
            editIndex: null,
            form: { name: '', value_type: 'fixed', value: '' },

            openModal(type, mode, index = null) {
                this.modalType = type;
                this.modalMode = mode;
                this.editIndex = index;
                this.showModal = true;

                if (mode === 'edit' && index !== null) {
                    let item = (type === 'allowance') ? this.allowances[index] : this.deductions[index];
                    this.form = { ...item }; // Copy data
                } else {
                    this.form = { name: '', value_type: 'fixed', value: '' }; // Reset form
                }
            },

            saveItem() {
                if(!this.form.name || !this.form.value) return alert('Please fill all fields');

                let newItem = { ...this.form };

                if (this.modalMode === 'add') {
                    if (this.modalType === 'allowance') this.allowances.push(newItem);
                    else this.deductions.push(newItem);
                } else {
                    if (this.modalType === 'allowance') this.allowances[this.editIndex] = newItem;
                    else this.deductions[this.editIndex] = newItem;
                }
                this.showModal = false;
            },

            deleteItem(type, index) {
                if(!confirm('Are you sure?')) return;
                if (type === 'allowance') this.allowances.splice(index, 1);
                else this.deductions.splice(index, 1);
            }
         }">
        
        <form action="{{ route('payroll.update_settings') }}" method="POST">
            @csrf
            <!-- Hidden inputs to send JSON data to backend -->
            <input type="hidden" name="allowances" :value="JSON.stringify(allowances)">
            <input type="hidden" name="deductions" :value="JSON.stringify(deductions)">

            <!-- 1. General Configuration -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <h3 class="font-bold text-gray-800">General Configuration</h3>
                    <p class="text-sm text-gray-500">Manage global payroll rules.</p>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pay Schedule</label>
                        <select name="pay_schedule" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="monthly">Monthly</option>
                            <option value="biweekly">Bi-Weekly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <input type="text" name="currency" value="$" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Overtime Rate</label>
                        <div class="flex items-center">
                            <input type="number" step="0.1" value="1.5" class="w-full px-4 py-2 border border-gray-200 rounded-l-lg">
                            <span class="bg-gray-100 border border-l-0 border-gray-200 px-3 py-2 rounded-r-lg text-gray-500 text-sm">x</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Method</label>
                        <select class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="flat">Flat Percentage</option>
                            <option value="brackets">Tax Tables</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid for Allowances & Deductions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- ALLOWANCES CARD -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800">Allowances</h3>
                            <p class="text-xs text-gray-500">Earnings added to basic pay.</p>
                        </div>
                        <button type="button" @click="openModal('allowance', 'add')" class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-bold hover:bg-green-200 transition">+ Add</button>
                    </div>
                    
                    <div class="divide-y divide-gray-100 flex-1">
                        <template x-for="(item, index) in allowances" :key="index">
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="item.value_type === 'fixed' ? 'Fixed Amount' : 'Percentage'"></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-bold text-green-600" x-text="item.value_type === 'fixed' ? '+' + item.value : '+' + item.value + '%'"></span>
                                    
                                    <!-- Edit Button -->
                                    <button type="button" @click="openModal('allowance', 'edit', index)" class="text-gray-300 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" @click="deleteItem('allowance', index)" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="allowances.length === 0" class="p-4 text-center text-sm text-gray-400">No allowances added.</div>
                    </div>
                </div>

                <!-- DEDUCTIONS CARD -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800">Deductions</h3>
                            <p class="text-xs text-gray-500">Amounts subtracted from pay.</p>
                        </div>
                        <button type="button" @click="openModal('deduction', 'add')" class="text-xs bg-red-100 text-red-700 px-3 py-1.5 rounded-lg font-bold hover:bg-red-200 transition">+ Add</button>
                    </div>
                    
                    <div class="divide-y divide-gray-100 flex-1">
                        <template x-for="(item, index) in deductions" :key="index">
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="item.value_type === 'fixed' ? 'Fixed Amount' : 'Percentage'"></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-bold text-red-500" x-text="item.value_type === 'fixed' ? '-' + item.value : '-' + item.value + '%'"></span>
                                    
                                    <!-- Edit Button -->
                                    <button type="button" @click="openModal('deduction', 'edit', index)" class="text-gray-300 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" @click="deleteItem('deduction', index)" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="deductions.length === 0" class="p-4 text-center text-sm text-gray-400">No deductions added.</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-lg transition transform hover:scale-105">
                    Save All Settings
                </button>
            </div>
        </form>

        <!-- MODAL FOR ADD/EDIT -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full relative z-10">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-100">
                        <h3 class="text-lg font-medium text-gray-900">
                            <span x-text="modalMode === 'add' ? 'Add' : 'Edit'"></span> 
                            <span x-text="modalType === 'allowance' ? 'Allowance' : 'Deduction'"></span>
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" x-model="form.name" placeholder="e.g. Transport" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select x-model="form.value_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="fixed">Fixed Amount ($)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                            <input type="number" step="0.01" x-model="form.value" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 flex justify-end gap-3">
                        <button @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button @click="saveItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Item</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection