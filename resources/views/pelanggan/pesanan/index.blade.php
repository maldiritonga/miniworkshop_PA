<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Mini Workshop</title>
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
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    <h1 class="text-lg font-black uppercase tracking-tighter text-gray-900">MINI WORKSHOP</h1>
                </a>
            </div>

            <div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Dasboard</a>
                <a href="{{ route('home') }}#catalog" class="hover:text-yellow-600 transition">katalog</a>
                <a href="{{ route('about') }}" class="hover:text-yellow-600 transition">About</a>
                <a href="{{ route('pesanan.saya') }}" class="text-yellow-600 transition">pesanan</a>
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

    <main class="max-w-5xl mx-auto px-6 md:px-12 py-12">
        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-10">Pesanan Saya</h2>
        <x-flash-message />

        @forelse($pesanan as $order)
        <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm border border-gray-50 mb-6 hover:shadow-md transition duration-300">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-50 rounded-2xl text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">ID Pesanan: #{{ $order->id_pesanan }}</div>
                        <div class="text-xs font-bold text-gray-600">{{ $order->tanggal_pesanan }}</div>
                    </div>
                </div>
                <div>
                    @php
                        $isReturSelesai = $order->status_pesanan === 'selesai' && $order->retur->contains('status_retur', 'selesai');

                        $statusClass = [
                            'menunggu_pembayaran' => 'bg-yellow-50 text-yellow-600',
                            'dikemas'    => 'bg-blue-50 text-blue-600',
                            'dikirim'    => 'bg-purple-50 text-purple-600',
                            'diantar'    => 'bg-orange-50 text-orange-600',
                            'selesai'    => 'bg-green-50 text-green-600',
                            'diretur'    => 'bg-indigo-50 text-indigo-600',
                            'dibatalkan' => 'bg-red-50 text-red-600',
                        ][$order->status_pesanan] ?? 'bg-gray-50 text-gray-600';

                        $statusLabel = [
                            'menunggu_pembayaran' => 'Menunggu Pembayaran',
                            'dikemas'    => 'Sedang Dikemas',
                            'dikirim'    => 'Sedang Dikirim',
                            'diantar'    => 'Sudah Diantar',
                            'selesai'    => $isReturSelesai ? 'Retur Selesai' : 'Selesai',
                            'diretur'    => 'Sedang Diretur',
                            'dibatalkan' => 'Dibatalkan',
                        ][$order->status_pesanan] ?? ucfirst(str_replace('_', ' ', $order->status_pesanan));
                    @endphp
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    @if($order->status_pesanan === 'menunggu_pembayaran' && !$order->isPaymentExpired())
                    <p class="text-[10px] font-bold text-amber-600 mt-2 text-right">
                        Bayar sebelum {{ $order->batasPembayaran()->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="flex-1 w-full">
                    <div class="space-y-4">
                        @foreach($order->detail as $detail)
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                @if($detail->produk->gambar_url)
                                    <img src="{{ $detail->produk->gambar_url }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=100&q=80" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-gray-900 truncate">{{ $detail->produk->nama_produk }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">{{ $detail->qty }} x Rp {{ number_format($detail->harga, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="md:w-48 w-full md:text-right">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Belanja</div>
                    <div class="text-lg font-black text-gray-900 mb-4">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                    <a href="{{ route('pesanan.show', $order->id_pesanan) }}" class="inline-block px-6 py-2.5 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm">
                        Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="py-32 text-center bg-white rounded-[3.5rem] shadow-sm border border-gray-50">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-2 uppercase tracking-tighter">Belum Ada Pesanan</h4>
            <p class="text-gray-400 max-w-sm mx-auto mb-8">Anda belum memiliki riwayat pesanan. Yuk mulai belanja sekarang!</p>
            <a href="{{ route('home') }}" class="px-10 py-4 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-500 transition shadow-lg text-sm">
                Mulai Belanja
            </a>
        </div>
        @endforelse
    </main>

    @include('layouts.footer')

</body>
</html>
