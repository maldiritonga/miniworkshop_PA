<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retur Saya - Mini Workshop</title>
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

    <main class="max-w-5xl mx-auto px-6 md:px-12 py-12" x-data="{ returOpen: false, returProdukId: '', returProdukNama: '', returPesananId: '' }">
        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-10">Retur Saya</h2>
        <x-flash-message />

        <!-- Bagian 1: Pesanan Selesai (Bisa Diretur) -->
        <div class="mb-12">
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter mb-6">Pesanan Selesai (Bisa Diretur)</h3>
            @forelse($pesanans as $pesanan)
                @if($pesanan->updated_at->copy()->addDays(7)->isFuture())
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 mb-6">
                        <div class="flex justify-between items-center border-b border-gray-50 pb-4 mb-4">
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ID Pesanan: #{{ $pesanan->id_pesanan }}</div>
                                <div class="text-xs font-bold text-gray-600">Selesai pada: {{ $pesanan->updated_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach($pesanan->detail as $item)
                                @php
                                    $returItem = $pesanan->retur->firstWhere('id_produk', $item->id_produk);
                                @endphp
                                <div class="flex items-center gap-4 border border-gray-50 p-4 rounded-2xl">
                                    <img src="{{ $item->produk->gambar_url ?? 'https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=100&q=80' }}" class="w-14 h-14 object-cover rounded-xl shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate">{{ $item->produk->nama_produk }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $item->qty }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        @if($returItem)
                                            <a href="{{ route('retur.show', $returItem->id_retur) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-100 transition inline-block">Lihat Retur</a>
                                        @else
                                            <button @click="returOpen = true; returProdukId = '{{ $item->id_produk }}'; returProdukNama = '{{ addslashes($item->produk->nama_produk) }}'; returPesananId = '{{ $pesanan->id_pesanan }}'" class="px-4 py-2 bg-red-50 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition whitespace-nowrap">Ajukan Retur</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-6 text-center text-gray-500 bg-white rounded-2xl border border-gray-100">Belum ada pesanan selesai yang bisa diretur.</div>
            @endforelse
        </div>

        <!-- Bagian 2: Riwayat Retur -->
        <div>
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter mb-6">Riwayat Retur</h3>
            @forelse($returs as $retur)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-50 mb-6 hover:shadow-md transition duration-300">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gray-50 rounded-2xl text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">ID Retur: #{{ $retur->id_retur }}</div>
                                <div class="text-xs font-bold text-gray-600">ID Pesanan: #{{ $retur->id_pesanan }} - {{ $retur->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        <div>
                            @php
                                $statusClass = [
                                    'diajukan'           => 'bg-blue-50 text-blue-600',
                                    'disetujui'          => 'bg-indigo-50 text-indigo-600',
                                    'ditolak'            => 'bg-red-50 text-red-600',
                                    'menunggu_rekening'  => 'bg-yellow-50 text-yellow-600',
                                    'menunggu_barang'    => 'bg-orange-50 text-orange-600',
                                    'barang_diterima'    => 'bg-purple-50 text-purple-600',
                                    'uang_ditransfer'    => 'bg-green-50 text-green-600',
                                    'selesai'            => 'bg-emerald-50 text-emerald-600',
                                ][$retur->status_retur] ?? 'bg-gray-50 text-gray-600';
                                $statusLabel = ucfirst(str_replace('_', ' ', $retur->status_retur));
                            @endphp
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="flex-1 w-full">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                    <img src="{{ $retur->produk->gambar_url ?? 'https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=100&q=80' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-900 truncate">{{ $retur->produk->nama_produk ?? 'Produk' }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium">Alasan: {{ Str::limit($retur->alasan_retur, 50) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="md:w-48 w-full md:text-right">
                            <a href="{{ route('retur.show', $retur->id_retur) }}" class="inline-block px-6 py-2.5 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition shadow-sm w-full text-center">Detail Retur</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 bg-white rounded-2xl border border-gray-100">Belum ada riwayat retur.</div>
            @endforelse
        </div>

        {{-- Modal Retur --}}
        <div x-show="returOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="returOpen = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-black text-gray-900 mb-1">Ajukan Retur Produk</h3>
                <p class="text-xs text-gray-400 mb-1">Produk: <span class="font-bold text-gray-900" x-text="returProdukNama"></span></p>
                <p class="text-xs text-gray-400 mb-6">Jelaskan alasan dan sertakan foto bukti kondisi produk.</p>

                <form :action="'{{ url('/pesanan-saya') }}/' + returPesananId + '/retur'" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="id_produk" x-bind:value="returProdukId">

                    {{-- Alasan Retur --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Alasan Retur <span class="text-red-500">*</span></label>
                        <textarea name="alasan_retur" rows="3" required maxlength="500"
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"
                            placeholder="Contoh: Produk tidak sesuai deskripsi, ukuran tidak pas, cacat produk, dll."></textarea>
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
                                this.previews.push(URL.createObjectURL(file));
                            });
                            this.updateInput();
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.previews.splice(index, 1);
                            this.updateInput();
                        },
                        updateInput() {
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fotoInput.files = dt.files;
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Foto Bukti (1-3 Foto) <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <template x-for="(preview, index) in previews" :key="index">
                                <div class="relative w-full aspect-square rounded-xl overflow-hidden border border-gray-200">
                                    <img :src="preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                            <label x-show="previews.length < 3" class="w-full aspect-square flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-red-400 transition bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span class="text-[10px] font-bold text-gray-400">Tambah</span>
                                <input type="file" multiple accept="image/*" class="hidden" @change="addFiles" required x-ref="fotoInput" name="foto_bukti[]">
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="returOpen = false" class="flex-1 py-3 bg-gray-100 text-gray-500 text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-gray-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-red-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-red-600 transition shadow-sm">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('layouts.footer')

</body>
</html>
