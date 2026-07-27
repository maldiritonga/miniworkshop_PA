<x-admin-layout title="Produk Masuk">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Produk Masuk</h1>
        <p class="text-[13px] text-gray-500 font-medium mt-1">Tambah stok produk yang baru diterima.</p>
    </div>

    <x-flash-message />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Form --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Form Produk Masuk</h3>

            @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                <ul class="list-disc list-inside text-sm text-red-600 font-medium space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.stok.masuk.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="id_produk" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $p)
                        <option value="{{ $p->id_produk }}" {{ old('id_produk') == $p->id_produk ? 'selected' : '' }}>
                            {{ $p->nama_produk }}{{ $p->size ? ' ('.$p->size.')' : '' }} — Stok: {{ $p->stok }} pcs
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="Masukkan jumlah produk yang diterima">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan <span class="text-gray-300">(opsional)</span></label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="Contoh: Restock dari supplier, Kiriman gudang, dll.">
                </div>

                <button type="submit"
                    class="w-full py-4 bg-green-500 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-green-600 transition shadow-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Stok
                </button>
            </form>
        </div>

        {{-- Info stok saat ini --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Stok Produk Saat Ini</h3>
            <div class="space-y-3 max-h-[480px] overflow-y-auto pr-1">
                @foreach($produk as $p)
                <div class="flex items-center justify-between p-3 rounded-2xl border border-gray-100 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                            @if($p->gambar)
                                <img src="{{ asset('images/products/'.$p->gambar) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <div class="text-[13px] font-bold text-gray-900">{{ $p->nama_produk }}</div>
                            @if($p->size)<div class="text-[10px] text-gray-400">{{ $p->size }}</div>@endif
                        </div>
                    </div>
                    <span class="text-[13px] font-black {{ $p->stok > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $p->stok }} pcs
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
