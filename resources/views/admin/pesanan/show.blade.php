<x-admin-layout title="Detail Pesanan #{{ $pesanan->id_pesanan }}">
    <div class="mb-10">
        <a href="{{ route('admin.pesanan.index') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detail Pesanan #{{ $pesanan->id_pesanan }}</h1>
    </div>

    <x-flash-message />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Order Items & Customer Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Items -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Produk Dipesan</h3>
                <div class="space-y-6">
                    @foreach($pesanan->detail as $item)
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 shrink-0">
                            @if($item->produk->gambar)
                                <img src="{{ asset('images/products/' . $item->produk->gambar) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=100&q=80" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="text-[13px] font-bold text-gray-900">{{ $item->produk->nama_produk }}</div>
                            <div class="text-[11px] text-gray-400">Variasi: {{ $item->produk->size }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[13px] font-bold text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-gray-400">Qty: {{ $item->qty }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-8 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-400">Total Pembayaran</span>
                    <span class="text-xl font-black text-blue-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Customer & Shipping -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Informasi Pelanggan</h3>
                        <div class="text-[13px] font-bold text-gray-900">{{ $pesanan->user->nama ?? 'Walking Customer (Offline)' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $pesanan->user->email ?? '-' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $pesanan->no_hp }}</div>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Alamat Pengiriman</h3>
                        <p class="text-[12px] text-gray-600 leading-relaxed">{{ $pesanan->alamat_pengiriman }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Status & Action -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Kelola Status</h3>
                
                <form action="{{ route('admin.pesanan.update', $pesanan->id_pesanan) }}" method="POST" x-data="{ status: '{{ $pesanan->status_pesanan }}' }">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Status Pesanan</label>
                        <select name="status_pesanan" x-model="status" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-[13px] font-bold focus:ring-blue-600 transition"
                            {{ (in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan', 'diretur']) && $pesanan->tipe_pesanan === 'online') ? 'disabled' : '' }}>
                            
                            @if($pesanan->tipe_pesanan === 'offline')
                                <option value="selesai" {{ $pesanan->status_pesanan == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $pesanan->status_pesanan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            @else
                                <option value="menunggu_pembayaran" {{ $pesanan->status_pesanan == 'menunggu_pembayaran' ? 'selected' : '' }} {{ ($pesanan->pembayaran && $pesanan->pembayaran->status_pembayaran === 'berhasil') ? 'disabled' : '' }}>Menunggu Pembayaran</option>
                                <option value="dikemas" {{ $pesanan->status_pesanan == 'dikemas' ? 'selected' : '' }}>Dikemas</option>
                                <option value="dikirim" {{ $pesanan->status_pesanan == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                <option value="diantar" {{ $pesanan->status_pesanan == 'diantar' ? 'selected' : '' }}>Sudah Diantar Kurir</option>
                                <option value="selesai" {{ $pesanan->status_pesanan == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $pesanan->status_pesanan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                @if($pesanan->status_pesanan === 'diretur')
                                    <option value="diretur" selected>Diretur</option>
                                @endif
                            @endif
                        </select>
                    </div>

                    <div x-show="status === 'dikirim' || status === 'diantar'" x-collapse>
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kurir (cth: jne, sicepat)</label>
                            @php($selectedKurir = strtolower($pesanan->kurir ?? ''))
                            <select name="kurir" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-[13px] font-bold focus:ring-blue-600 transition">
                                <option value="">Pilih Kurir</option>
                                @if($selectedKurir && !in_array($selectedKurir, ['jne','sicepat','jnt']))
                                    <option value="{{ $selectedKurir }}" selected>{{ strtoupper($selectedKurir) }}</option>
                                @endif
                                <option value="jne" {{ $selectedKurir === 'jne' ? 'selected' : '' }}>JNE</option>
                                <option value="sicepat" {{ $selectedKurir === 'sicepat' ? 'selected' : '' }}>SiCepat</option>
                                <option value="jnt" {{ $selectedKurir === 'jnt' ? 'selected' : '' }}>J&amp;T</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">No. Resi</label>
                            <input type="text" name="resi" value="{{ $pesanan->resi }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-[13px] font-bold focus:ring-blue-600 transition" placeholder="Masukkan nomor resi">
                        </div>
                    </div>

                    @if(! (in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan', 'diretur']) && $pesanan->tipe_pesanan === 'online') )
                        <button type="submit" class="w-full py-4 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-800 transition shadow-sm mt-4">
                            Update Status
                        </button>
                    @endif
                </form>

                @if($pesanan->status_pesanan === 'dikirim')
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <form action="{{ route('admin.pesanan.update', $pesanan->id_pesanan) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_pesanan" value="diantar">
                        <input type="hidden" name="kurir" value="{{ $pesanan->kurir }}">
                        <input type="hidden" name="resi" value="{{ $pesanan->resi }}">
                        <button type="submit" onclick="return confirm('Konfirmasi bahwa kurir sudah mengantar paket ini?')"
                            style="background-color: #3b82f6 !important; color: white !important; width: 100%;"
                            class="w-full py-4 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Tandai Sudah Diantar Kurir
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Metode Pembayaran</h3>
                <div class="p-4 bg-blue-50 rounded-2xl">
                    <div class="text-[11px] font-black text-blue-600 uppercase tracking-widest mb-1">{{ str_replace('_', ' ', $pesanan->pembayaran->metode_pembayaran) }}</div>
                    @if($pesanan->pembayaran->bank_tujuan)
                    <div class="text-[10px] text-blue-500 font-bold">Bank: {{ $pesanan->pembayaran->bank_tujuan }}</div>
                    @endif
                    <div class="text-[10px] text-blue-400 font-medium italic mt-1">Status: {{ str_replace('_', ' ', $pesanan->pembayaran->status_pembayaran) }}</div>
                </div>

                {{-- Bukti Pembayaran --}}
                @if($pesanan->pembayaran->bukti_pembayaran)
                <div class="mt-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-3">Bukti Pembayaran</h3>
                    <a href="{{ asset('images/bukti/' . $pesanan->pembayaran->bukti_pembayaran) }}" target="_blank" class="inline-block">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 hover:border-gray-300 transition">
                            <img src="{{ asset('images/bukti/' . $pesanan->pembayaran->bukti_pembayaran) }}"
                                alt="Bukti Pembayaran"
                                class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </div>
                    </a>
                    <p class="text-[10px] text-gray-400 mt-2">Klik foto untuk melihat ukuran penuh.</p>
                </div>
                @endif

                {{-- 2. Konfirmasi Pembayaran --}}
                @if($pesanan->pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                <div class="mt-8 pt-8 border-t border-gray-100" x-data="{ mode: 'idle' }">
                    <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4">Konfirmasi Pembayaran</h3>
                    
                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 rounded-2xl border border-red-100">
                        <ul class="list-disc list-inside text-[11px] text-red-600 font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div style="display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="flex: 1;">
                            <form action="{{ route('admin.pesanan.konfirmasi-pembayaran', $pesanan->id_pesanan) }}" method="POST">
                                @csrf
                                <input type="hidden" name="aksi" value="konfirmasi">
                                <button type="submit" onclick="return confirm('Konfirmasi pembayaran ini?')"
                                    style="background-color: #10b981 !important; color: white !important; width: 100%;"
                                    class="py-4 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Konfirmasi
                                </button>
                            </form>
                        </div>
                        <div style="flex: 1;">
                            <button type="button" @click="mode = (mode === 'reject' ? 'idle' : 'reject')"
                                style="width: 100%;"
                                class="py-4 bg-white border-2 border-red-100 text-red-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-50 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak Bukti
                            </button>
                        </div>
                    </div>

                    <div x-show="mode === 'reject'" x-cloak x-collapse class="mt-4 p-5 bg-red-50 rounded-[2rem] border border-red-100 shadow-inner">
                        <form action="{{ route('admin.pesanan.konfirmasi-pembayaran', $pesanan->id_pesanan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="aksi" value="tolak">
                            <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-2 ml-1">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" rows="3" required
                                class="w-full bg-white border-red-100 rounded-xl p-4 text-[13px] font-bold text-gray-900 focus:ring-red-400 focus:border-red-400 transition placeholder:text-red-200"
                                placeholder="Tulis alasan mengapa bukti ini ditolak..."></textarea>
                            <button type="submit" onclick="return confirm('Yakin ingin menolak bukti ini?')"
                                class="mt-4 w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-700 transition shadow-md shadow-red-200">
                                Kirim Penolakan & Hapus Bukti
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- 3. Berhasil Badge + Invoice + Label + Pickup --}}
                @if($pesanan->pembayaran->status_pembayaran === 'berhasil')
                <div class="mt-4 p-3 bg-green-50 rounded-2xl text-center">
                    <span class="text-xs font-black text-green-600 uppercase tracking-widest">✓ Pembayaran Dikonfirmasi</span>
                    @if($pesanan->pembayaran->tanggal_pembayaran)
                    <div class="text-[10px] text-green-400 mt-1">{{ $pesanan->pembayaran->tanggal_pembayaran->format('d M Y, H:i') }}</div>
                    @endif
                </div>
                <a href="{{ route('admin.pesanan.invoice', $pesanan->id_pesanan) }}" target="_blank"
                    class="mt-3 flex items-center justify-center gap-2 w-full py-3 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-800 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Invoice
                </a>
                <a href="{{ route('admin.pesanan.label-pengiriman', $pesanan->id_pesanan) }}" target="_blank"
                    class="mt-2 flex items-center justify-center gap-2 w-full py-3 bg-blue-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-blue-700 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Lihat Label Pengiriman
                </a>
                @if(!$pesanan->biteship_order_id)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <form action="{{ route('admin.pesanan.request-pickup', $pesanan->id_pesanan) }}" method="POST">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Yakin ingin memanggil kurir (Request Pickup) untuk pesanan ini?')"
                            style="background-color: #4f46e5 !important; color: white !important; width: 100%;"
                            class="flex items-center justify-center gap-2 py-3 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            Request Pickup (Biteship)
                        </button>
                    </form>
                </div>
                @else
                <div class="mt-3 p-3 bg-indigo-50 border border-indigo-100 rounded-2xl text-center">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">✓ Pickup Telah Direquest</span>
                    <div class="text-[10px] font-bold text-indigo-400 mt-1">ID: {{ $pesanan->biteship_order_id }}</div>
                </div>
                @endif
                @endif

                {{-- 4. Menunggu Bukti --}}
                @if($pesanan->pembayaran->status_pembayaran !== 'berhasil' && !$pesanan->pembayaran->bukti_pembayaran)
                <div class="mt-6 p-4 bg-yellow-50 rounded-2xl text-center">
                    <span class="text-xs font-black text-yellow-600 uppercase tracking-widest">Menunggu Bukti Pembayaran</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
