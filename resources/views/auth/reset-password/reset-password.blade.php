<x-auth-layout>
    <x-slot name="title">Reset Password</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Reset Password</h1>
        <p class="text-gray-500 font-medium">Silakan buat password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                placeholder="Masukkan email Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Reset Password
            </button>
        </div>

        <p class="text-center text-sm font-medium text-gray-500">
            Kembali ke <a href="{{ route('login') }}" class="text-orange-600 font-bold hover:underline">halaman login</a>
        </p>
    </form>
</x-auth-layout>
