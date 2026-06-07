<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - MINI WORKSHOP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F9F9F9] text-gray-900" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 transform lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex flex-col h-full p-6">
                <!-- User Profile -->
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 mb-10 px-2 group">
                    <div class="h-12 w-12 rounded-full flex items-center justify-center bg-gray-900 text-yellow-400 font-bold text-lg overflow-hidden shrink-0 border border-gray-100">
                        @if(Auth::user()->foto_profil)
                            <img src="{{ asset('images/profil/' . Auth::user()->foto_profil) }}" alt="User Profile" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=random" alt="User Profile" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-[15px] text-gray-900 leading-tight truncate group-hover:text-blue-600 transition">{{ Auth::user()->nama }}</h2>
                        <p class="text-[11px] font-black text-blue-600 uppercase tracking-tighter">{{ Auth::user()->role }}</p>
                    </div>
                </a>

                <!-- Navigation Sidebar -->
                <nav class="flex-1 space-y-1">
                    <x-admin-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="home">
                        Dashboard
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.produk.index') }}" :active="request()->routeIs('admin.produk.*')" icon="box">
                        Produk
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.offline-transaction.index') }}" :active="request()->routeIs('admin.offline-transaction.*')" icon="clipboard">
                        Transaksi Offline
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.pesanan.index') }}" :active="request()->routeIs('admin.pesanan.*')" icon="clipboard">
                        Pesanan
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.retur.index') }}" :active="request()->routeIs('admin.retur.*')" icon="refresh">
                        Retur
                    </x-admin-nav-link>

                    {{-- Menu eksklusif Admin --}}
                    @if(Auth::user()->role === 'admin')

                        <x-admin-nav-link href="{{ route('admin.laporan.index') }}" :active="request()->routeIs('admin.laporan.*')" icon="chart-bar">
                            Laporan
                        </x-admin-nav-link>

                        <div class="py-4 px-4">
                            <hr class="border-gray-100">
                        </div>

                        <x-admin-nav-link href="{{ route('admin.akun-kasir.index') }}" :active="request()->routeIs('admin.akun-kasir.*')" icon="settings">
                            Mengelola Akun Kasir
                        </x-admin-nav-link>

                        <x-admin-nav-link href="{{ route('admin.akun-pelanggan.index') }}" :active="request()->routeIs('admin.akun-pelanggan.*')" icon="user-group">
                            Mengelola Akun Pelanggan
                        </x-admin-nav-link>

                    @endif
                </nav>

                <!-- Bottom Menu -->
                <div class="mt-auto pt-10 space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-4 w-full px-4 py-3.5 text-[14px] font-bold text-blue-600 hover:bg-blue-50 rounded-xl transition group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Website
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-4 w-full px-4 py-3.5 text-[14px] font-bold text-gray-500 hover:text-red-600 transition group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-[#F9F9F9] overflow-y-auto">
            <!-- Mobile Header -->
            <header class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-gray-100">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
                <span class="font-bold text-gray-900 leading-none">Mini Workshop</span>
                <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden border border-gray-200 block shrink-0">
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset('images/profil/' . Auth::user()->foto_profil) }}" alt="User Profile" class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=random" alt="User Profile" class="w-full h-full object-cover">
                    @endif
                </a>
            </header>

            @php $totalStokGabungan = \App\Models\Produk::aktif()->sum('stok'); @endphp
            @if($totalStokGabungan < \App\Models\Produk::STOK_BATAS_RENDAH)
                <x-admin.stok-top-bar :total-stok-gabungan="$totalStokGabungan" />
            @endif

            <div class="px-6 py-8 sm:px-10 lg:px-16 w-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-cloak
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
</body>
</html>
