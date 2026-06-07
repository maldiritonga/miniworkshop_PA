<x-auth-layout>
    <x-slot name="title">Verifikasi Kode</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Verifikasi Kode</h1>
        <p class="text-gray-500 font-medium">Masukkan kode 6 digit yang kami kirim ke email Anda.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100 text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.code.check') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-gray-800 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username" readonly
                placeholder="Masukkan email Anda"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="code" class="block text-sm font-bold text-gray-800 mb-2">Kode Verifikasi</label>
            <input id="code" type="text" inputmode="numeric" name="code" value="{{ old('code') }}" required maxlength="6"
                placeholder="Contoh: 123456"
                class="w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none tracking-[0.35em] text-center font-extrabold text-lg" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Verifikasi
            </button>
        </div>
    </form>

    <div class="text-center text-sm font-medium text-gray-500 space-y-3 mt-6">
        <div>Kode terbaru akan menggantikan kode sebelumnya.</div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">
            <button type="submit" class="text-orange-600 font-bold hover:underline">
                Kirim ulang kode
            </button>
        </form>
    </div>
</x-auth-layout>
