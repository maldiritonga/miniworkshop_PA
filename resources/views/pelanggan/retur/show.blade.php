<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Retur #{{ $retur->id_retur }} - Mini Workshop</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fafafa; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-900 overflow-x-hidden">

    <!-- Navigation -->
    <nav class="bg-white py-5 px-6 md:px-12 sticky top-0 z-50 shadow-sm" x-data="{ helpOpen: false }">
        <div class="w-full flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    <h1 class="text-lg font-black uppercase tracking-tighter text-gray-900">MINI WORKSHOP</h1>
                </a>
            </div>

            <div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Dasboard</a>
                <a href="{{ route('home') }}#catalog" class="hover:text-yellow-600 transition">katalog</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">About</a>
                <a href="{{ route('pesanan.saya') }}" class="{{ request()->routeIs('pesanan.*') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Pesanan</a>
                <a href="{{ route('retur.saya') }}" class="{{ request()->routeIs('retur.*') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Retur</a>                                
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <a href="{{ route('keranjang.index') }}" class="hidden sm:inline-block px-5 py-2.5 bg-[#f5efe6] text-gray-800 font-bold text-xs rounded-lg hover:bg-[#ebdccc] transition">Keranjang ({{ $cartCount ?? 0 }})</a>
                @include('layouts.help-modal')
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 bg-white">
                                @if(Auth::user()->foto_profil)
                                    <img src="{{ asset('images/profil/' . Auth::user()->foto_profil) }}" alt="User Profile" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=random" alt="User Profile" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-100">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Profil Saya</a>
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

    <main class="max-w-4xl mx-auto px-6 md:px-12 py-12">
        <a href="{{ route('retur.saya') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 hover:text-yellow-600 transition mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Retur Saya
        </a>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detail Retur #{{ $retur->id_retur }}</h2>
            @php
                $statusBadge = \App\Models\Retur::statusBadge($retur->status_retur);
            @endphp
            <span class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest {{ $statusBadge['class'] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $statusBadge['label'] ?? ucfirst(str_replace('_', ' ', $retur->status_retur)) }}
            </span>
        </div>

        <x-flash-message />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <!-- Product Info -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-5">Produk Diretur</h3>
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                            @if($retur->produk->gambar_url)
                                <img src="{{ $retur->produk->gambar_url }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="text-base font-bold text-gray-900 mb-1">{{ $retur->produk->nama_produk }}</div>
                            <div class="text-xs text-gray-500 mb-3">Dari Pesanan <a href="{{ route('pesanan.show', $retur->id_pesanan) }}" class="text-indigo-600 hover:underline font-bold">#{{ $retur->id_pesanan }}</a></div>
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alasan Retur</div>
                                <p class="text-sm text-gray-700">{{ $retur->alasan_retur }}</p>
                            </div>
                        </div>
                    </div>

                    @if($retur->foto_bukti)
                    <div class="mt-6">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Foto Bukti</div>
                        <div class="flex gap-3 overflow-x-auto pb-2">
                            @php
                                $fotos = is_array($retur->foto_bukti) ? $retur->foto_bukti : json_decode($retur->foto_bukti, true);
                                if(!$fotos) $fotos = [$retur->foto_bukti];
                            @endphp
                            @foreach($fotos as $foto)
                                @if(is_string($foto))
                                    <a href="{{ asset('images/retur/' . $foto) }}" target="_blank" class="shrink-0">
                                        <img src="{{ asset('images/retur/' . $foto) }}" class="w-24 h-24 object-cover rounded-xl border border-gray-200 hover:opacity-80 transition">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Status Action Boxes -->
                @if($retur->status_retur === 'ditolak' && $retur->alasan_penolakan)
                <div class="p-6 bg-red-50 border border-red-100 rounded-[2rem]">
                    <h3 class="text-xs font-black text-red-600 uppercase tracking-widest mb-2">Retur Ditolak</h3>
                    <p class="text-sm text-red-700">{{ $retur->alasan_penolakan }}</p>
                </div>
                @endif

                @if($retur->status_retur === 'menunggu_rekening')
                <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-[2rem]">
                    <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-2">Menunggu Rekening Anda</h3>
                    <p class="text-sm text-gray-600 mb-4">Admin telah menyetujui retur ini. Silakan masukkan nomor rekening Anda untuk pengembalian dana.</p>
                    
                    <div class="mb-5 p-4 bg-yellow-50/80 border border-yellow-200/60 rounded-2xl flex gap-3 items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-yellow-800">Informasi Biaya Transfer</p>
                            <p class="text-xs font-medium text-yellow-700 mt-1 leading-relaxed">Rekening utama toko kami menggunakan <strong>Bank Mandiri</strong>. Jika Anda menggunakan bank selain Mandiri, biaya transfer antar bank (sekitar Rp 2.500 - Rp 5.000) akan <strong>ditanggung oleh Anda</strong> dan langsung dipotong dari total dana yang dikembalikan. Gunakan rekening Mandiri untuk menghindari biaya admin.</p>
                        </div>
                    </div>

                    <form action="{{ route('retur.kirim-rekening', [$retur->id_pesanan, $retur->id_retur]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Nama Bank *</label>
                                <input type="text" name="nama_bank" required placeholder="Contoh: BCA" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-400 focus:border-indigo-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Nomor Rekening *</label>
                                <input type="text" name="no_rekening" required placeholder="0123456789" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-400 focus:border-indigo-400">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Atas Nama *</label>
                                <input type="text" name="nama_pemilik_rekening" required placeholder="Nama pemilik rekening" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-400 focus:border-indigo-400">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition shadow-sm mt-2">Kirim Data Rekening</button>
                    </form>
                </div>
                @endif

                @if($retur->status_retur === 'menunggu_barang')
                <div class="p-6 bg-purple-50 border border-purple-100 rounded-[2rem]">
                    <h3 class="text-xs font-black text-purple-600 uppercase tracking-widest mb-2">Kirim Barang Retur</h3>
                    <p class="text-sm text-gray-700 mb-3">Terima kasih, data rekening Anda sudah tersimpan. Selanjutnya, <strong>silakan lakukan pengiriman barang retur</strong> ke alamat toko kami.</p>
                    <p class="text-sm text-gray-700 mb-5">Jangan lupa mencetak label pengiriman di bawah ini dan menempelkannya pada paket Anda.</p>
                    
                    @if($retur->canPrintLabel())
                    <a href="{{ route('pesanan.label-retur', [$retur->id_pesanan, $retur->id_retur]) }}" target="_blank"
                        class="inline-flex items-center justify-center gap-2 w-full px-6 py-3.5 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Label Pengiriman
                    </a>
                    @endif
                </div>
                @endif

                @if($retur->status_retur === 'menunggu_transfer')
                <div class="p-6 bg-orange-50 border border-orange-100 rounded-[2rem]">
                    <h3 class="text-xs font-black text-orange-600 uppercase tracking-widest mb-2">Barang Telah Diterima Toko</h3>
                    <p class="text-sm text-gray-700">Toko telah menerima barang retur Anda. Saat ini Admin sedang memproses transfer pengembalian dana ke rekening Anda. Mohon ditunggu ya!</p>
                </div>
                @endif

                @if($retur->status_retur === 'uang_ditransfer')
                <div class="p-6 bg-blue-50 border border-blue-100 rounded-[2rem]">
                    <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-2">Dana Telah Ditransfer</h3>
                    <p class="text-sm text-gray-700 mb-4">Admin telah mentransfer dana ke rekening Anda. Silakan cek saldo Anda dan konfirmasi jika sudah masuk.</p>
                    
                    @if($retur->bukti_transfer)
                    <div class="mb-5">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bukti Transfer</div>
                        <a href="{{ asset('images/bukti_transfer/' . $retur->bukti_transfer) }}" target="_blank" class="inline-block">
                            <img src="{{ asset('images/bukti_transfer/' . $retur->bukti_transfer) }}" class="h-24 rounded-xl border border-gray-200">
                        </a>
                    </div>
                    @endif

                    <form action="{{ route('retur.konfirmasi-selesai', [$retur->id_pesanan, $retur->id_retur]) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Konfirmasi bahwa dana sudah Anda terima?')" class="w-full py-3.5 bg-blue-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition shadow-sm">Dana Sudah Diterima</button>
                    </form>
                </div>
                @endif

                @if($retur->status_retur === 'selesai')
                <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[2rem] flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-emerald-800 uppercase tracking-widest mb-1">Proses Retur Selesai</h3>
                        <p class="text-xs text-emerald-700">Dana telah Anda terima dan proses pengembalian barang sudah tuntas.</p>
                    </div>
                </div>
                @endif

            </div>
            
            <div class="md:col-span-1 space-y-6">
                <!-- Sidebar info -->
                @if($retur->nama_bank && $retur->no_rekening)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Rekening Anda</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Bank</div>
                            <div class="text-sm font-bold text-gray-900">{{ $retur->nama_bank }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Nomor Rekening</div>
                            <div class="text-sm font-bold text-gray-900">{{ $retur->no_rekening }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Atas Nama</div>
                            <div class="text-sm font-bold text-gray-900">{{ $retur->nama_pemilik_rekening }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
