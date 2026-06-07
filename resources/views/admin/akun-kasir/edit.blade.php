<x-admin-layout>
    <x-slot name="title">Edit Akun Kasir</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.akun-kasir.index') }}" class="hover:text-gray-900">Akun Kasir</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Edit</span>
        </nav>

        <div class="bg-white rounded-[32px] p-8 md:p-12 shadow-sm border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Akun Kasir</h1>

            <form action="{{ route('admin.akun-kasir.update', $kasir->id_user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" required value="{{ old('nama', $kasir->nama) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Nama staf kasir">
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Email</label>
                        <input type="email" name="email" id="email" required value="{{ old('email', $kasir->email) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="email@example.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" id="no_hp" required value="{{ old('no_hp', $kasir->no_hp) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="08123456789">
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>
                    <div></div>
                </div>

                <div class="bg-yellow-50 p-6 rounded-2xl space-y-4">
                    <h3 class="text-sm font-bold text-yellow-800">Ubah Password (Opsional)</h3>
                    <p class="text-xs text-yellow-700">Kosongkan jika tidak ingin mengubah password.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Password Baru</label>
                            <input type="password" name="password" id="password"
                                   class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                                   placeholder="Min 8 karakter">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4">
                    <a href="{{ route('admin.akun-kasir.index') }}" class="px-8 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-10 py-4 bg-yellow-400 text-gray-900 font-black rounded-2xl hover:bg-yellow-500 transition shadow-lg shadow-yellow-100 uppercase tracking-widest text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
