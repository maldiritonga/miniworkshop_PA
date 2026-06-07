<x-auth-layout>
    <x-slot name="title">Konfirmasi Password</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Konfirmasi Password</h1>
        <p class="text-gray-500 font-medium">Untuk keamanan, silakan masukkan password Anda untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <div>
            <label for="password" class="block text-sm font-bold text-gray-800 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="Masukkan password Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Konfirmasi
            </button>
        </div>
    </form>
</x-auth-layout>
