@extends('layouts.hrms')

@section('title', 'Settings')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ activeTab: 'profile' }">

        <!-- Sidebar Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <nav class="flex flex-col">
                    <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                        class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </button>

                    <button @click="activeTab = 'financial'"
                        :class="activeTab === 'financial' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                        class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        Financial Details
                    </button>

                    <button @click="activeTab = 'emergency'"
                        :class="activeTab === 'emergency' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                        class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        Emergency Contacts
                    </button>

                    <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                        class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        Security & Password
                    </button>

                    @if(Auth::user()->role === 'super_admin')
                        <div class="border-t border-gray-100 my-1"></div>
                        <button @click="activeTab = 'company'"
                            :class="activeTab === 'company' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Company Settings
                        </button>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">

            <!-- TAB 1: PERSONAL INFORMATION (View/Edit Mode) -->
            <div x-show="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8"
                x-data="{ editMode: false }">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Personal Information</h2>
                    <button @click="editMode = !editMode" type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span x-text="editMode ? 'Cancel' : 'Edit'"></span>
                    </button>
                </div>

                <!-- Profile Picture Section -->
                <div class="mb-8 flex items-center gap-6">
                    <div class="relative">
                        @if($employee && $employee->profile_picture)
                            <img src="{{ asset($employee->profile_picture) }}" alt="Profile"
                                class="w-24 h-24 rounded-full object-cover border-4 border-gray-100">
                        @else
                            <div
                                class="w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-3xl font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div x-show="editMode" style="display: none;">
                        <form action="{{ route('settings.update_profile_picture') }}" method="POST"
                            enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="profile_picture" accept="image/*" required
                                class="text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                Upload
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF (MAX. 2MB)</p>
                    </div>
                </div>

                <!-- View Mode -->
                <div x-show="!editMode">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">First Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->first_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Last Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->last_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                            <p class="text-gray-900 font-medium">{{ $employee->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                            <p class="text-gray-900 font-medium">{{ $employee->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form x-show="editMode" style="display: none;" action="{{ route('settings.update_profile') }}"
                    method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name*</label>
                            <input type="text" name="first_name" value="{{ $employee->first_name ?? '' }}" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name*</label>
                            <input type="text" name="last_name" value="{{ $employee->last_name ?? '' }}" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address*</label>
                            <input type="email" name="email" value="{{ $user->email }}" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number*</label>
                            <input type="text" name="phone" value="{{ $employee->phone ?? '' }}" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                                placeholder="Numbers only">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address*</label>
                            <textarea name="address" rows="3" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">{{ $employee->address ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Save
                            Changes</button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: FINANCIAL DETAILS (View/Edit Mode) -->
            <div x-show="activeTab === 'financial'" style="display: none;"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-8" x-data="{ editMode: false }">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Financial Details</h2>
                    <button @click="editMode = !editMode" type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span x-text="editMode ? 'Cancel' : 'Edit'"></span>
                    </button>
                </div>

                <!-- View Mode -->
                <div x-show="!editMode">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bank Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->bank_name ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Account Number</label>
                            <p class="text-gray-900 font-medium">{{ $employee->account_number ?? 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form x-show="editMode" style="display: none;" action="{{ route('settings.update_financial_details') }}"
                    method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ $employee->bank_name ?? '' }}"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                            <input type="text" name="account_number" value="{{ $employee->account_number ?? '' }}"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Save
                            Changes</button>
                    </div>
                </form>
            </div>

            <!-- TAB 3: EMERGENCY CONTACTS (View/Edit Mode) -->
            <div x-show="activeTab === 'emergency'" style="display: none;"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-8" x-data="{ 
                        editMode: false,
                        contacts: {{ json_encode($emergencyContacts ?? [['name' => '', 'relationship' => '', 'phone' => '', 'address' => '']]) }}
                    }">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Emergency Contacts</h2>
                    <button @click="editMode = !editMode" type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span x-text="editMode ? 'Cancel' : 'Edit'"></span>
                    </button>
                </div>

                <!-- View Mode -->
                <div x-show="!editMode">
                    <template x-for="(contact, index) in contacts" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Contact Name</label>
                                    <p class="text-gray-900 font-medium" x-text="contact.name || 'N/A'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Relationship</label>
                                    <p class="text-gray-900 font-medium" x-text="contact.relationship || 'N/A'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                                    <p class="text-gray-900 font-medium" x-text="contact.phone || 'N/A'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                                    <p class="text-gray-900 font-medium" x-text="contact.address || 'N/A'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Edit Mode -->
                <form x-show="editMode" style="display: none;" action="{{ route('settings.update_emergency_contacts') }}"
                    method="POST">
                    @csrf
                    <div class="space-y-6">
                        <template x-for="(contact, index) in contacts" :key="index">
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <button type="button" @click="contacts.splice(index, 1)" x-show="contacts.length > 1"
                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name*</label>
                                        <input type="text" :name="'contacts[' + index + '][name]'" x-model="contact.name"
                                            required
                                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship*</label>
                                        <input type="text" :name="'contacts[' + index + '][relationship]'"
                                            x-model="contact.relationship" required
                                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                                            placeholder="e.g., Spouse, Parent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number*</label>
                                        <input type="text" :name="'contacts[' + index + '][phone]'" x-model="contact.phone"
                                            required oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500"
                                            placeholder="Numbers only">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address*</label>
                                        <input type="text" :name="'contacts[' + index + '][address]'"
                                            x-model="contact.address" required
                                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button type="button" @click="contacts.push({ name: '', relationship: '', phone: '', address: '' })"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Add Another Contact
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Save
                            Emergency Contacts</button>
                    </div>
                </form>
            </div>

            <!-- TAB 4: SECURITY (Password Change) -->
            <div x-show="activeTab === 'security'" style="display: none;"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Change Password</h2>
                <p class="text-sm text-gray-500 mb-6">Ensure your account is using a long, random password to stay secure.
                    Changing your password will automatically clear any temporary credentials.</p>

                <form action="{{ route('settings.update_password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 max-w-lg">
                        <!-- Current Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="current_password" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium">Update
                            Password</button>
                    </div>
                </form>
            </div>

            <!-- TAB 5: COMPANY (Super Admin Only) -->
            @if(Auth::user()->role === 'super_admin')
                <div x-show="activeTab === 'company'" style="display: none;"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Company Settings</h2>
                    <form action="{{ route('settings.update_company') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" name="name" value="{{ $company->name ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                <input type="email" name="email" value="{{ $company->email ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                <input type="url" name="website" value="{{ $company->website ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit"
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">Save
                                Company Info</button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
@endsection