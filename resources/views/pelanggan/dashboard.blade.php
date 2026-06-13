<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Workshop - Temukan Kemewahan Preloved</title>
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
            <!-- Left: Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    <h1 class="text-lg font-black uppercase tracking-tighter text-gray-900">MINI WORKSHOP</h1>
                </a>
            </div>

            <!-- Middle: Menus (Visible on all screens above small) -->
            <div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Dasboard</a>
                <a href="#catalog" class="hover:text-yellow-600 transition">katalog</a>
                <a href="{{ route('about') }}" class="hover:text-yellow-600 transition">About</a>
                <a href="{{ route('pesanan.saya') }}" class="hover:text-yellow-600 transition">pesanan</a>
            </div>

            <!-- Right: Actions -->
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

    <!-- Main Content Container -->
    <div class="w-full py-6">
        <div class="px-6 md:px-12">
            <x-flash-message />
        </div>
        
        <!-- Hero Section -->
        <div class="px-6 md:px-12 mb-12">
            <div class="relative w-full h-[500px] md:h-[650px] rounded-[2.5rem] overflow-hidden shadow-sm bg-gray-200">
                <img src="{{ asset('images/fotoD.png') }}" alt="Hero Image" class="absolute inset-0 w-full h-full object-cover object-center" onerror="this.src='https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1600&q=80'">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
                    <h2 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-white mb-4 drop-shadow-lg tracking-tight">Temukan Kemewahan Preloved</h2>
                    <p class="text-base md:text-lg text-gray-100 mb-8 drop-shadow-md font-medium">Kualitas premium dengan harga terjangkau</p>
                    <a href="#catalog" class="px-10 py-4 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-500 transition shadow-lg text-sm md:text-base">
                        Jelajahi Koleksi
                    </a>
                </div>
            </div>
        </div>

        <!-- Section: Barang Baru Masuk -->
        <div class="px-6 md:px-12 mb-16">
            <h3 class="text-xl font-bold text-gray-900 mb-8 ml-2">Barang Baru Masuk</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @forelse($barangBaru as $item)
                <div class="group cursor-pointer">
                    <div class="w-full aspect-[4/5] rounded-[2rem] overflow-hidden bg-white mb-4 shadow-sm group-hover:shadow-md transition-all duration-300 relative">
                        @if($item->gambar_url)
                            <a href="{{ $item->stok > 0 ? route('produk.show', $item->id_produk) : '#' }}">
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $item->stok <= 0 ? 'opacity-50' : '' }}">
                            </a>
                        @else
                            <a href="{{ $item->stok > 0 ? route('produk.show', $item->id_produk) : '#' }}">
                                <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=400&q=80" alt="Default Product" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $item->stok <= 0 ? 'opacity-50' : '' }}">
                            </a>
                        @endif
                        @if($item->stok <= 0)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="bg-black/60 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full">Stok Habis</span>
                        </div>
                        @elseif($item->isDiskonAktif())
                        <div class="absolute top-3 right-3 bg-red-500/95 backdrop-blur-sm text-white px-2.5 py-1 rounded-xl shadow-lg text-right z-10 border border-red-400">
                            <div class="text-[10px] font-black tracking-widest uppercase">Diskon {{ $item->diskon_persen }}%</div>
                            @if($item->diskon_selesai)
                                <div class="text-[8px] font-semibold opacity-90 mt-0.5">s.d. {{ \Carbon\Carbon::parse($item->diskon_selesai)->translatedFormat('d M Y') }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="px-2">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">{{ $item->kategori->nama_kategori }}</div>
                        @if($item->stok > 0)
                        <a href="{{ route('produk.show', $item->id_produk) }}" class="font-bold text-gray-900 text-sm mb-1 truncate block hover:text-yellow-600 transition">
                            {{ $item->nama_produk }}
                        </a>
                        @else
                        <span class="font-bold text-gray-400 text-sm mb-1 truncate block">{{ $item->nama_produk }}</span>
                        @endif
                        <div class="flex justify-between items-center">
                            <div class="text-[11px] font-bold {{ $item->stok > 0 ? 'text-yellow-600/80' : 'text-gray-400' }}">
                                @if($item->isDiskonAktif())
                                    <span class="line-through text-xs font-normal text-gray-400 mr-1">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    Rp {{ number_format($item->harga_akhir, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                @endif
                            </div>
                            @if($item->stok > 0)
                                <span class="text-[9px] px-1.5 py-0.5 bg-green-50 text-green-600 font-bold rounded uppercase tracking-tighter">Tersedia</span>
                            @else
                                <span class="text-[9px] px-1.5 py-0.5 bg-red-50 text-red-600 font-bold rounded uppercase tracking-tighter">Habis</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-10 text-center text-gray-400 font-bold">
                    Tidak ada barang baru dalam 7 hari terakhir.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Section: Katalog (Scroll Target) -->
        <div id="catalog" class="px-6 md:px-12 pt-20 pb-24 bg-white rounded-t-[3.5rem] shadow-inner">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Katalog Produk</h3>
                    <div class="h-1.5 w-20 bg-yellow-400 rounded-full mt-2"></div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route(Route::currentRouteName()) }}#catalog" 
                       class="px-6 py-2 rounded-full text-xs font-bold transition {{ !request()->filled('kategori') ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    @foreach($kategoriList as $cat)
                        <a href="{{ route(Route::currentRouteName(), ['kategori' => $cat->id_kategori]) }}#catalog" 
                           class="px-6 py-2 rounded-full text-xs font-bold transition {{ request('kategori') == $cat->id_kategori ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $cat->nama_kategori }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-10">
                @forelse($katalogProduk as $item)
                <div class="group cursor-pointer">
                    <div class="w-full aspect-[4/5] rounded-[2rem] overflow-hidden bg-white mb-4 shadow-sm group-hover:shadow-md transition-all duration-300 relative">
                        @if($item->gambar_url)
                            <a href="{{ $item->stok > 0 ? route('produk.show', $item->id_produk) : '#' }}">
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $item->stok <= 0 ? 'opacity-50' : '' }}">
                            </a>
                        @else
                            <a href="{{ $item->stok > 0 ? route('produk.show', $item->id_produk) : '#' }}">
                                <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=400&q=80" alt="Default Product" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $item->stok <= 0 ? 'opacity-50' : '' }}">
                            </a>
                        @endif
                        @if($item->stok <= 0)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="bg-black/60 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full">Stok Habis</span>
                        </div>
                        @elseif($item->isDiskonAktif())
                        <div class="absolute top-3 right-3 bg-red-500/95 backdrop-blur-sm text-white px-2.5 py-1 rounded-xl shadow-lg text-right z-10 border border-red-400">
                            <div class="text-[10px] font-black tracking-widest uppercase">Diskon {{ $item->diskon_persen }}%</div>
                            @if($item->diskon_selesai)
                                <div class="text-[8px] font-semibold opacity-90 mt-0.5">s.d. {{ \Carbon\Carbon::parse($item->diskon_selesai)->translatedFormat('d M Y') }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="px-2">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">{{ $item->kategori->nama_kategori }} | {{ $item->size }}</div>
                        @if($item->stok > 0)
                        <a href="{{ route('produk.show', $item->id_produk) }}" class="font-bold text-gray-900 text-sm mb-1 truncate block hover:text-yellow-600 transition">{{ $item->nama_produk }}</a>
                        @else
                        <span class="font-bold text-gray-400 text-sm mb-1 truncate block">{{ $item->nama_produk }}</span>
                        @endif
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex flex-col">
                                @if($item->isDiskonAktif())
                                    <span class="text-xs line-through text-gray-400 font-medium">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    <span class="text-sm font-black text-red-500">Rp {{ number_format($item->harga_akhir, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-sm font-black {{ $item->stok > 0 ? 'text-gray-900' : 'text-gray-400' }}">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            @if($item->stok > 0)
                                <span class="text-[10px] px-2 py-1 bg-green-50 text-green-600 font-bold rounded-md uppercase">Tersedia</span>
                            @else
                                <span class="text-[10px] px-2 py-1 bg-red-50 text-red-600 font-bold rounded-md uppercase">Habis</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-32 text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 mb-2">Katalog Kosong</h4>
                    <p class="text-gray-400 max-w-sm mx-auto">Admin sedang menyiapkan koleksi barang preloved terbaik untuk Anda. Cek kembali nanti!</p>
                </div>
                @endforelse
            </div>

            @if($katalogProduk->hasPages())
            <div class="mt-16 flex justify-center">
                {{ $katalogProduk->links() }}
            </div>
            @endif
        </div>

        @include('layouts.footer')
    </div>

</body>
</html>
