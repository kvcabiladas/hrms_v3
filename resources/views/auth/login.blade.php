<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex flex-col items-center mb-6">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">HRMS Portal</h2>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Login Field (Email or Username) -->
        <div class="mb-4">
            <label for="login" class="block font-medium text-sm text-gray-700 mb-1">Email or Username</label>
            <input id="login"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                type="text" name="login" :value="old('login')" required autofocus />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4" x-data="{ show: false }">
            <label for="password" class="block font-medium text-sm text-gray-700 mb-1">Password</label>
            <div class="relative">
                <input id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pr-10"
                    :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-green-600 focus:outline-none">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                        </path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 select-none">Remember me</span>
            </label>
        </div>

        <button
            class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow-sm">Log
            in</button>
    </form>
</x-guest-layout>