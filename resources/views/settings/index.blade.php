@extends('layouts.hrms')

@section('title', 'Account Settings')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ activeTab: 'profile' }">
        
        <!-- Sidebar Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <nav class="flex flex-col">
                    <button @click="activeTab = 'profile'" 
                            :class="activeTab === 'profile' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        My Profile
                    </button>

                    <button @click="activeTab = 'security'" 
                            :class="activeTab === 'security' ? 'bg-green-50 text-green-700 border-l-4 border-green-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Security & Password
                    </button>

                    <!-- Recommended Extra Options -->
                    <button disabled class="text-left px-4 py-3 font-medium text-sm text-gray-400 cursor-not-allowed flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Notifications (Coming Soon)
                    </button>

                    @if(Auth::user()->role === 'super_admin')
                    <div class="border-t border-gray-100 my-1"></div>
                    <button @click="activeTab = 'company'" 
                            :class="activeTab === 'company' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="text-left px-4 py-3 font-medium text-sm transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Company Settings
                    </button>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            
            <!-- TAB 1: PROFILE -->
            <div x-show="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Personal Information</h2>
                <form action="{{ route('settings.update_profile') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" name="first_name" value="{{ $employee->first_name ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="{{ $employee->last_name ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ $employee->phone ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">{{ $employee->address ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: SECURITY (With Eye Icons) -->
            <div x-show="activeTab === 'security'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Change Password</h2>
                <p class="text-sm text-gray-500 mb-6">Ensure your account is using a long, random password to stay secure. Changing your password will automatically clear any temporary credentials.</p>
                
                <form action="{{ route('settings.update_password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 max-w-lg">
                        <!-- Current Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="current_password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium">Update Password</button>
                    </div>
                </form>
            </div>

            <!-- TAB 3: COMPANY (Super Admin Only) -->
            @if(Auth::user()->role === 'super_admin')
            <div x-show="activeTab === 'company'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Company Settings</h2>
                <form action="{{ route('settings.update_company') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label><input type="text" name="name" value="{{ $company->name ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label><input type="email" name="email" value="{{ $company->email ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Website</label><input type="url" name="website" value="{{ $company->website ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></div>
                    </div>
                    <div class="mt-6"><button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">Save Company Info</button></div>
                </form>
            </div>
            @endif

        </div>
    </div>
@endsection