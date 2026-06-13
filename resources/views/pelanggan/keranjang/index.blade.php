<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Saya - Mini Workshop</title>
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
                <a href="{{ route('pesanan.saya') }}" class="hover:text-yellow-600 transition">pesanan</a>
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <a href="{{ route('keranjang.index') }}" class="px-5 py-2.5 bg-yellow-400 text-gray-900 font-bold text-xs rounded-lg shadow-sm transition">Keranjang ({{ $cartCount }})</a>
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-12" x-data="{
        selectedItems: [],
        items: {{ json_encode($items->map(function($item) {
            return [
                'id' => (string)$item->id_keranjang_detail,
                'price' => (int)($item->produk->harga_akhir ?? $item->harga),
                'qty' => (int)$item->qty,
                'available' => $item->produk->stok > 0
            ];
        })) }},
        
        toggleSelectAll() {
            const availableItems = this.items.filter(i => i.available).map(i => i.id);
            if (this.selectedItems.length === availableItems.length && availableItems.length > 0) {
                this.selectedItems = [];
            } else {
                this.selectedItems = availableItems;
            }
        },

        get total() {
            let sum = 0;
            this.items.forEach(item => {
                if (this.selectedItems.includes(item.id)) {
                    sum += item.price * item.qty;
                }
            });
            return sum;
        },

        formatPrice(price) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price).replace('IDR', 'Rp');
        }
    }">
        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-8">Keranjang Belanja</h2>
        <x-flash-message />

        @if(count($items) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Items List -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Select All Bar -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-50 flex items-center gap-4 mb-4">
                    <input type="checkbox" @click="toggleSelectAll()" :checked="selectedItems.length === items.filter(i => i.available).length && items.filter(i => i.available).length > 0" class="w-5 h-5 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                    <span class="text-sm font-bold text-gray-700">Pilih Semua (<span x-text="items.filter(i => i.available).length"></span>/{{ count($items) }})</span>
                </div>

                @foreach($items as $item)
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-50 flex gap-4 md:gap-6 items-center {{ $item->produk->stok <= 0 ? 'opacity-60 bg-gray-50/50' : '' }}">
                    @if($item->produk->stok > 0)
                        <input type="checkbox" x-model="selectedItems" value="{{ $item->id_keranjang_detail }}" class="w-5 h-5 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                    @else
                        <input type="checkbox" disabled class="w-5 h-5 rounded border-gray-200 bg-gray-100 cursor-not-allowed">
                    @endif
                    
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl overflow-hidden shrink-0">
                        @if($item->produk->gambar_url)
                            <img src="{{ $item->produk->gambar_url }}" alt="{{ $item->produk->nama_produk }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=400&q=80" alt="Default Product" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 truncate">{{ $item->produk->kategori->nama_kategori }} | {{ $item->produk->size }}</div>
                        <h3 class="font-bold text-gray-900 mb-1 truncate text-sm md:text-base">{{ $item->produk->nama_produk }}</h3>
                        <div class="text-sm font-black text-gray-900">Rp {{ number_format($item->produk->harga_akhir ?? $item->harga, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4">
                        @if($item->produk->stok > 0)
                        <div class="flex items-center bg-gray-50 rounded-xl px-2">
                            <button class="p-2 text-gray-400 hover:text-gray-900">-</button>
                            <span class="px-2 font-bold text-sm">{{ $item->qty }}</span>
                            <button class="p-2 text-gray-400 hover:text-gray-900">+</button>
                        </div>
                        @else
                        <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-xl whitespace-nowrap">
                            Stok Habis
                        </span>
                        @endif
                        <form action="{{ route('keranjang.remove', $item->id_keranjang_detail) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:text-red-600 transition" onclick="return confirm('Hapus produk ini dari keranjang?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Right: Summary -->
            <div class="lg:col-span-1">
                <form action="{{ route('checkout.index') }}" method="GET">
                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="cart_item_ids[]" :value="id">
                    </template>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50 sticky top-32">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 uppercase tracking-tighter">Ringkasan Belanja</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-sm font-medium text-gray-600">
                                <span>Subtotal (<span x-text="selectedItems.length"></span> Produk)</span>
                                <span x-text="formatPrice(total)"></span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-600">
                                <span>Pengiriman</span>
                                <span class="text-green-600 uppercase font-black text-[10px]">Dihitung nanti</span>
                            </div>
                            <hr class="border-gray-100">
                            <div class="flex justify-between text-lg font-black text-gray-900 uppercase tracking-tighter">
                                <span>Total</span>
                                <span class="text-yellow-600" x-text="formatPrice(total)"></span>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            :disabled="selectedItems.length === 0"
                            class="w-full py-4 bg-yellow-400 text-gray-900 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-yellow-500 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Checkout (<span x-text="selectedItems.length"></span>)
                        </button>
                        <a href="{{ route('home') }}" class="block w-full text-center mt-4 text-[11px] font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-widest">
                            Lanjut Belanja
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="py-32 text-center bg-white rounded-[3.5rem] shadow-sm border border-gray-50">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-2 uppercase tracking-tighter">Keranjang Kosong</h4>
            <p class="text-gray-400 max-w-sm mx-auto mb-8">Wah, keranjangmu masih kosong nih. Yuk cari barang preloved favoritmu!</p>
            <a href="{{ route('home') }}" class="px-10 py-4 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg text-sm">
                Mulai Belanja
            </a>
        </div>
        @endif
    </div>

    @include('layouts.footer')

</body>
</html>
