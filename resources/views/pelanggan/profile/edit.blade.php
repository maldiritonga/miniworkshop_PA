<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Mini Workshop</title>
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
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Beranda</a>
                <a href="{{ route('pesanan.saya') }}" class="hover:text-yellow-600 transition">Pesanan</a>
                <a href="{{ route('profile.edit') }}" class="text-yellow-600">Profil</a>
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

    <main class="max-w-4xl mx-auto px-6 md:px-12 py-12" x-data="{
        modalAlamat: false,
        modalEdit: false,
        editId: null,
        editLabel: '',
        editNama: '',
        editHp: '',

        // --- state untuk form TAMBAH ---
        addProvinces: [], addRegencies: [], addDistricts: [], addVillages: [],
        addProvince: '', addRegency: '', addDistrict: '', addVillage: '',
        addDetail: '', addFull: '',

        // --- state untuk form EDIT ---
        editProvinces: [], editRegencies: [], editDistricts: [], editVillages: [],
        editProvince: '', editRegency: '', editDistrict: '', editVillage: '',
        editDetail: '', editFull: '',

        async initProvinces() {
            if (this.addProvinces.length) return;
            const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            this.addProvinces = await res.json();
            this.editProvinces = this.addProvinces;
        },

        // ADD
        async addLoadRegencies() {
            this.addRegencies = []; this.addDistricts = []; this.addVillages = [];
            this.addRegency = ''; this.addDistrict = ''; this.addVillage = '';
            if (!this.addProvince) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.addProvince}.json`);
            this.addRegencies = await res.json();
            this.buildAddFull();
        },
        async addLoadDistricts() {
            this.addDistricts = []; this.addVillages = [];
            this.addDistrict = ''; this.addVillage = '';
            if (!this.addRegency) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.addRegency}.json`);
            this.addDistricts = await res.json();
            this.buildAddFull();
        },
        async addLoadVillages() {
            this.addVillages = []; this.addVillage = '';
            if (!this.addDistrict) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.addDistrict}.json`);
            this.addVillages = await res.json();
            this.buildAddFull();
        },
        buildAddFull() {
            const p = this.addProvinces.find(x => x.id === this.addProvince)?.name || '';
            const r = this.addRegencies.find(x => x.id === this.addRegency)?.name || '';
            const d = this.addDistricts.find(x => x.id === this.addDistrict)?.name || '';
            const v = this.addVillages.find(x => x.id === this.addVillage)?.name || '';
            let parts = [];
            if (this.addDetail) parts.push(this.addDetail);
            if (v) parts.push(v);
            if (d) parts.push(d);
            if (r) parts.push(r);
            if (p) parts.push(p);
            this.addFull = parts.join(', ');
        },

        // EDIT
        async editLoadRegencies() {
            this.editRegencies = []; this.editDistricts = []; this.editVillages = [];
            this.editRegency = ''; this.editDistrict = ''; this.editVillage = '';
            if (!this.editProvince) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.editProvince}.json`);
            this.editRegencies = await res.json();
            this.buildEditFull();
        },
        async editLoadDistricts() {
            this.editDistricts = []; this.editVillages = [];
            this.editDistrict = ''; this.editVillage = '';
            if (!this.editRegency) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.editRegency}.json`);
            this.editDistricts = await res.json();
            this.buildEditFull();
        },
        async editLoadVillages() {
            this.editVillages = []; this.editVillage = '';
            if (!this.editDistrict) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.editDistrict}.json`);
            this.editVillages = await res.json();
            this.buildEditFull();
        },
        buildEditFull() {
            const p = this.editProvinces.find(x => x.id === this.editProvince)?.name || '';
            const r = this.editRegencies.find(x => x.id === this.editRegency)?.name || '';
            const d = this.editDistricts.find(x => x.id === this.editDistrict)?.name || '';
            const v = this.editVillages.find(x => x.id === this.editVillage)?.name || '';
            let parts = [];
            if (this.editDetail) parts.push(this.editDetail);
            if (v) parts.push(v);
            if (d) parts.push(d);
            if (r) parts.push(r);
            if (p) parts.push(p);
            this.editFull = parts.join(', ');
        },

        openEdit(id, label, nama, hp) {
            this.editId = id;
            this.editLabel = label;
            this.editNama = nama;
            this.editHp = hp;
            this.editProvince = ''; this.editRegency = ''; this.editDistrict = ''; this.editVillage = '';
            this.editRegencies = []; this.editDistricts = []; this.editVillages = [];
            this.editDetail = ''; this.editFull = '';
            this.initProvinces();
            this.modalEdit = true;
        },

        openAdd() {
            this.addProvince = ''; this.addRegency = ''; this.addDistrict = ''; this.addVillage = '';
            this.addRegencies = []; this.addDistricts = []; this.addVillages = [];
            this.addDetail = ''; this.addFull = '';
            this.initProvinces();
            this.modalAlamat = true;
        }
    }">

        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-8">Profil Saya</h2>

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

            {{-- Kiri: Info Akun --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Avatar & Info --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 text-center">
                    <div x-data="{ openMenu: false }" class="relative w-20 h-20 mx-auto mb-4 group">

                        {{-- Foto Profil --}}
                        <button @click="openMenu = !openMenu" type="button" class="w-20 h-20 rounded-full overflow-hidden bg-yellow-100 border-2 border-gray-100 relative focus:outline-none">
                            @if($user->foto_profil)
                                <img src="{{ asset('images/profil/' . $user->foto_profil) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=fef08a&color=713f12&size=80&bold=true"
                                    class="w-full h-full object-cover">
                            @endif
                            
                            {{-- Overlay kamera saat hover --}}
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="openMenu" x-cloak @click.away="openMenu = false" 
                            class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-100">
                            <label for="foto-profil-input" @click="openMenu = false" class="block w-full text-left px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">
                                Ganti foto profil
                            </label>
                            @if($user->foto_profil)
                                <form action="{{ route('profile.foto.delete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus foto profil ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Hapus foto profil
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-2">
                        <div class="text-base font-black text-gray-900">{{ $user->nama }}</div>
                        <button onclick="document.getElementById('input-nama').focus(); document.getElementById('input-nama').select();" class="text-gray-400 hover:text-yellow-600 transition" title="Ganti Nama">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $user->email }}</div>
                    <div class="mt-2 inline-block px-3 py-1 bg-yellow-50 text-yellow-700 text-[10px] font-black uppercase tracking-widest rounded-full">
                        {{ ucfirst($user->role) }}
                    </div>

                    {{-- Form upload foto (submit otomatis saat file dipilih) --}}
                    <form id="form-foto-profil" action="{{ route('profile.foto') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <input type="file" id="foto-profil-input" name="foto_profil"
                            accept="image/jpg,image/jpeg,image/png,image/webp"
                            class="hidden"
                            onchange="if(this.files[0]) { this.form.submit(); }">
                        <p class="text-[10px] text-gray-400">Klik foto untuk membuka menu</p>
                        <p class="text-[9px] text-gray-300 mt-0.5">JPG, PNG, WebP · Maks 2MB</p>
                    </form>
                </div>

                {{-- Update Info Akun --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-5">Informasi Akun</h3>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input type="text" id="input-nama" name="nama" value="{{ old('nama', $user->nama) }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-colors duration-300">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <button type="submit"
                            class="w-full py-3 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                {{-- Ganti Password --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-5">Ganti Password</h3>
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>

                        <button type="submit"
                            class="w-full py-3 bg-yellow-400 text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-yellow-500 transition">
                            Ganti Password
                        </button>
                    </form>
                </div>

            </div>

            {{-- Kanan: Alamat --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Alamat Pengiriman</h3>
                            <p class="text-[11px] text-gray-400 mt-0.5">Maksimal 3 alamat. Alamat utama otomatis terisi saat checkout.</p>
                        </div>
                        @if($alamats->count() < 3)
                        <button @click="openAdd()"
                            class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-yellow-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Alamat
                        </button>
                        @endif
                    </div>

                    @if($alamats->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm font-bold text-gray-400">Belum ada alamat tersimpan.</p>
                        <p class="text-[11px] text-gray-300 mt-1">Tambahkan alamat untuk mempercepat proses checkout.</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($alamats as $alamat)
                        <div class="relative border-2 rounded-2xl p-5 transition
                            {{ $alamat->is_utama ? 'border-yellow-400 bg-yellow-50/50' : 'border-gray-100 hover:border-gray-200' }}">

                            {{-- Badge Utama --}}
                            @if($alamat->is_utama)
                            <div class="absolute -top-3 left-4">
                                <span class="px-3 py-1 bg-yellow-400 text-gray-900 text-[9px] font-black uppercase tracking-widest rounded-full shadow-sm">
                                    ★ Utama
                                </span>
                            </div>
                            @endif

                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                            {{ $alamat->label }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">{{ $alamat->nama_penerima }}</div>
                                    <div class="text-[12px] text-gray-500 mt-0.5">{{ $alamat->no_hp }}</div>
                                    <div class="text-[12px] text-gray-600 mt-1.5 leading-relaxed">{{ $alamat->alamat_lengkap }}</div>
                                </div>

                                {{-- Aksi --}}
                                <div class="flex flex-col gap-2 shrink-0">
                                    <button @click="openEdit({{ $alamat->id_alamat }}, '{{ addslashes($alamat->label) }}', '{{ addslashes($alamat->nama_penerima) }}', '{{ addslashes($alamat->no_hp) }}')"
                                        class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                                        Edit
                                    </button>
                                    @if(!$alamat->is_utama)
                                    <form action="{{ route('alamat.utama', $alamat->id_alamat) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-yellow-700 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition">
                                            Utamakan
                                        </button>
                                    </form>
                                    <form action="{{ route('alamat.destroy', $alamat->id_alamat) }}" method="POST"
                                        onsubmit="return confirm('Hapus alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-red-500 bg-red-50 rounded-xl hover:bg-red-100 transition">
                                            Hapus
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('alamat.destroy', $alamat->id_alamat) }}" method="POST"
                                        onsubmit="return confirm('Hapus alamat utama ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-red-500 bg-red-50 rounded-xl hover:bg-red-100 transition">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Tambah Alamat --}}
        <div x-show="modalAlamat" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="modalAlamat = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-black text-gray-900 mb-1">Tambah Alamat Baru</h3>
                <p class="text-xs text-gray-400 mb-6">Isi informasi alamat pengiriman Anda.</p>

                <form action="{{ route('alamat.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Label & No HP --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Label <span class="text-red-500">*</span></label>
                            <select name="label" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Kos">Kos</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. HP <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    {{-- Nama Penerima --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $user->nama) }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                            placeholder="Nama lengkap penerima">
                    </div>

                    {{-- Dropdown Wilayah --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                            <select x-model="addProvince" @change="addLoadRegencies()"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                <option value="">Pilih Provinsi</option>
                                <template x-for="item in addProvinces" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kabupaten/Kota <span class="text-red-500">*</span></label>
                            <select x-model="addRegency" @change="addLoadDistricts()" :disabled="!addProvince"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kab/Kota</option>
                                <template x-for="item in addRegencies" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                            <select x-model="addDistrict" @change="addLoadVillages()" :disabled="!addRegency"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kecamatan</option>
                                <template x-for="item in addDistricts" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kelurahan/Desa <span class="text-red-500">*</span></label>
                            <select x-model="addVillage" @change="buildAddFull()" :disabled="!addDistrict"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kelurahan</option>
                                <template x-for="item in addVillages" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Detail Alamat --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Detail Alamat <span class="text-red-500">*</span></label>
                        <textarea x-model="addDetail" @input="buildAddFull()" rows="2" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"
                            placeholder="Nama jalan, nomor rumah, RT/RW, kode pos, dll"></textarea>
                    </div>

                    {{-- Preview Alamat Lengkap --}}
                    <div x-show="addFull" class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-1">Preview Alamat</p>
                        <p class="text-xs text-gray-700 leading-relaxed" x-text="addFull"></p>
                    </div>

                    {{-- Hidden input alamat_lengkap --}}
                    <input type="hidden" name="alamat_lengkap" x-bind:value="addFull">

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalAlamat = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-yellow-400 text-gray-900 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-yellow-500 transition shadow-sm">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Alamat --}}
        <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="modalEdit = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-black text-gray-900 mb-1">Edit Alamat</h3>
                <p class="text-xs text-gray-400 mb-6">Perbarui informasi alamat pengiriman.</p>

                <form :action="'/alamat/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Label & No HP --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Label <span class="text-red-500">*</span></label>
                            <select name="label" x-model="editLabel"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Kos">Kos</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. HP <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" x-model="editHp"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Nama Penerima --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_penerima" x-model="editNama"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                    </div>

                    {{-- Dropdown Wilayah --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                            <select x-model="editProvince" @change="editLoadRegencies()"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                <option value="">Pilih Provinsi</option>
                                <template x-for="item in editProvinces" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kabupaten/Kota <span class="text-red-500">*</span></label>
                            <select x-model="editRegency" @change="editLoadDistricts()" :disabled="!editProvince"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kab/Kota</option>
                                <template x-for="item in editRegencies" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                            <select x-model="editDistrict" @change="editLoadVillages()" :disabled="!editRegency"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kecamatan</option>
                                <template x-for="item in editDistricts" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kelurahan/Desa <span class="text-red-500">*</span></label>
                            <select x-model="editVillage" @change="buildEditFull()" :disabled="!editDistrict"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:opacity-50">
                                <option value="">Pilih Kelurahan</option>
                                <template x-for="item in editVillages" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Detail Alamat --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Detail Alamat <span class="text-red-500">*</span></label>
                        <textarea x-model="editDetail" @input="buildEditFull()" rows="2" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"
                            placeholder="Nama jalan, nomor rumah, RT/RW, kode pos, dll"></textarea>
                    </div>

                    {{-- Preview Alamat Lengkap --}}
                    <div x-show="editFull" class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-1">Preview Alamat</p>
                        <p class="text-xs text-gray-700 leading-relaxed" x-text="editFull"></p>
                    </div>

                    {{-- Hidden input alamat_lengkap --}}
                    <input type="hidden" name="alamat_lengkap" x-bind:value="editFull">

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalEdit = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-yellow-400 text-gray-900 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-yellow-500 transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    @include('layouts.footer')

</body>
</html>
