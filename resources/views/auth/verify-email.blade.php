<x-auth-layout>
    <x-slot name="title">Verifikasi Email</x-slot>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Verifikasi Email</h1>
        <p class="text-gray-500 font-medium">Kami sudah mengirim link verifikasi ke email Anda. Silakan cek inbox/spam.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100 text-center">
            Link verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full py-4 bg-[#FF5722] hover:bg-[#E64A19] text-white font-extrabold rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-[0.98]">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-4 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-2xl shadow-sm transition-all active:scale-[0.98]">
                Keluar
            </button>
        </form>
    </div>
</x-auth-layout>
