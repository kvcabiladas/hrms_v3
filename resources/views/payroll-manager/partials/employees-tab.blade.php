<!-- Search and Sort -->
<div class="mb-6 flex justify-between items-center">
    <div class="flex gap-4 flex-1">
        <div class="flex-1 max-w-md">
            <input type="text" x-model="searchQuery" placeholder="Search by name or employee ID..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
        </div>
        <select x-model="sortBy"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:outline-none bg-white">
            <option value="">Sort By</option>
            <option value="name_asc">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="id_asc">Employee ID (Ascending)</option>
            <option value="id_desc">Employee ID (Descending)</option>
            <option value="rate_asc">Hourly Rate (Low to High)</option>
            <option value="rate_desc">Hourly Rate (High to Low)</option>
            <option value="department_asc">Department (A-Z)</option>
            <option value="department_desc">Department (Z-A)</option>
        </select>
    </div>
    <a href="{{ route('payroll-manager.payroll-management', ['tab' => 'employees', 'action' => 'run-payroll']) }}"
        class="ml-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        Run Payroll
    </a>
</div>

<!-- Employee List Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Department</th>
                    <th class="px-6 py-4">Designation</th>
                    <th class="px-6 py-4">Hourly Rate</th>
                    <th class="px-6 py-4">Payrolls</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="employee in (() => {
                    let filtered = {{ Js::from($employees) }}.filter(emp => {
                        const searchLower = searchQuery.toLowerCase();
                        const fullName = (emp.first_name + ' ' + emp.last_name).toLowerCase();
                        const empId = emp.employee_id.toLowerCase();
                        return fullName.includes(searchLower) || empId.includes(searchLower);
                    });

                    // Sort logic
                    if (sortBy === 'name_asc') {
                        filtered.sort((a, b) => (a.first_name + ' ' + a.last_name).localeCompare(b.first_name + ' ' + b.last_name));
                    } else if (sortBy === 'name_desc') {
                        filtered.sort((a, b) => (b.first_name + ' ' + b.last_name).localeCompare(a.first_name + ' ' + a.last_name));
                    } else if (sortBy === 'id_asc') {
                        filtered.sort((a, b) => a.employee_id.localeCompare(b.employee_id));
                    } else if (sortBy === 'id_desc') {
                        filtered.sort((a, b) => b.employee_id.localeCompare(a.employee_id));
                    } else if (sortBy === 'rate_asc') {
                        filtered.sort((a, b) => (a.hourly_rate || 0) - (b.hourly_rate || 0));
                    } else if (sortBy === 'rate_desc') {
                        filtered.sort((a, b) => (b.hourly_rate || 0) - (a.hourly_rate || 0));
                    } else if (sortBy === 'department_asc') {
                        filtered.sort((a, b) => (a.department?.name || '').localeCompare(b.department?.name || ''));
                    } else if (sortBy === 'department_desc') {
                        filtered.sort((a, b) => (b.department?.name || '').localeCompare(a.department?.name || ''));
                    }

                    return filtered;
                })()" :key="employee.id">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold"
                                    x-text="employee.first_name.charAt(0) + employee.last_name.charAt(0)">
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"
                                        x-text="employee.first_name + ' ' + employee.last_name"></p>
                                    <p class="text-xs text-gray-500" x-text="employee.email"></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-900" x-text="employee.employee_id"></td>
                        <td class="px-6 py-4 text-gray-900" x-text="employee.department?.name || 'N/A'"></td>
                        <td class="px-6 py-4 text-gray-900" x-text="employee.designation?.name || 'N/A'"></td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-green-600"
                                x-text="'₱' + (employee.hourly_rate || 0).toFixed(2) + '/hr'"></span>
                        </td>
                        <td class="px-6 py-4 text-gray-900" x-text="employee.payrolls_count || 0"></td>
                        <td class="px-6 py-4 text-center">
                            <a :href="`/payroll-manager/employees/${employee.id}/payroll`"
                                class="text-green-600 hover:text-green-800 font-medium text-sm">
                                View Payroll
                            </a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div x-show="(() => {
        let filtered = {{ Js::from($employees) }}.filter(emp => {
            const searchLower = searchQuery.toLowerCase();
            const fullName = (emp.first_name + ' ' + emp.last_name).toLowerCase();
            const empId = emp.employee_id.toLowerCase();
            return fullName.includes(searchLower) || empId.includes(searchLower);
        });
        return filtered.length === 0;
    })()" class="px-6 py-12 text-center text-gray-500">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
        </svg>
        <p class="text-sm">No employees found</p>
    </div>
</div>