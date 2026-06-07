<x-admin-layout title="Detail Retur #{{ $retur->id_retur }}">
    <div class="mb-8">
        <a href="{{ route('admin.retur.index') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Retur
        </a>
        <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detail Retur #{{ $retur->id_retur }}</h1>
    </div>

    <x-flash-message />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kiri: Info Retur --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Produk yang Diretur --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Produk yang Diretur</h3>
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 shrink-0">
                        @if($retur->produk->gambar)
                            <img src="{{ asset('images/products/'.$retur->produk->gambar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-[15px] font-bold text-gray-900">{{ $retur->produk->nama_produk }}</div>
                        @if($retur->produk->size)
                        <div class="text-[11px] text-gray-400 mt-0.5">Ukuran: {{ $retur->produk->size }}</div>
                        @endif
                        <div class="text-[11px] text-gray-400 mt-0.5">ID Produk: #{{ $retur->id_produk }}</div>
                    </div>
                </div>
            </div>

            {{-- Alasan Retur --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Alasan Retur</h3>
                <p class="text-[14px] text-gray-700 leading-relaxed bg-gray-50 rounded-2xl p-4">
                    {{ $retur->alasan_retur ?? 'Tidak ada alasan yang diberikan.' }}
                </p>
            </div>

            {{-- Foto Bukti --}}
            @if($retur->foto_bukti && count($retur->foto_bukti) > 0)
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-5">
                    Foto Bukti
                    <span class="ml-2 text-[11px] font-bold text-gray-400 normal-case">({{ count($retur->foto_bukti) }} foto)</span>
                </h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($retur->foto_bukti as $foto)
                    <a href="{{ asset('images/retur/' . $foto) }}" target="_blank" class="block shrink-0">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 hover:border-gray-300 transition">
                            <img src="{{ asset('images/retur/' . $foto) }}"
                                alt="Foto Bukti Retur"
                                class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </div>
                    </a>
                    @endforeach
                </div>
                <p class="text-[10px] text-gray-400 mt-3">Klik foto untuk melihat ukuran penuh.</p>
            </div>
            @endif

            {{-- Info Rekening Pelanggan (tampil jika sudah diisi) --}}
            @if($retur->no_rekening)
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-5">Rekening Pelanggan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400 font-medium">Bank</span>
                        <span class="font-bold text-gray-900">{{ $retur->nama_bank }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400 font-medium">No. Rekening</span>
                        <span class="font-bold text-gray-900 tracking-wider">{{ $retur->no_rekening }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400 font-medium">Atas Nama</span>
                        <span class="font-bold text-gray-900">{{ $retur->nama_pemilik_rekening }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bukti Transfer (tampil jika sudah ada) --}}
            @if($retur->bukti_transfer)
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-5">Bukti Transfer</h3>
                <a href="{{ asset('images/bukti_transfer/' . $retur->bukti_transfer) }}" target="_blank">
                    <img src="{{ asset('images/bukti_transfer/' . $retur->bukti_transfer) }}"
                         alt="Bukti Transfer"
                         class="w-full max-w-xs rounded-2xl border border-gray-100 hover:opacity-90 transition cursor-pointer">
                </a>
                <p class="text-[10px] text-gray-400 mt-3">Klik gambar untuk melihat ukuran penuh.</p>
            </div>
            @endif

            {{-- Info Pesanan --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-5">Informasi Pesanan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ID Pesanan</div>
                        <div class="font-bold text-gray-900">#{{ $retur->id_pesanan }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Pesanan</div>
                        <div class="font-bold text-gray-900">{{ $retur->pesanan->tanggal_pesanan }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pelanggan</div>
                        <div class="font-bold text-gray-900">{{ $retur->pesanan->user->nama ?? 'Walking Customer' }}</div>
                        <div class="text-[11px] text-gray-400">{{ $retur->pesanan->user->email ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</div>
                        <div class="font-bold text-gray-900">Rp {{ number_format($retur->pesanan->total_harga, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Metode Pembayaran</div>
                        <div class="font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $retur->pesanan->pembayaran->metode_pembayaran ?? '-') }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Retur</div>
                        <div class="font-bold text-gray-900">{{ $retur->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.pesanan.show', $retur->id_pesanan) }}"
                        class="text-[11px] font-black text-blue-600 uppercase tracking-widest hover:underline">
                        Lihat Detail Pesanan →
                    </a>
                </div>
            </div>
        </div>

        {{-- Kanan: Status & Aksi --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Status Saat Ini --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Status Retur</h3>
                @php
                    $badge = \App\Models\Retur::statusBadge($retur->status_retur);
                @endphp
                <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>

                {{-- Progress Steps --}}
                <div class="mt-6 space-y-3">
                    @php
                        $steps = [
                            'diajukan'          => ['label' => 'Retur Diajukan',        'icon' => '📋'],
                            'menunggu_rekening' => ['label' => 'Menunggu Rekening',     'icon' => '🏦'],
                            'menunggu_barang'   => ['label' => 'Menunggu Barang',        'icon' => '📦'],
                            'menunggu_transfer' => ['label' => 'Menunggu Transfer',      'icon' => '💳'],
                            'uang_ditransfer'   => ['label' => 'Uang Ditransfer',        'icon' => '✅'],
                            'selesai'           => ['label' => 'Retur Selesai',          'icon' => '🎉'],
                        ];
                        $stepOrder = array_keys($steps);
                        $currentIdx = array_search($retur->status_retur, $stepOrder);
                    @endphp
                    @if($retur->status_retur !== 'ditolak')
                    @foreach($steps as $key => $step)
                    @php $idx = array_search($key, $stepOrder); @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px]
                            {{ $idx < $currentIdx ? 'bg-green-100 text-green-600' : ($idx === $currentIdx ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-400') }}">
                            {{ $idx <= $currentIdx ? $step['icon'] : '○' }}
                        </div>
                        <span class="text-[12px] font-{{ $idx === $currentIdx ? 'black' : 'medium' }}
                            {{ $idx < $currentIdx ? 'text-green-600' : ($idx === $currentIdx ? 'text-indigo-700' : 'text-gray-400') }}">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    @endforeach
                    @else
                    <div class="text-[12px] font-bold text-red-500">Retur ini telah ditolak.</div>
                    @if($retur->alasan_penolakan)
                    <div class="text-[11px] text-gray-500 mt-1">{{ $retur->alasan_penolakan }}</div>
                    @endif
                    @endif
                </div>
            </div>

            {{-- ===== AKSI BERDASARKAN STATUS ===== --}}

            {{-- STATUS: diajukan → Terima / Tolak --}}
            @if($retur->status_retur === 'diajukan')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100" x-data="{ tolakOpen: false }">
                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-5">Tindakan Admin</h3>
                
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="flex: 1;">
                        <form action="{{ route('admin.retur.terima', $retur->id_retur) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Terima retur ini? Pelanggan akan diminta mengisi nomor rekening.')"
                                style="background-color: #10b981 !important; color: white !important; width: 100%;"
                                class="py-4 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Terima
                            </button>
                        </form>
                    </div>
                    <div style="flex: 1;">
                        <button type="button" @click="tolakOpen = !tolakOpen"
                            style="width: 100%;"
                            class="py-4 bg-white border-2 border-red-100 text-red-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-50 transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak Retur
                        </button>
                    </div>
                </div>

                <div x-show="tolakOpen" x-cloak x-collapse class="mt-4 p-5 bg-red-50 rounded-[2rem] border border-red-100 shadow-inner">
                    <form action="{{ route('admin.retur.tolak', $retur->id_retur) }}" method="POST">
                        @csrf
                        <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-2 ml-1">Alasan Penolakan *</label>
                        <textarea name="alasan_penolakan" rows="3" required
                            class="w-full bg-white border-red-100 rounded-xl p-4 text-[13px] font-bold text-gray-900 focus:ring-red-400 focus:border-red-400 transition placeholder:text-red-200 resize-none"
                            placeholder="Tulis alasan mengapa retur ini ditolak..."></textarea>
                        <button type="submit" onclick="return confirm('Yakin ingin menolak retur ini?')"
                            class="mt-4 w-full py-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-700 transition shadow-md shadow-red-200">
                            Kirim Penolakan
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- STATUS: menunggu_rekening → tunggu pelanggan isi rekening --}}
            @if($retur->status_retur === 'menunggu_rekening')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4">Status Aksi</h3>
                <div class="p-4 bg-indigo-50 rounded-2xl text-center">
                    <div class="text-2xl mb-2">🏦</div>
                    <div class="text-[11px] font-black text-indigo-600 uppercase tracking-widest">Menunggu Pelanggan</div>
                    <div class="text-[11px] text-indigo-400 mt-1">Pelanggan sedang mengisi data rekening bank.</div>
                </div>
            </div>
            @endif

            {{-- STATUS: menunggu_barang → Konfirmasi Barang Sampai --}}
            @if($retur->status_retur === 'menunggu_barang')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4">Tindakan Admin</h3>
                <div class="p-4 bg-purple-50 rounded-2xl text-center mb-4">
                    <div class="text-2xl mb-2">📦</div>
                    <div class="text-[11px] font-black text-purple-600 uppercase tracking-widest">Menunggu Barang dari Pelanggan</div>
                    <div class="text-[11px] text-purple-400 mt-1">Konfirmasi ketika barang sudah sampai di toko.</div>
                </div>
                <form action="{{ route('admin.retur.konfirmasi-barang', $retur->id_retur) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Konfirmasi bahwa barang retur sudah sampai di toko?')"
                        style="background-color: #10b981 !important; color: white !important; width: 100%;"
                        class="py-4 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Barang Sudah Sampai
                    </button>
                </form>
            </div>
            @endif

            {{-- STATUS: menunggu_transfer → Upload Bukti Transfer --}}
            @if($retur->status_retur === 'menunggu_transfer')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4">Upload Bukti Transfer</h3>
                <div class="p-4 bg-orange-50 rounded-2xl text-center mb-4">
                    <div class="text-2xl mb-2">💳</div>
                    <div class="text-[11px] font-black text-orange-600 uppercase tracking-widest">Transfer ke Rekening Pelanggan</div>
                    @if($retur->no_rekening)
                    <div class="mt-2 text-left space-y-1">
                        <div class="text-[11px] text-gray-600"><span class="font-bold">Bank:</span> {{ $retur->nama_bank }}</div>
                        <div class="text-[11px] text-gray-600"><span class="font-bold">No. Rek:</span> {{ $retur->no_rekening }}</div>
                        <div class="text-[11px] text-gray-600"><span class="font-bold">a.n.:</span> {{ $retur->nama_pemilik_rekening }}</div>
                    </div>
                    @endif
                </div>
                <form action="{{ route('admin.retur.upload-bukti-transfer', $retur->id_retur) }}" method="POST" enctype="multipart/form-data" class="space-y-4"
                    x-data="{ preview: null }">
                    @csrf
                    <label class="block cursor-pointer">
                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 text-center hover:border-orange-400 transition"
                            :class="preview ? 'border-orange-400' : ''">
                            <template x-if="!preview">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs font-bold text-gray-400">Klik untuk pilih foto bukti transfer</p>
                                    <p class="text-[10px] text-gray-300 mt-1">JPG, PNG, WebP · Maks 2MB</p>
                                </div>
                            </template>
                            <template x-if="preview">
                                <img :src="preview" class="max-h-40 mx-auto rounded-xl object-contain">
                            </template>
                        </div>
                        <input type="file" name="bukti_transfer" accept="image/*" class="hidden" required
                            @change="preview = URL.createObjectURL($event.target.files[0])">
                    </label>
                    <button type="submit" onclick="return confirm('Kirim bukti transfer dan notifikasi ke pelanggan?')"
                        style="background-color: #f97316 !important; color: white !important; width: 100%;"
                        class="py-4 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-lg">
                        Kirim Bukti Transfer
                    </button>
                </form>
            </div>
            @endif

            {{-- STATUS: uang_ditransfer → Menunggu Konfirmasi Pelanggan --}}
            @if($retur->status_retur === 'uang_ditransfer')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4">Status Aksi</h3>
                <div class="p-4 bg-blue-50 rounded-2xl text-center">
                    <div class="text-2xl mb-2">⏳</div>
                    <div class="text-[11px] font-black text-blue-600 uppercase tracking-widest">Menunggu Konfirmasi Pelanggan</div>
                    <div class="text-[11px] text-blue-400 mt-1">Pelanggan akan mengkonfirmasi bahwa dana sudah diterima.</div>
                </div>
            </div>
            @endif

            {{-- STATUS: selesai --}}
            @if($retur->status_retur === 'selesai')
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="text-center">
                    <div class="text-4xl mb-3">🎉</div>
                    <div class="text-[12px] font-black text-green-600 uppercase tracking-widest">Retur Selesai</div>
                    <div class="text-[11px] text-gray-400 mt-1">Pelanggan telah mengkonfirmasi penerimaan dana.</div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>
