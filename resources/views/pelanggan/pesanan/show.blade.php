<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #{{ $pesanan->id_pesanan }} - Mini Workshop</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-900 overflow-x-hidden">

    <!-- Navigation -->
    <nav class="bg-white py-5 px-6 md:px-12 sticky top-0 z-50 shadow-sm" x-data="{ helpOpen: false }">
        <div class="w-full flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                <h1 class="text-lg font-black uppercase tracking-tighter text-gray-900">MINI WORKSHOP</h1>
            </a>
            <div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Dashboard</a>
                <a href="{{ route('pesanan.saya') }}" class="text-yellow-600">Pesanan</a>
            </div>
            <div class="flex items-center gap-3 md:gap-6">
                @include('layouts.help-modal')
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open">
                        @if(Auth::user()->foto_profil)
                            <img src="{{ asset('images/profil/' . Auth::user()->foto_profil) }}" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=random" class="w-9 h-9 rounded-full border border-gray-200">
                        @endif
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 md:px-12 py-12" x-data="{ uploadOpen: false, returOpen: false, returProdukId: '', returProdukNama: '' }">

        {{-- Notifikasi retur hanya untuk pesanan ini --}}
        @auth
        @php
            $notifsPesanan = Auth::user()->unreadNotifications->filter(
                fn ($notif) => isset($notif->data['id_pesanan'])
                    && (int) $notif->data['id_pesanan'] === (int) $pesanan->id_pesanan
            );
        @endphp
        @if($notifsPesanan->isNotEmpty())
        <div class="mb-6 space-y-3">
            @foreach($notifsPesanan as $notif)
            <div class="flex items-start gap-4 bg-indigo-50 border border-indigo-100 rounded-2xl px-5 py-4">
                <div class="shrink-0 w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12px] font-black text-indigo-700 uppercase tracking-widest mb-0.5">{{ $notif->data['judul'] ?? 'Notifikasi' }}</div>
                    <p class="text-[12px] text-gray-700 leading-relaxed">{{ $notif->data['pesan'] ?? '' }}</p>
                    @if(isset($notif->data['id_produk']))
                    <a href="#retur-produk-{{ $notif->data['id_produk'] }}"
                        class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition">
                        Lihat Produk Retur
                    </a>
                    @endif
                </div>
                <form action="{{ route('notifikasi.baca', $notif->id) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="text-indigo-300 hover:text-indigo-600 transition" title="Tandai sudah dibaca">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
        @endauth
        <a href="{{ route('pesanan.saya') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Pesanan Saya
        </a>

        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-8">Detail Pesanan #{{ $pesanan->id_pesanan }}</h2>

        @if($pesanan->status_pesanan === 'menunggu_pembayaran' && !$pesanan->isPaymentExpired())
        <div class="mb-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl"
             x-data="{
                deadline: '{{ $pesanan->batasPembayaran()->toIso8601String() }}',
                remaining: '',
                init() { this.tick(); setInterval(() => this.tick(), 1000); },
                tick() {
                    const diff = new Date(this.deadline) - new Date();
                    if (diff <= 0) { this.remaining = 'Waktu habis'; return; }
                    const h = Math.floor(diff / 3600000);
                    const m = Math.floor((diff % 3600000) / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    this.remaining = `${h} jam ${m} menit ${s} detik`;
                }
             }">
            <p class="text-sm font-bold text-amber-900">
                Batas unggah bukti pembayaran: <span class="font-black" x-text="remaining"></span>
            </p>
            <p class="text-xs text-amber-700 mt-1">
                Selesaikan pembayaran sebelum {{ $pesanan->batasPembayaran()->format('d M Y, H:i') }} WIB.
                Jika lewat 24 jam, pesanan dibatalkan otomatis dan stok dikembalikan ke katalog.
            </p>
        </div>
        @endif

        @if($pesanan->status_pesanan === 'dibatalkan')
        <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-2xl">
            <p class="text-sm font-bold text-red-800">
                Pesanan ini dibatalkan. Stok produk telah dikembalikan ke katalog.
            </p>
        </div>
        @endif

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

            {{-- Kiri: Produk & Info --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Konfirmasi Penerimaan --}}
                @if($pesanan->status_pesanan === 'diantar')
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-[2rem] p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-yellow-600 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-yellow-800 uppercase tracking-widest mb-1">Konfirmasi Penerimaan Barang</h4>
                            <p class="text-xs text-yellow-700 leading-relaxed mb-4">
                                Kurir telah mengonfirmasi bahwa produk Anda sudah diantar ke alamat tujuan. Apakah Anda sudah menerima barang tersebut dengan aman dan sesuai?
                            </p>
                            <form action="{{ route('pesanan.konfirmasi-diterima', $pesanan->id_pesanan) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa Anda telah menerima produk? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-400 text-gray-900 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Ya, Sudah Diterima
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Produk --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-5">Produk Dipesan</h3>
                    <div class="space-y-5">
                        @foreach($pesanan->detail as $item)
                        @php
                            $returItem = $pesanan->retur->firstWhere('id_produk', $item->id_produk);
                            $sudahRetur = $returItem !== null;
                            $bisaAjukanRetur = in_array($pesanan->status_pesanan, ['selesai', 'diretur'], true) && ! $sudahRetur;
                            $returBadge = $returItem ? \App\Models\Retur::statusBadge($returItem->status_retur) : null;
                        @endphp
                        <div id="retur-produk-{{ $item->id_produk }}" class="rounded-2xl {{ $returItem?->canPrintLabel() ? 'ring-2 ring-indigo-200 bg-indigo-50/40 p-4 -mx-1' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                    @if($item->produk->gambar_url)
                                        <img src="{{ $item->produk->gambar_url }}" alt="{{ $item->produk->nama_produk }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-900 truncate">{{ $item->produk->nama_produk }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $item->qty }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-sm font-black text-gray-900">Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</div>
                                    @if($sudahRetur && $returBadge)
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest whitespace-nowrap {{ $returBadge['class'] }}">
                                                {{ $returBadge['label'] }}
                                            </span>
                                            @if($returItem->status_retur === 'ditolak' && $returItem->alasan_penolakan)
                                            <span class="text-[9px] text-red-400 max-w-[120px] text-right leading-tight" title="{{ $returItem->alasan_penolakan }}">
                                                {{ Str::limit($returItem->alasan_penolakan, 40) }}
                                            </span>
                                            @endif
                                        </div>
                                    @elseif($bisaAjukanRetur)
                                        <button
                                            @click="returOpen = true; returProdukId = '{{ $item->id_produk }}'; returProdukNama = '{{ addslashes($item->produk->nama_produk) }}'"
                                            class="px-3 py-1.5 bg-red-50 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition whitespace-nowrap">
                                            Retur
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($returItem)
                                @if($returItem->status_retur === 'menunggu_rekening')
                                <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Menunggu Rekening Anda</p>
                                    <p class="text-xs text-gray-600 mb-3">Admin telah menyetujui retur ini. Silakan masukkan nomor rekening Anda untuk pengembalian dana.</p>
                                    <form action="{{ route('retur.kirim-rekening', [$pesanan->id_pesanan, $returItem->id_retur]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Nama Bank *</label>
                                                <input type="text" name="nama_bank" required placeholder="BCA / Mandiri / BRI" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-[12px] font-bold focus:ring-indigo-400 focus:border-indigo-400">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Nomor Rekening *</label>
                                                <input type="text" name="no_rekening" required placeholder="0123456789" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-[12px] font-bold focus:ring-indigo-400 focus:border-indigo-400">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Atas Nama *</label>
                                                <input type="text" name="nama_pemilik_rekening" required placeholder="Nama pemilik" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-[12px] font-bold focus:ring-indigo-400 focus:border-indigo-400">
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full mt-2 py-2.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition">Kirim Data Rekening</button>
                                    </form>
                                </div>
                                @endif

                                @if($returItem->status_retur === 'menunggu_barang')
                                <div class="mt-4 p-4 bg-purple-50 border border-purple-100 rounded-xl">
                                    <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest mb-1">Kirim Barang Retur</p>
                                    <p class="text-xs text-gray-600 mb-2">
                                        Terima kasih, data rekening Anda sudah tersimpan. Selanjutnya, <strong>silakan lakukan pengiriman barang retur</strong> ke alamat toko kami.
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        Jangan lupa mencetak label pengiriman di bawah ini dan menempelkannya pada paket Anda.
                                    </p>
                                </div>
                                @endif

                                @if($returItem->status_retur === 'menunggu_transfer')
                                <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl">
                                    <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-1">Barang Telah Diterima Toko</p>
                                    <p class="text-xs text-gray-600">
                                        Toko telah menerima barang retur Anda. Saat ini Admin sedang memproses transfer pengembalian dana ke rekening Anda. Mohon ditunggu ya!
                                    </p>
                                </div>
                                @endif

                                @if($returItem->canPrintLabel())
                                <div class="mt-4 p-4 bg-white border border-indigo-100 rounded-xl">
                                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Label Pengiriman Retur</p>
                                    <p class="text-xs text-gray-600 mb-3">
                                        Cetak label untuk produk <strong>{{ $item->produk->nama_produk }}</strong>@if($item->produk->size) ({{ $item->produk->size }})@endif — tempelkan pada paket retur dan kirim ke toko.
                                    </p>
                                    <a href="{{ route('pesanan.label-retur', [$pesanan->id_pesanan, $returItem->id_retur]) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Cetak Label — {{ $item->produk->nama_produk }}
                                    </a>
                                </div>
                                @endif

                                @if($returItem->status_retur === 'uang_ditransfer')
                                <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Dana Telah Ditransfer</p>
                                    <p class="text-xs text-gray-600 mb-3">Admin telah mentransfer dana ke rekening Anda. Silakan cek saldo Anda dan konfirmasi jika sudah masuk.</p>
                                    
                                    @if($returItem->bukti_transfer)
                                    <a href="{{ asset('images/bukti_transfer/' . $returItem->bukti_transfer) }}" target="_blank" class="inline-block mb-4">
                                        <img src="{{ asset('images/bukti_transfer/' . $returItem->bukti_transfer) }}" class="h-16 rounded-lg border border-gray-200">
                                    </a>
                                    @endif

                                    <form action="{{ route('retur.konfirmasi-selesai', [$pesanan->id_pesanan, $returItem->id_retur]) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Konfirmasi bahwa dana sudah Anda terima?')" class="w-full py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition">Dana Sudah Diterima</button>
                                    </form>
                                </div>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-5 border-t border-gray-100 flex justify-between">
                        <span class="text-sm font-bold text-gray-400">Total Pembayaran</span>
                        <span class="text-lg font-black text-gray-900">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Alamat Pengiriman</h3>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $pesanan->alamat_pengiriman }}</p>
                    <div class="text-[11px] text-gray-400 mt-1">No. HP: {{ $pesanan->no_hp }}</div>
                </div>

            </div>

            {{-- Kanan: Status & Pembayaran --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Info Pengiriman (Biteship) --}}
                @if($pesanan->resi && $pesanan->kurir)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Lacak Pengiriman</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-medium">Kurir</span>
                            <span class="font-bold text-gray-900 uppercase">{{ $pesanan->kurir }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-medium">No. Resi</span>
                            <span class="font-bold text-gray-900">{{ $pesanan->resi }}</span>
                        </div>
                        @if(isset($tracking) && $tracking['success'] && isset($tracking['history']) && count($tracking['history']) > 0)
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Riwayat Pengiriman</div>
                            <div class="space-y-0">
                                @php
                                    $statusMap = [
                                        'allocated' => 'Kurir Dialokasikan',
                                        'picking_up' => 'Proses Penjemputan',
                                        'picked' => 'Penjemputan Berhasil',
                                        'dropping_off' => 'Menuju Lokasi Tujuan',
                                        'delivered' => 'Paket Terkirim',
                                        'rejected' => 'Ditolak',
                                        'cancelled' => 'Dibatalkan',
                                        'dropped' => 'Diserahkan ke Kurir',
                                        'delivering' => 'Sedang Dikirim'
                                    ];
                                    $historyItems = array_reverse($tracking['history']);
                                @endphp
                                @foreach($historyItems as $index => $item)
                                    @php
                                        $isFirst = $index === 0;
                                        $statusTitle = $statusMap[$item['status']] ?? ucwords(str_replace('_', ' ', $item['status']));
                                    @endphp
                                    <div class="relative flex gap-4 pb-6">
                                        <!-- Timeline Column -->
                                        <div class="relative flex flex-col items-center w-4 shrink-0">
                                            <!-- Dot -->
                                            <div class="w-4 h-4 rounded-full border-2 {{ $isFirst ? 'border-blue-600 bg-white' : 'border-gray-300 bg-white' }} flex items-center justify-center z-10 mt-0.5 shrink-0">
                                                <div class="w-2 h-2 rounded-full {{ $isFirst ? 'bg-blue-600' : 'bg-gray-400' }}"></div>
                                            </div>
                                            <!-- Line -->
                                            @if(!$loop->last)
                                                <div class="absolute top-4 bottom-[-2px] w-[2px] {{ $isFirst ? 'bg-blue-600' : 'bg-gray-200' }} z-0"></div>
                                            @endif
                                        </div>

                                        <!-- Content Column -->
                                        <div class="flex-1">
                                            <div class="text-[13px] font-bold {{ $isFirst ? 'text-blue-900' : 'text-gray-700' }} mb-0.5">{{ $statusTitle }}</div>
                                            <div class="text-[11px] text-gray-500 leading-relaxed mb-1">{{ $item['note'] ?? '' }}</div>
                                            <div class="text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($item['updated_at'])->translatedFormat('d M Y H:i') }} WIB</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Status Pesanan --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Status Pesanan</h3>
                    @php
                        $isReturSelesai = $pesanan->status_pesanan === 'selesai' && $pesanan->retur->contains('status_retur', 'selesai');
                        
                        $statusConfig = [
                            'menunggu_pembayaran' => ['class' => 'bg-yellow-50 text-yellow-600', 'label' => 'Menunggu Pembayaran'],
                            'dikemas'             => ['class' => 'bg-blue-50 text-blue-600',   'label' => 'Sedang Dikemas'],
                            'dikirim'             => ['class' => 'bg-purple-50 text-purple-600','label' => 'Sedang Dikirim'],
                            'diantar'             => ['class' => 'bg-orange-50 text-orange-600','label' => 'Sudah Diantar Kurir'],
                            'selesai'             => ['class' => 'bg-green-50 text-green-600', 'label' => $isReturSelesai ? 'Retur Selesai' : 'Selesai'],
                            'diretur'             => ['class' => 'bg-indigo-50 text-indigo-600','label' => 'Sedang Diretur'],
                            'dibatalkan'          => ['class' => 'bg-red-50 text-red-600',     'label' => 'Dibatalkan'],
                        ];
                        $sc = $statusConfig[$pesanan->status_pesanan] ?? ['class' => 'bg-gray-50 text-gray-600', 'label' => $pesanan->status_pesanan];
                    @endphp
                    <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sc['class'] }}">
                        {{ $sc['label'] }}
                    </span>

                    {{-- Tombol Batalkan --}}
                    @if($pesanan->status_pesanan === 'menunggu_pembayaran' && !$pesanan->isPaymentExpired() && $pesanan->pembayaran?->status_pembayaran !== 'menunggu_konfirmasi')
                    <div class="mt-5 pt-5 border-t border-gray-100" x-data="{ konfirmasi: false }">
                        <button type="button" @click="konfirmasi = true"
                            class="w-full py-3 bg-red-50 text-red-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-500 hover:text-white transition">
                            Batalkan Pesanan
                        </button>

                        {{-- Konfirmasi inline --}}
                        <div x-show="konfirmasi" x-cloak x-transition
                            class="mt-3 p-4 bg-red-50 border border-red-100 rounded-2xl">
                            <p class="text-xs font-bold text-red-700 mb-3 text-center">Yakin ingin membatalkan pesanan ini?</p>
                            <div class="flex gap-2">
                                <button type="button" @click="konfirmasi = false"
                                    class="flex-1 py-2.5 bg-white border border-gray-200 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition">
                                    Tidak
                                </button>
                                <form action="{{ route('pesanan.batalkan', $pesanan->id_pesanan) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full py-2.5 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-600 transition">
                                        Ya, Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Info Pembayaran --}}
                @if($pesanan->pembayaran)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Pembayaran</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-medium">Metode</span>
                            <span class="font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $pesanan->pembayaran->metode_pembayaran) }}</span>
                        </div>
                        @if($pesanan->pembayaran->bank_tujuan)
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-medium">Bank</span>
                            <span class="font-bold text-gray-900">{{ $pesanan->pembayaran->bank_tujuan }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-medium">Status</span>
                            @php
                                $spConfig = [
                                    'belum_dibayar'        => 'bg-yellow-50 text-yellow-600',
                                    'menunggu_konfirmasi'  => 'bg-blue-50 text-blue-600',
                                    'berhasil'             => 'bg-green-50 text-green-600',
                                    'ditolak'              => 'bg-red-50 text-red-600',
                                ];
                                $spClass = $spConfig[$pesanan->pembayaran->status_pembayaran] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $spClass }}">
                                {{ str_replace('_', ' ', $pesanan->pembayaran->status_pembayaran) }}
                            </span>
                        </div>
                    </div>

                    {{-- Tampilkan info rekening jika transfer bank --}}
                    @if($pesanan->pembayaran->metode_pembayaran === 'transfer_bank' && $pesanan->pembayaran->bank_tujuan)
                    @php
                        $rekeningInfo = [
                            'BRI' => ['no_rek' => '1234-5678-9012-3456', 'atas_nama' => 'Mini Workshop Store'],
                            'BNI' => ['no_rek' => '9876-5432-1098-7654', 'atas_nama' => 'Mini Workshop Store'],
                            'BCA' => ['no_rek' => '5555-1234-5678-9000', 'atas_nama' => 'Mini Workshop Store'],
                        ];
                        $rek = $rekeningInfo[$pesanan->pembayaran->bank_tujuan] ?? null;
                    @endphp
                    @if($rek)
                    <div class="mt-4 p-4 bg-blue-50 rounded-2xl">
                        <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Transfer ke</div>
                        <div class="font-black text-blue-700 text-base">{{ $pesanan->pembayaran->bank_tujuan }}</div>
                        <div class="font-bold text-gray-900 tracking-wider mt-1">{{ $rek['no_rek'] }}</div>
                        <div class="text-xs text-gray-500">a.n. {{ $rek['atas_nama'] }}</div>
                        <div class="mt-3 pt-3 border-t border-blue-100 flex justify-between items-center">
                            <span class="text-xs text-blue-400">Jumlah Transfer</span>
                            <span class="font-black text-blue-700">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif
                    @endif

                    {{-- Tampilkan QRIS jika metode qris --}}
                    @if($pesanan->pembayaran->metode_pembayaran === 'qris')
                    <div class="mt-4 flex flex-col items-center p-4 bg-gray-50 rounded-2xl">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Scan QRIS</div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=MiniWorkshopQRIS2026&bgcolor=ffffff&color=000000&margin=10"
                            alt="QRIS" class="w-36 h-36 rounded-xl">
                        <div class="text-xs text-gray-400 mt-2 text-center">Bayar sebesar<br><span class="font-black text-gray-900">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span></div>
                    </div>
                    @endif

                    {{-- Bukti yang sudah diupload --}}
                    @if($pesanan->pembayaran->bukti_pembayaran)
                    <div class="mt-4">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bukti Pembayaran</div>
                        <img src="{{ asset('images/bukti/' . $pesanan->pembayaran->bukti_pembayaran) }}"
                            alt="Bukti Pembayaran" class="w-full rounded-2xl border border-gray-100 cursor-pointer"
                            onclick="window.open(this.src)">
                    </div>
                    @endif

                    {{-- Alasan penolakan --}}
                    @if($pesanan->pembayaran->status_pembayaran === 'ditolak' && $pesanan->pembayaran->alasan_penolakan)
                    <div class="mt-3 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <div class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Alasan Penolakan</div>
                        <p class="text-xs text-red-700 leading-relaxed">{{ $pesanan->pembayaran->alasan_penolakan }}</p>
                    </div>
                    @endif

                    {{-- Tombol Upload Bukti --}}
                    @if($pesanan->canUploadBuktiPembayaran())
                    <button @click="uploadOpen = true"
                        class="mt-4 w-full py-3 bg-yellow-400 text-gray-900 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-yellow-500 transition shadow-sm">
                        {{ $pesanan->pembayaran->bukti_pembayaran ? 'Unggah Ulang Bukti' : 'Unggah Bukti Pembayaran' }}
                    </button>
                    @elseif($pesanan->status_pesanan === 'menunggu_pembayaran' && $pesanan->isPaymentExpired())
                    <div class="mt-4 p-3 bg-red-50 rounded-xl text-xs text-red-600 font-bold text-center">
                        Batas waktu pembayaran 24 jam telah habis. Pesanan dibatalkan dan stok dikembalikan.
                    </div>
                    @endif

                    @if($pesanan->pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                    <div class="mt-4 p-3 bg-blue-50 rounded-xl text-xs text-blue-600 font-bold text-center">
                        Bukti pembayaran sedang diverifikasi admin.
                    </div>
                    @endif

                    {{-- Tombol Invoice --}}
                    @if($pesanan->pembayaran->status_pembayaran === 'berhasil')
                    <a href="{{ route('pesanan.invoice', $pesanan->id_pesanan) }}" target="_blank"
                        class="mt-4 flex items-center justify-center gap-2 w-full py-3 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-800 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Invoice
                    </a>
                    @endif
                </div>
                @endif

            </div>
        </div>

        {{-- Modal Upload Bukti --}}
        <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="uploadOpen = false" class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-lg font-black text-gray-900 mb-1">Unggah Bukti Pembayaran</h3>
                <p class="text-xs text-gray-400 mb-6">Format: JPG, PNG. Maks 2MB.</p>

                <form action="{{ route('pesanan.upload-bukti', $pesanan->id_pesanan) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div x-data="{ preview: null }">
                        <label class="block w-full cursor-pointer">
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-yellow-400 transition"
                                :class="preview ? 'border-yellow-400' : ''">
                                <template x-if="!preview">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs font-bold text-gray-400">Klik untuk pilih foto</p>
                                    </div>
                                </template>
                                <template x-if="preview">
                                    <img :src="preview" class="max-h-48 mx-auto rounded-xl object-contain">
                                </template>
                            </div>
                            <input type="file" name="bukti_pembayaran" accept="image/*" class="hidden"
                                @change="preview = URL.createObjectURL($event.target.files[0])" required>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="uploadOpen = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-yellow-400 text-gray-900 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-yellow-500 transition shadow-sm">
                            Kirim Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Retur --}}
        <div x-show="returOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="returOpen = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-black text-gray-900 mb-1">Ajukan Retur Produk</h3>
                <p class="text-xs text-gray-400 mb-1">Produk: <span class="font-bold text-gray-900" x-text="returProdukNama"></span></p>
                <p class="text-xs text-gray-400 mb-6">Jelaskan alasan dan sertakan foto bukti kondisi produk.</p>

                <form action="{{ route('retur.store', $pesanan->id_pesanan) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="id_produk" x-bind:value="returProdukId">

                    {{-- Alasan Retur --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Alasan Retur <span class="text-red-500">*</span></label>
                        <textarea name="alasan_retur" rows="3" required maxlength="500"
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"
                            placeholder="Contoh: Produk tidak sesuai deskripsi, ukuran tidak pas, cacat produk, dll."></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Maksimal 500 karakter</p>
                    </div>

                    {{-- Upload Foto Bukti --}}
                    <div x-data="{
                        previews: [],
                        files: [],
                        addFiles(event) {
                            const newFiles = Array.from(event.target.files);
                            const total = this.files.length + newFiles.length;
                            if (total > 3) {
                                alert('Maksimal 3 foto yang dapat diunggah.');
                                event.target.value = '';
                                return;
                            }
                            newFiles.forEach(file => {
                                this.files.push(file);
                                const reader = new FileReader();
                                reader.onload = e => this.previews.push(e.target.result);
                                reader.readAsDataURL(file);
                            });
                            event.target.value = '';
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.previews.splice(index, 1);
                        }
                    }" x-init="
                        $watch('files', value => {
                            const dt = new DataTransfer();
                            value.forEach(f => dt.items.add(f));
                            $refs.fileInput.files = dt.files;
                        })
                    ">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                            Foto Bukti <span class="text-red-500">*</span>
                            <span class="normal-case font-normal text-gray-400 ml-1">(min. 1, maks. 3 foto · JPG/PNG · maks. 2MB/foto)</span>
                        </label>

                        {{-- Preview Grid --}}
                        <div class="grid grid-cols-3 gap-3 mb-3" x-show="previews.length > 0">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-200">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <button type="button" @click="removeFile(i)"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-black hover:bg-red-600 transition shadow">
                                        ✕
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Tombol Tambah Foto --}}
                        <label x-show="previews.length < 3"
                            class="flex items-center justify-center gap-2 w-full py-4 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-xs font-bold text-gray-400" x-text="previews.length === 0 ? 'Tambah Foto Bukti' : 'Tambah Foto Lagi'"></span>
                            <input type="file" name="foto_bukti[]" accept="image/*" multiple class="hidden"
                                x-ref="fileInput"
                                @change="addFiles($event)">
                        </label>

                        <p class="text-[10px] text-gray-400 mt-1.5" x-text="previews.length + '/3 foto dipilih'"></p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="returOpen = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-red-500 text-white text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-red-600 transition shadow-sm">
                            Ajukan Retur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('layouts.footer')

</body>
</html>
