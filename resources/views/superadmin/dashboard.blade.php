@extends('layouts.hrms')

@section('title', 'Super Admin Dashboard')

@section('content')
    <!-- Quick Actions -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
        <div class="flex gap-4">
            <a href="{{ route('superadmin.createHr') }}" class="flex items-center gap-3 px-6 py-4 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition shadow-md">
                <div class="p-2 bg-white/20 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div class="text-left">
                    <span class="block font-bold">Create HR Personnel</span>
                    <span class="text-xs text-purple-200">Add new HR admin</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">HR Personnel</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['hr_count'] }}</h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Total Employees</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['employee_count'] }}</h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Departments</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['dept_count'] }}</h3>
        </div>
    </div>

    <!-- HR Personnel List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">HR Personnel List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Employee ID</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hrPersonnel as $hr)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $hr->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-mono font-bold text-gray-700">
                                {{ $hr->employee->employee_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $hr->employee->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $hr->email }}</td>
                        
                        <!-- Action Column with Modal -->
                        <td class="px-6 py-4 text-center" x-data="{ showModal: false }">
                            <button @click="showModal = true" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">
                                View Credentials
                            </button>

                            <!-- Modal Backdrop -->
                            <div x-show="showModal" 
                                 class="fixed inset-0 z-50 overflow-y-auto" 
                                 style="display: none;"
                                 aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    
                                    <div x-show="showModal" 
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                         @click="showModal = false"
                                         aria-hidden="true"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="showModal" 
                                         class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 19.464a4.335 4.335 0 00-.77.77l-1.414-1.414a2.5 2.5 0 010-3.536l6.364-6.364L10 7H7a2 2 0 00-2 2v6h2v2h2v2h2"></path></svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Credentials for {{ $hr->name }}
                                                    </h3>
                                                    <div class="mt-4 space-y-3">
                                                        <!-- Employee ID -->
                                                        <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                                            <span class="text-xs text-gray-500 uppercase font-bold">Employee ID</span>
                                                            <div class="font-mono text-sm text-gray-800">{{ $hr->employee->employee_id ?? 'N/A' }}</div>
                                                        </div>

                                                        <!-- Username -->
                                                        <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                                            <span class="text-xs text-gray-500 uppercase font-bold">Username</span>
                                                            <div class="font-mono text-sm text-gray-800">{{ $hr->username }}</div>
                                                        </div>

                                                        <!-- Temp Password -->
                                                        <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                                            <span class="text-xs text-gray-500 uppercase font-bold">Temp Password</span>
                                                            <div class="flex justify-between items-center">
                                                                @if($hr->temp_password)
                                                                    <div class="font-mono text-sm text-red-600 font-bold">{{ $hr->temp_password }}</div>
                                                                    <button onclick="copyToClipboard('{{ $hr->temp_password }}')" class="text-blue-600 hover:text-blue-800 text-xs font-bold focus:outline-none">COPY</button>
                                                                @else
                                                                    <div class="font-mono text-sm text-green-600 italic">Changed by user</div>
                                                                @endif
                                                            </div>
                                                            @if($hr->temp_password)
                                                                <p class="text-xs text-gray-400 mt-1">Visible until user changes it.</p>
                                                            @endif
                                                        </div>

                                                        <!-- Access Code -->
                                                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                                                            <span class="text-xs text-yellow-600 uppercase font-bold">Access Code</span>
                                                            <div class="flex justify-between items-center">
                                                                <div class="font-mono text-lg font-bold text-yellow-800">{{ $hr->employee->access_code ?? 'N/A' }}</div>
                                                                <button onclick="copyToClipboard('{{ $hr->employee->access_code }}')" class="text-yellow-700 hover:text-yellow-900 text-xs font-bold focus:outline-none">COPY</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button @click="showModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No HR personnel found. Click "Create HR Personnel" above to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Copy to Clipboard Script -->
    <script>
        function copyToClipboard(text) {
            if (!text) return;
            // Use standard Clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Copied: ' + text);
                });
            } else {
                // Fallback for older browsers or non-secure context
                let textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Copied: ' + text);
            }
        }
    </script>
@endsection