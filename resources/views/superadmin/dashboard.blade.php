@extends('layouts.hrms')

@section('title', 'Super Admin Dashboard')

@section('content')
    <!-- Top Actions -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-700">HR Personnel Overview</h2>
        
        <a href="{{ route('superadmin.createHr') }}" class="flex items-center gap-2 px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium shadow-md transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add New HR
        </a>
    </div>

    <!-- HR Personnel List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">List of HR Admins</h3>
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
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-900">
                            {{ $hr->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-900">
                            {{ $hr->employee->employee_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-gray-900">{{ $hr->employee->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-900">{{ $hr->email }}</td>
                        
                        <!-- Action Column with Modal -->
                        <td class="px-6 py-4 text-center" x-data="{ showModal: false }">
                            <button @click="showModal = true" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">
                                View Credentials
                            </button>

                            <!-- Modal Backdrop -->
                            <div x-show="showModal" 
                                 class="fixed inset-0 z-50 overflow-y-auto" 
                                 style="display: none;"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                 
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 19.464a4.335 4.335 0 00-.77.77l-1.414-1.414a2.5 2.5 0 010-3.536l6.364-6.364L10 7H7a2 2 0 00-2 2v6h2v2h2v2h2"></path></svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
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
                                                                    <button onclick="copyToClipboard('{{ $hr->temp_password }}')" class="text-gray-600 hover:text-green-600 transition focus:outline-none" title="Copy password">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                        </svg>
                                                                    </button>
                                                                @else
                                                                    <div class="font-mono text-sm text-green-600 italic">Changed by user</div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Access Code -->
                                                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                                                            <span class="text-xs text-yellow-600 uppercase font-bold">Access Code</span>
                                                            <div class="flex justify-between items-center">
                                                                <div class="font-mono text-lg font-bold text-yellow-800">{{ $hr->employee->access_code ?? 'N/A' }}</div>
                                                                <button onclick="copyToClipboard('{{ $hr->employee->access_code }}')" class="text-gray-600 hover:text-green-600 transition focus:outline-none" title="Copy access code">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </button>
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p>No HR personnel found.</p>
                            <a href="{{ route('superadmin.createHr') }}" class="text-purple-600 hover:underline mt-2 inline-block">Create the first one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Copy Script -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied: ' + text);
            });
        }
    </script>
@endsection