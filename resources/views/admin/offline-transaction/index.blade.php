<x-admin-layout title="Transaksi Offline">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Transaksi Offline</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Catat transaksi pelanggan yang datang langsung ke toko.</p>
        </div>
    </div>

    <x-flash-message />

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-100 rounded-2xl px-6 py-4">
            <ul class="list-disc list-inside text-sm text-red-600 font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Form Transaksi --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 sticky top-8">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Form Transaksi</h2>

                <form action="{{ route('admin.offline-transaction.store') }}" method="POST" class="space-y-5" id="offlineForm">
                    @csrf

                    {{-- Pilih Produk --}}
                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Produk</label>
                        <select name="id_produk" id="produkSelect" required
                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->id_produk }}"
                                    data-harga="{{ $item->harga }}"
                                    data-stok="{{ $item->stok }}"
                                    data-nama="{{ $item->nama_produk }}"
                                    {{ old('id_produk') == $item->id_produk ? 'selected' : '' }}>
                                    {{ $item->nama_produk }}
                                    @if($item->size) ({{ $item->size }}) @endif
                                    — Stok: {{ $item->stok }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Info Produk Terpilih --}}
                    <div id="produkInfo" class="hidden bg-blue-50 rounded-2xl px-4 py-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] font-black text-blue-400 uppercase tracking-widest">Harga Satuan</span>
                            <span id="hargaSatuan" class="font-black text-blue-700 text-base"></span>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-[11px] font-black text-blue-400 uppercase tracking-widest">Stok Tersedia</span>
                            <span id="stokInfo" class="font-black text-blue-700"></span>
                        </div>
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah</label>
                        <input type="number" name="qty" id="qtyInput" value="{{ old('qty', 1) }}" min="1" required
                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>

                    {{-- Total Harga --}}
                    <div id="totalBox" class="hidden bg-gray-900 rounded-2xl px-4 py-4 flex items-center justify-between">
                        <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Total</span>
                        <span id="totalHarga" class="font-black text-white text-lg"></span>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([
                                'cash'         => ['label' => 'Cash',          'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                                'transfer bank'=> ['label' => 'Transfer Bank', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                                'qris'         => ['label' => 'QRIS',          'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z'],
                                'debit'        => ['label' => 'Debit',         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                            ] as $value => $opt)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="metode_pembayaran" value="{{ $value }}"
                                    class="peer sr-only"
                                    {{ old('metode_pembayaran', 'cash') === $value ? 'checked' : '' }}>
                                <div class="flex flex-col items-center gap-2 p-4 bg-gray-50 border-2 border-transparent rounded-2xl text-center transition
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 peer-checked:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $opt['icon'] }}" />
                                    </svg>
                                    <span class="text-[11px] font-black text-gray-500 uppercase tracking-widest peer-checked:text-blue-700 transition">{{ $opt['label'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit"
                        class="w-full py-4 bg-blue-600 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-200 uppercase tracking-widest hover:bg-blue-700 transition mt-2">
                        Proses Transaksi
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Produk --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Produk Tersedia</h2>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">Klik produk untuk mengisi form transaksi secara otomatis.</p>
                </div>

                @if($produk->isEmpty())
                    <div class="px-8 py-20 text-center">
                        <div class="text-gray-200 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-400">Tidak ada produk aktif dengan stok tersedia.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 divide-y divide-gray-50">
                        @foreach ($produk as $item)
                        <button type="button"
                            onclick="pilihProduk('{{ $item->id_produk }}', '{{ $item->harga }}', '{{ $item->stok }}')"
                            class="flex items-center gap-4 px-6 py-5 hover:bg-blue-50/50 transition text-left group w-full">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden shrink-0">
                                @if($item->gambar)
                                    <img src="{{ asset('images/products/'.$item->gambar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-bold text-gray-900 truncate group-hover:text-blue-700 transition">{{ $item->nama_produk }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                    @if($item->size) · {{ $item->size }} @endif
                                </div>
                                <div class="text-[12px] font-black text-yellow-600 mt-1">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[11px] font-black {{ $item->stok <= 5 ? 'text-red-500' : 'text-green-600' }}">{{ $item->stok }}</div>
                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Stok</div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        const produkSelect = document.getElementById('produkSelect');
        const qtyInput     = document.getElementById('qtyInput');
        const produkInfo   = document.getElementById('produkInfo');
        const hargaSatuan  = document.getElementById('hargaSatuan');
        const stokInfo     = document.getElementById('stokInfo');
        const totalBox     = document.getElementById('totalBox');
        const totalHarga   = document.getElementById('totalHarga');

        function formatRupiah(angka) {
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }

        function updateInfo() {
            const selected = produkSelect.options[produkSelect.selectedIndex];
            const harga = selected.dataset.harga;
            const stok  = selected.dataset.stok;

            if (harga && stok) {
                hargaSatuan.textContent = formatRupiah(harga);
                stokInfo.textContent    = stok + ' pcs';
                qtyInput.max            = stok;
                produkInfo.classList.remove('hidden');
                updateTotal();
            } else {
                produkInfo.classList.add('hidden');
                totalBox.classList.add('hidden');
            }
        }

        function updateTotal() {
            const selected = produkSelect.options[produkSelect.selectedIndex];
            const harga = selected.dataset.harga;
            const qty   = parseInt(qtyInput.value) || 0;

            if (harga && qty > 0) {
                totalHarga.textContent = formatRupiah(harga * qty);
                totalBox.classList.remove('hidden');
            } else {
                totalBox.classList.add('hidden');
            }
        }

        function pilihProduk(id, harga, stok) {
            produkSelect.value = id;
            qtyInput.value     = 1;
            updateInfo();
            produkSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        produkSelect.addEventListener('change', updateInfo);
        qtyInput.addEventListener('input', updateTotal);

        // Inisialisasi jika ada old value
        if (produkSelect.value) updateInfo();
    </script>
</x-admin-layout>
