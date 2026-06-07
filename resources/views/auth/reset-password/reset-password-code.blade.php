<x-auth-layout>
    <x-slot name="title">Reset Password</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Reset Password</h1>
        <p class="text-gray-500 font-medium">Buat password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.code.update') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <div class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-700 font-bold">
                {{ $email }}
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-800 mb-2">Password Baru</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Masukkan password baru"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-800 mb-2">Konfirmasi Password Baru</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ulangi password baru"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Simpan Password Baru
            </button>
        </div>
    </form>
</x-auth-layout>

