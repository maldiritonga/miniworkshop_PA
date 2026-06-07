<x-auth-layout>
    <x-slot name="title">Masuk Ke Akun</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Masuk</h1>
        <p class="text-gray-500 font-medium">Selamat datang kembali di Mini Workshop</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100 text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="Masukkan email Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-sm font-bold text-gray-800">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-orange-600 hover:underline">Lupa Password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="Masukkan password Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
            <label for="remember_me" class="ml-3 text-sm font-medium text-gray-600">Ingat saya</label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Masuk
            </button>
        </div>

        <div class="flex items-center py-2">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="mx-4 text-gray-400 font-medium text-sm">atau</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <div>
            <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center py-4 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-2xl shadow-sm transition-all active:scale-[0.98]">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-6 h-6 mr-3">
                Masuk dengan Google
            </a>
        </div>

        <p class="text-center text-sm font-medium text-gray-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-green-600 font-bold hover:underline">Daftar</a>
        </p>
    </form>
</x-auth-layout>
