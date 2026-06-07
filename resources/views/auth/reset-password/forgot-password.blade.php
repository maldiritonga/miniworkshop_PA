<x-auth-layout>
    <x-slot name="title">Lupa Password</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Lupa Password</h1>
        <p class="text-gray-500 font-medium">Masukkan email akun Anda untuk menerima kode verifikasi reset password.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100 text-center">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <div class="mb-4 font-bold text-sm text-red-600 bg-red-50 p-4 rounded-xl border border-red-100 text-center">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                placeholder="Masukkan email Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Kirim Kode Verifikasi
            </button>
        </div>

        <p class="text-center text-sm font-medium text-gray-500">
            Kembali ke <a href="{{ route('login') }}" class="text-orange-600 font-bold hover:underline">halaman login</a>
        </p>
    </form>
</x-auth-layout>
