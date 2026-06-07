<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Mini Workshop</title>
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

            <!-- Middle: Menus -->
            <div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Dasboard</a>
                <a href="{{ route('home') }}#catalog" class="hover:text-yellow-600 transition">katalog</a>
                <a href="{{ route('about') }}" class="text-yellow-600 transition">About</a>
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
    <div class="w-full py-6 bg-white min-h-screen pb-12">
        <!-- Hero Image Slider Section (Matching Dashboard Size) -->
        <div class="px-6 md:px-12 mb-12">
            <div x-data="{ 
                activeSlide: 1, 
                slides: [1, 2, 3],
                init() {
                    setInterval(() => {
                        this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1;
                    }, 4000);
                }
            }" class="relative w-full h-[500px] md:h-[650px] rounded-[2.5rem] overflow-hidden group bg-gray-200 shadow-sm">
                
                <!-- Slides -->
                <div class="flex h-full transition-transform duration-700 ease-in-out" 
                     :style="'transform: translateX(-' + ((activeSlide - 1) * 100) + '%)'">
                    
                    <div class="min-w-full h-full relative">
                        <img src="{{ asset('images/G1.jpeg') }}" class="w-full h-full object-cover object-center" alt="Galeri Mini Workshop 1">
                        <div class="absolute inset-0 bg-black/20"></div>
                    </div>
                    <div class="min-w-full h-full relative">
                        <img src="{{ asset('images/G2.jpeg') }}" class="w-full h-full object-cover object-center" alt="Galeri Mini Workshop 2">
                        <div class="absolute inset-0 bg-black/20"></div>
                    </div>
                    <div class="min-w-full h-full relative">
                        <img src="{{ asset('images/G3.jpeg') }}" class="w-full h-full object-cover object-center" alt="Galeri Mini Workshop 3">
                        <div class="absolute inset-0 bg-black/20"></div>
                    </div>

                </div>

                <!-- Overlay Text on Slider -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 pointer-events-none">
                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">
                        Tentang <span class="text-yellow-400">Mini Workshop</span>
                    </h1>
                    <p class="text-base md:text-lg text-gray-100 max-w-3xl mx-auto drop-shadow-md font-medium">
                        Pilihan fashion terjangkau, berkualitas, dan tetap stylish untuk Anda.
                    </p>
                </div>
                
                <!-- Previous Button -->
                <button @click="activeSlide = activeSlide === 1 ? slides.length : activeSlide - 1" 
                        class="absolute left-6 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-md text-white p-4 rounded-full hover:bg-white/40 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100 shadow-xl transform hover:scale-105">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                
                <!-- Next Button -->
                <button @click="activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1" 
                        class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-md text-white p-4 rounded-full hover:bg-white/40 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100 shadow-xl transform hover:scale-105">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex space-x-3">
                    <template x-for="slide in slides" :key="slide">
                        <button @click="activeSlide = slide" 
                                :class="{'w-10 bg-yellow-400': activeSlide === slide, 'w-3 bg-white/60 hover:bg-white/90': activeSlide !== slide}" 
                                class="h-3 rounded-full transition-all duration-500 shadow-md"></button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Text Content Section (Centered max-width) -->
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-12 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16 items-start">
                <div class="prose prose-lg md:prose-xl max-w-none text-gray-700 leading-relaxed">
                    <p class="first-letter:text-6xl md:first-letter:text-7xl first-letter:font-black first-letter:text-yellow-500 first-letter:mr-3 first-letter:float-left first-line:tracking-widest first-line:uppercase text-justify">
                        Mini Workshop merupakan usaha yang bergerak di bidang penjualan fashion preloved seperti pakaian dan sepatu dengan kualitas yang masih layak pakai dan mengikuti tren fashion masa kini. Mini Workshop hadir untuk memberikan pilihan fashion yang terjangkau, berkualitas, dan tetap stylish bagi pelanggan.
                    </p>
                    <p class="text-justify mt-6">
                        Sejak awal berdiri, Mini Workshop memanfaatkan media sosial sebagai sarana promosi dan penjualan produk kepada pelanggan. Dengan meningkatnya minat dan jumlah pelanggan, Mini Workshop terus berupaya meningkatkan kualitas pelayanan serta kemudahan dalam proses pemesanan produk.
                    </p>
                </div>
                
                <div class="prose prose-lg md:prose-xl max-w-none text-gray-700 leading-relaxed">
                    <p class="text-justify">
                        Melalui website ini, Mini Workshop menyediakan layanan pembelian produk secara online yang memudahkan pelanggan dalam melihat katalog produk, memperoleh informasi detail produk, serta melakukan pemesanan dengan lebih praktis dan terstruktur.
                    </p>
                    
                    <div class="bg-[#f5efe6] border-l-4 border-yellow-400 p-6 md:p-8 rounded-2xl mt-8 shadow-sm relative overflow-hidden group">
                        <!-- Decorative SVG Background -->
                        <svg class="absolute -right-8 -top-8 w-32 h-32 text-yellow-600/10 transform group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                        
                        <p class="font-medium text-gray-900 text-lg md:text-xl italic m-0 relative z-10 leading-snug">
                            "Mini Workshop berkomitmen untuk terus memberikan pelayanan terbaik, menjaga kualitas produk, serta menghadirkan pengalaman berbelanja fashion preloved yang mudah, aman, dan nyaman bagi pelanggan."
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 md:mt-20 text-center">
                <a href="{{ route('home') }}#catalog" class="inline-flex items-center justify-center px-8 py-4 md:px-10 md:py-4 text-base md:text-lg font-bold text-gray-900 bg-yellow-400 rounded-full hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 transition-all duration-300 shadow-xl hover:shadow-yellow-500/40 transform hover:-translate-y-1">
                    <svg class="w-6 h-6 md:w-7 md:h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Eksplor Katalog Kami
                </a>
            </div>
        </div>
    </div>

    @include('layouts.footer')

</body>
</html>
