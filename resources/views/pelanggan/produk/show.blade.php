<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $produk->nama_produk }} - Mini Workshop</title>
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

    <!-- Navigation (Same as Dashboard) -->
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
                <a href="{{ route('pesanan.saya') }}" class="hover:text-yellow-600 transition">pesanan</a>
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <a href="{{ route('keranjang.index') }}" class="hidden sm:inline-block px-5 py-2.5 bg-[#f5efe6] text-gray-800 font-bold text-xs rounded-lg hover:bg-[#ebdccc] transition">Keranjang ({{ $cartCount }})</a>
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
                            @if(in_array(Auth::user()->role, ['admin', 'kasir']))
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 text-blue-600">Panel Admin</a>
                            @endif
                            <hr class="my-2 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-[13px] font-bold text-gray-800 hover:text-yellow-600 transition">Login</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
        <x-flash-message />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <!-- Left: Image Section -->
            <div class="w-full aspect-[1/1.1] rounded-[2.5rem] overflow-hidden bg-white shadow-sm border border-gray-50">
                @if($produk->gambar)
                    <img src="{{ asset('images/products/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=800&q=80" alt="Default Product" class="w-full h-full object-cover">
                @endif
            </div>

            <!-- Right: Content Section -->
            <div class="flex flex-col h-full py-4">
                <nav class="flex text-xs font-bold uppercase tracking-widest text-gray-400 mb-6 gap-2">
                    <a href="{{ route('home') }}" class="hover:text-gray-600 transition">Home</a>
                    <span>/</span>
                    <span class="text-gray-900">{{ $produk->kategori->nama_kategori }}</span>
                </nav>

                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight leading-tight">
                    {{ $produk->nama_produk }}
                </h1>

                <div class="flex items-center gap-3 mb-8">
                    <span class="px-4 py-1.5 bg-gray-100 text-gray-900 text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $produk->size }}
                    </span>
                    @if($produk->stok > 0)
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase tracking-wider">
                            Tersedia
                        </span>
                    @else
                        <span class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] font-black rounded-lg uppercase tracking-wider">
                            Habis
                        </span>
                    @endif
                </div>

                <div class="mb-10">
                    <div class="text-3xl font-black text-gray-900 mb-2">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-widest">Harga belum termasuk ongkir</p>
                </div>

                <div class="prose prose-sm max-w-none text-gray-600 mb-12 leading-relaxed">
                    <p>{{ $produk->deskripsi }}</p>
                </div>

                <!-- Buttons side by side as in image -->
                <div class="mt-auto grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ offlineOpen: false }">
                    <form action="{{ route('keranjang.add', $produk->id_produk) }}" method="POST">
                        @csrf
                        @if($produk->stok > 0)
                        <button type="submit" class="w-full py-4 bg-yellow-400 text-gray-900 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-yellow-500 transition shadow-lg flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Tambah ke Keranjang
                        </button>
                        @else
                        <button type="button" disabled class="w-full py-4 bg-gray-100 text-gray-400 font-black text-xs uppercase tracking-widest rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Stok Habis
                        </button>
                        @endif
                    </form>
                    <form action="{{ route('buy-now', $produk->id_produk) }}" method="POST">
                        @csrf
                        @if($produk->stok > 0)
                        <button type="submit" class="w-full py-4 bg-yellow-400 text-gray-900 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-yellow-500 transition shadow-lg">
                            Beli Sekarang
                        </button>
                        @else
                        <button type="button" disabled class="w-full py-4 bg-gray-100 text-gray-400 font-black text-xs uppercase tracking-widest rounded-xl cursor-not-allowed">
                            Stok Habis
                        </button>
                        @endif
                    </form>

                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'kasir']))
                    <div class="sm:col-span-2 mt-4">
                        <button @click="offlineOpen = true" class="w-full py-4 bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-blue-700 transition shadow-lg flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Transaksi Offline (Staf)
                        </button>
                    </div>

                    <!-- Offline Sale Modal -->
                    <div x-show="offlineOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                        <div @click.away="offlineOpen = false" class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Transaksi Offline</h3>
                            <p class="text-sm text-gray-500 mb-6">Produk: <span class="font-bold text-gray-900">{{ $produk->nama_produk }}</span></p>
                            
                            <form action="{{ route('admin.offline-transaction.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Jumlah Pembelian</label>
                                    <input type="number" name="qty" value="1" min="1" max="{{ $produk->stok }}" required
                                           class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" required class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-400">
                                        <option value="cash">Cash</option>
                                        <option value="transfer bank">Transfer Bank</option>
                                        <option value="qris">QRIS</option>
                                        <option value="debit">Debit</option>
                                    </select>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="offlineOpen = false" class="flex-1 py-4 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest">Batal</button>
                                    <button type="submit" class="flex-1 py-4 bg-blue-600 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-200 uppercase tracking-widest">Proses</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Shipping Info -->
                <div class="mt-12 p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center gap-5">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-black text-gray-900 uppercase tracking-tighter mb-0.5">Pengiriman Cepat</div>
                        <p class="text-[11px] text-gray-400 font-medium">Estimasi 2-3 hari kerja untuk wilayah Sumatra.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

</body>
</html>
