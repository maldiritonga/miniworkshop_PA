<x-auth-layout>
    <x-slot name="title">Daftar Akun</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Daftar</h1>
        <p class="text-gray-500 font-medium">Silahkan lakukan Registrasi</p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-10">
        <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full w-1/3 bg-black rounded-full"></div>
        </div>
        <p class="text-sm font-bold text-orange-800/60 mt-2">Informasi Pribadi</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="nama" class="block text-sm font-bold text-gray-800 mb-2">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" :value="old('nama')" required autofocus 
                placeholder="Masukkan nama lengkap Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required 
                placeholder="Masukkan email Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-bold text-gray-800 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                placeholder="Buat password"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-800 mb-2">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                placeholder="Konfirmasi password"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Daftar
            </button>
        </div>

        <p class="text-center text-sm font-medium text-gray-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-green-600 font-bold hover:underline">Masuk</a>
        </p>
    </form>
</x-auth-layout>
