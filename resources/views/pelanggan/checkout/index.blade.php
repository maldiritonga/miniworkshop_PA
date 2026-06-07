<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mini Workshop</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-900 overflow-x-hidden">

    <!-- Header Shopee Style -->
    <nav class="bg-white py-4 px-6 md:px-12 sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    <h1 class="text-xl font-black text-gray-900 tracking-tight">Mini Workshop</h1>
                </a>
                <div class="h-6 w-[2px] bg-gray-200 mx-2 hidden md:block"></div>
                <h2 class="text-lg font-bold text-gray-700 hidden md:block">Checkout</h2>
            </div>
            @auth
            <div class="text-sm font-medium text-gray-600 flex items-center gap-2">
                @if(Auth::user()->foto_profil)
                    <img src="{{ asset('images/profil/' . Auth::user()->foto_profil) }}" class="w-6 h-6 rounded-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=random" class="w-6 h-6 rounded-full">
                @endif
                {{ Auth::user()->nama }}
            </div>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto py-8 px-4 md:px-0" x-data="{
        editAddress: {{ $alamats->isEmpty() ? 'true' : 'false' }},
        addresses: {{ $alamats->toJson() }},
        selectedAddress: {{ $alamatUtama ? $alamatUtama->toJson() : ($alamats->isNotEmpty() ? $alamats->first()->toJson() : 'null') }},
        modalPilihAlamat: false,
        provinces: [],
        regencies: [],
        districts: [],
        villages: [],
        selectedProvince: '',
        selectedRegency: '',
        selectedDistrict: '',
        selectedVillage: '',
        addressDetail: '',
        fullAddress: '',
        shippingCost: 0,
        selectedShipping: '',
        shippingOptions: [],
        isFetchingRates: false,
        totalWeight: {{ $totalWeight ?? 1000 }},

        getCourierLabel(code) {
            const c = (code || '').toString().toLowerCase();
            if (c === 'jnt') return 'J&T';
            if (c === 'jne') return 'JNE';
            if (c === 'sicepat') return 'SiCepat';
            return (code || '').toString().toUpperCase();
        },

        init() {
            this.$watch('selectedAddress', value => {
                if(value && !this.editAddress) this.fetchRates(value.alamat_lengkap);
            });
            this.$watch('editAddress', value => {
                if(!value) {
                    if(this.selectedAddress) this.fetchRates(this.selectedAddress.alamat_lengkap);
                } else {
                    if(this.fullAddress) this.fetchRates(this.fullAddress);
                }
            });

            if(!this.editAddress && this.selectedAddress) {
                this.fetchRates(this.selectedAddress.alamat_lengkap);
            } else if(this.editAddress && this.fullAddress) {
                this.fetchRates(this.fullAddress);
            }

            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(data => this.provinces = data);
        },

        async updateRegencies() {
            this.regencies = [];
            this.districts = [];
            this.villages = [];
            this.selectedRegency = '';
            this.selectedDistrict = '';
            this.selectedVillage = '';
            if (!this.selectedProvince) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.selectedProvince}.json`);
            this.regencies = await res.json();
            this.updateFullAddress();
        },

        async updateDistricts() {
            this.districts = [];
            this.villages = [];
            this.selectedDistrict = '';
            this.selectedVillage = '';
            if (!this.selectedRegency) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.selectedRegency}.json`);
            this.districts = await res.json();
            this.updateFullAddress();
        },

        async updateVillages() {
            this.villages = [];
            this.selectedVillage = '';
            if (!this.selectedDistrict) return;
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.selectedDistrict}.json`);
            this.villages = await res.json();
            this.updateFullAddress();
        },

        updateFullAddress() {
            const p = this.provinces.find(x => x.id === this.selectedProvince)?.name || '';
            const r = this.regencies.find(x => x.id === this.selectedRegency)?.name || '';
            const d = this.districts.find(x => x.id === this.selectedDistrict)?.name || '';
            const v = this.villages.find(x => x.id === this.selectedVillage)?.name || '';
            let parts = [];
            if (this.addressDetail) parts.push(this.addressDetail);
            if (v) parts.push(v);
            if (d) parts.push(d);
            if (r) parts.push(r);
            if (p) parts.push(p);
            this.fullAddress = parts.join(', ');
        },

        async fetchRates(address) {
            if (!address) return;
            this.isFetchingRates = true;
            this.shippingOptions = [];
            this.selectedShipping = '';
            this.shippingCost = 0;

            try {
                const response = await fetch('{{ route("checkout.shipping-rates") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ address: address, weight: this.totalWeight })
                });
                
                const data = await response.json();
                if (data.rates && data.rates.length > 0) {
                    this.shippingOptions = data.rates.map(rate => {
                        const courierCode = (rate.company || '').toString().toLowerCase();
                        const serviceType = (rate.type || '').toString();
                        const price = Number(rate.price) || 0;
                        return {
                            courier_code: courierCode,
                            courier_label: this.getCourierLabel(courierCode),
                            service_type: serviceType,
                            price: price,
                            id: courierCode + '_' + serviceType
                        };
                    });
                    if(this.shippingOptions.length > 0) {
                        this.selectedShipping = this.shippingOptions[0].id;
                        this.shippingCost = this.shippingOptions[0].price;
                    }
                }
            } catch (error) {
                console.error('Error fetching rates:', error);
            } finally {
                this.isFetchingRates = false;
            }
        }
    }">
        <x-flash-message />

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            @if(request()->has('produk_id'))
                <input type="hidden" name="produk_id" value="{{ request()->produk_id }}">
            @endif
            @if(request()->has('cart_item_ids'))
                @foreach(request()->cart_item_ids as $id)
                    <input type="hidden" name="cart_item_ids[]" value="{{ $id }}">
                @endforeach
            @endif

            <!-- 1. Address Section -->
            <div class="bg-white rounded-2xl shadow-sm mb-6 border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 text-gray-900 mb-6 border-b border-gray-100 pb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg">Alamat Pengiriman</h3>
                    </div>

                    {{-- Tampilan alamat tersimpan dari profil --}}
                    @if($alamats->isNotEmpty())
                    <div x-show="!editAddress">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="flex-1">
                                <template x-if="selectedAddress">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-bold text-gray-900 text-lg" x-text="selectedAddress.nama_penerima"></span>
                                            <span class="text-gray-500 text-sm font-medium" x-text="selectedAddress.no_hp"></span>
                                            <span x-show="selectedAddress.is_utama" class="px-2.5 py-1 bg-yellow-400 text-yellow-900 text-[10px] font-black uppercase tracking-widest rounded-md">Utama</span>
                                        </div>
                                        <p class="text-sm text-gray-600 leading-relaxed max-w-2xl" x-text="selectedAddress.alamat_lengkap"></p>

                                        {{-- Hidden inputs untuk form submit --}}
                                        <input type="hidden" name="alamat_pengiriman" :value="selectedAddress.alamat_lengkap" :disabled="editAddress">
                                        <input type="hidden" name="no_hp" :value="selectedAddress.no_hp" :disabled="editAddress">
                                    </div>
                                </template>
                                <template x-if="!selectedAddress">
                                    <p class="text-sm text-gray-500 italic">Belum ada alamat tersimpan.</p>
                                </template>
                            </div>
                            <button type="button" @click="modalPilihAlamat = true"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap">
                                Ubah Alamat
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Form input manual (jika tidak ada alamat tersimpan) --}}
                    <div x-show="editAddress || {{ $alamats->isEmpty() ? 'true' : 'false' }}" class="space-y-6">
                        <input type="hidden" name="alamat_pengiriman" x-bind:value="fullAddress" :disabled="!editAddress">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Nama Penerima</label>
                                <input type="text" value="{{ Auth::user()->nama }}" disabled class="w-full bg-gray-50 border-gray-300 rounded-sm p-2.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Nomor Telepon</label>
                                <input type="text" name="no_hp" value="{{ $noHp ?? Auth::user()->no_hp }}" :disabled="!editAddress" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d]" placeholder="Masukkan nomor telepon aktif">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Provinsi</label>
                                <select x-model="selectedProvince" @change="updateRegencies()" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d]">
                                    <option value="">Pilih Provinsi</option>
                                    <template x-for="item in provinces" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Kabupaten/Kota</label>
                                <select x-model="selectedRegency" @change="updateDistricts()" :disabled="!selectedProvince" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d] disabled:bg-gray-50">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    <template x-for="item in regencies" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Kecamatan</label>
                                <select x-model="selectedDistrict" @change="updateVillages()" :disabled="!selectedRegency" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d] disabled:bg-gray-50">
                                    <option value="">Pilih Kecamatan</option>
                                    <template x-for="item in districts" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Desa/Kelurahan</label>
                                <select x-model="selectedVillage" @change="updateFullAddress()" :disabled="!selectedDistrict" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d] disabled:bg-gray-50">
                                    <option value="">Pilih Desa/Kelurahan</option>
                                    <template x-for="item in villages" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Detail Alamat (Jalan, Gedung, No. Rumah)</label>
                            <textarea x-model="addressDetail" @input="updateFullAddress()" rows="2" class="w-full border-gray-300 rounded-sm p-2.5 text-sm focus:ring-[#ee4d2d] focus:border-[#ee4d2d]" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, dll"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            @if($alamats->isNotEmpty())
                            <button type="button" @click="editAddress = false" class="px-6 py-2.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-50 transition">Batal</button>
                            @endif
                            <button type="button" @click="if(fullAddress) { editAddress = false; savedAddress = fullAddress; fetchRates(fullAddress); } " class="px-6 py-2.5 bg-yellow-400 text-gray-900 font-bold rounded-xl text-sm hover:bg-yellow-500 transition">Konfirmasi</button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Modal Pilih Alamat --}}
            <div x-show="modalPilihAlamat" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                <div @click.away="modalPilihAlamat = false" class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Pilih Alamat Pengiriman</h3>
                        <button @click="modalPilihAlamat = false" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
                        <template x-for="al in addresses" :key="al.id_alamat">
                            <div class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200"
                                :class="selectedAddress && selectedAddress.id_alamat === al.id_alamat ? 'border-yellow-400 bg-yellow-50/50' : 'border-gray-100 hover:border-gray-300 hover:bg-gray-50'"
                                @click="selectedAddress = al; modalPilihAlamat = false;">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] font-bold text-gray-600 bg-white border border-gray-200 px-2.5 py-1 rounded-md" x-text="al.label"></span>
                                    <span x-show="al.is_utama" class="text-[10px] font-black text-yellow-900 bg-yellow-400 px-2.5 py-1 rounded-md uppercase tracking-widest">Utama</span>
                                </div>
                                <div class="font-bold text-sm text-gray-900" x-text="al.nama_penerima"></div>
                                <div class="text-xs text-gray-500" x-text="al.no_hp"></div>
                                <div class="text-xs text-gray-600 mt-1 leading-relaxed" x-text="al.alamat_lengkap"></div>
                            </div>
                        </template>

                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-2 text-gray-900 bg-gray-100 py-3 rounded-xl text-sm font-bold hover:bg-gray-200 mt-4 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Alamat Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Product Summary Section -->
            <div class="bg-white rounded-2xl shadow-sm mb-6 border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-900">Rincian Pesanan</h3>
                </div>
                
                <div class="grid grid-cols-12 px-6 py-4 border-b border-gray-100 text-xs font-black text-gray-400 uppercase tracking-wider bg-gray-50/50">
                    <div class="col-span-6">Produk Dipesan</div>
                    <div class="col-span-2 text-center">Harga Satuan</div>
                    <div class="col-span-2 text-center">Jumlah</div>
                    <div class="col-span-2 text-right">Subtotal Produk</div>
                </div>

                @foreach($items as $item)
                <div class="grid grid-cols-12 px-6 py-5 items-center border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <div class="col-span-6 flex gap-4 items-center">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                            @if($item->produk->gambar)
                                <img src="{{ asset('images/products/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama_produk }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://images.unsplash.com/photo-1515347619252-73da985fa6d5?auto=format&fit=crop&w=100&q=80" alt="Default Product" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $item->produk->nama_produk }}</span>
                            <span class="text-xs font-medium text-gray-500 mt-1 bg-gray-100 w-fit px-2 py-0.5 rounded-md">Variasi: {{ $item->produk->size }}</span>
                        </div>
                    </div>
                    <div class="col-span-2 text-center text-sm font-medium text-gray-600">Rp{{ number_format($item->harga, 0, ',', '.') }}</div>
                    <div class="col-span-2 text-center text-sm font-bold text-gray-900">{{ $item->qty }}</div>
                    <div class="col-span-2 text-right text-sm font-bold text-gray-900">Rp{{ number_format($item->harga * $item->qty, 0, ',', '.') }}</div>
                </div>
                @endforeach

                <!-- Shipping Options -->
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-6 bg-yellow-50/30">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">Pesan untuk Penjual (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Contoh: Tolong packing kayu ya" class="w-full bg-white border border-gray-200 rounded-xl text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all">
                    </div>
                    <div class="md:w-1/3 md:pl-8 md:border-l border-gray-200 flex flex-col justify-center">
                        <div class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Pengiriman</div>
                        
                        <div x-show="isFetchingRates" class="text-xs text-gray-500 py-3 flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mencari kurir...
                        </div>

                        <div x-show="!isFetchingRates && shippingOptions.length === 0" class="text-xs text-red-500 py-2">
                            Tidak ada kurir yang tersedia untuk alamat ini.
                        </div>

                        <div x-show="!isFetchingRates && shippingOptions.length > 0">
                            <select x-model="selectedShipping" @change="shippingCost = shippingOptions.find(o => o.id === selectedShipping)?.price || 0" class="w-full bg-white border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-[#ee4d2d] focus:border-[#ee4d2d]">
                                <template x-for="rate in shippingOptions" :key="rate.id">
                                    <option :value="rate.id" x-text="rate.courier_label + ' ' + rate.service_type + ' - Rp' + rate.price.toLocaleString('id-ID')"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Hidden Inputs for Form Submission -->
                        <input type="hidden" name="ongkir" :value="shippingCost">
                        <input type="hidden" name="kurir" :value="shippingOptions.find(o => o.id === selectedShipping)?.courier_code || ''">
                    </div>
                </div>

                <div class="px-6 py-5 bg-gray-50 text-right flex items-center justify-end gap-4">
                    <span class="text-sm font-semibold text-gray-500">Total Pesanan ({{ count($items) }} Produk):</span>
                    <span class="text-xl font-black text-gray-900" x-text="'Rp' + ({{ $total }} + shippingCost).toLocaleString('id-ID')">Rp{{ number_format($total + 15000, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. Payment Method Section -->
            <div class="bg-white rounded-2xl shadow-sm mb-6 border border-gray-100 overflow-hidden" x-data="{ metode: 'transfer_bank', bank: '' }">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Metode Pembayaran</h3>
                </div>
                <div class="p-6">
                    {{-- Pilihan Metode --}}
                    <div class="flex flex-wrap gap-4 mb-8">
                        <label class="relative flex items-center px-6 py-3 border-2 rounded-xl cursor-pointer transition-all duration-200"
                            :class="metode === 'transfer_bank' ? 'border-yellow-400 bg-yellow-50 text-gray-900 ring-2 ring-yellow-400 ring-opacity-50' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'">
                            <input type="radio" name="metode_pembayaran" value="transfer_bank" x-model="metode" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="metode === 'transfer_bank' ? 'text-yellow-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="font-bold">Transfer Bank</span>
                        </label>
                        <label class="relative flex items-center px-6 py-3 border-2 rounded-xl cursor-pointer transition-all duration-200"
                            :class="metode === 'qris' ? 'border-yellow-400 bg-yellow-50 text-gray-900 ring-2 ring-yellow-400 ring-opacity-50' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'">
                            <input type="radio" name="metode_pembayaran" value="qris" x-model="metode" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" :class="metode === 'qris' ? 'text-yellow-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="font-bold">QRIS</span>
                        </label>
                    </div>

                    {{-- Panel Transfer Bank --}}
                    <div x-show="metode === 'transfer_bank'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-6">
                        <p class="text-sm font-semibold text-gray-600 mb-4">Pilih Bank Tujuan:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach([
                                'BRI' => ['color' => 'blue',   'no_rek' => '1234-5678-9012-3456', 'atas_nama' => 'Mini Workshop Store'],
                                'BNI' => ['color' => 'orange', 'no_rek' => '9876-5432-1098-7654', 'atas_nama' => 'Mini Workshop Store'],
                                'BCA' => ['color' => 'sky',    'no_rek' => '5555-1234-5678-9000', 'atas_nama' => 'Mini Workshop Store'],
                            ] as $namaBank => $info)
                            <label class="cursor-pointer group" @click="bank = '{{ $namaBank }}'">
                                <input type="radio" name="bank_tujuan" value="{{ $namaBank }}" x-model="bank" class="hidden">
                                <div class="border-2 rounded-2xl p-5 transition-all duration-200 relative overflow-hidden"
                                    :class="bank === '{{ $namaBank }}' ? 'border-gray-900 bg-gray-900 shadow-md' : 'border-gray-200 group-hover:border-gray-400'">
                                    
                                    {{-- Background decoration --}}
                                    <div class="absolute -right-4 -bottom-4 opacity-5 transition-transform group-hover:scale-110" :class="bank === '{{ $namaBank }}' ? 'text-white' : 'text-gray-900'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>

                                    <div class="flex items-center justify-between mb-4 relative z-10">
                                        <span class="font-black text-xl" :class="bank === '{{ $namaBank }}' ? 'text-white' : 'text-{{ $info['color'] }}-600'">{{ $namaBank }}</span>
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
                                            :class="bank === '{{ $namaBank }}' ? 'border-yellow-400 bg-yellow-400' : 'border-gray-300'">
                                            <svg x-show="bank === '{{ $namaBank }}'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-900" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative z-10">
                                        <div class="text-[10px] uppercase tracking-wider font-bold mb-1" :class="bank === '{{ $namaBank }}' ? 'text-gray-400' : 'text-gray-500'">No. Rekening</div>
                                        <div class="font-mono font-bold text-base tracking-widest mb-1" :class="bank === '{{ $namaBank }}' ? 'text-white' : 'text-gray-900'">{{ $info['no_rek'] }}</div>
                                        <div class="text-xs" :class="bank === '{{ $namaBank }}' ? 'text-gray-300' : 'text-gray-600'">a.n. {{ $info['atas_nama'] }}</div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <p x-show="metode === 'transfer_bank' && bank === ''" class="text-xs text-red-500 mt-2">* Silakan pilih bank tujuan transfer.</p>
                    </div>

                    {{-- Panel QRIS --}}
                    <div x-show="metode === 'qris'" x-transition class="mb-6">
                        <div class="flex flex-col sm:flex-row items-center gap-8 bg-gray-50 rounded-2xl p-6">
                            <div class="shrink-0 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                {{-- Placeholder QR Code --}}
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=MiniWorkshopQRIS2026&bgcolor=ffffff&color=000000&margin=10"
                                    alt="QRIS Mini Workshop" class="w-40 h-40">
                            </div>
                            <div>
                                <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">QRIS</div>
                                <div class="text-xl font-black text-gray-900 mb-2">Mini Workshop</div>
                                <p class="text-sm text-gray-500 leading-relaxed mb-4">
                                    Scan QR code di atas menggunakan aplikasi dompet digital atau mobile banking apapun yang mendukung QRIS.
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['GoPay','OVO','Dana','ShopeePay','LinkAja','M-Banking'] as $app)
                                    <span class="px-2 py-1 bg-white border border-gray-200 rounded-lg text-[10px] font-bold text-gray-500">{{ $app }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">* Setelah pembayaran, unggah bukti screenshot dari halaman pesanan Anda.</p>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100 flex flex-col items-end">
                        <div class="w-full md:w-[400px] bg-gray-50 p-6 rounded-2xl space-y-4">
                            <div class="flex justify-between text-sm font-semibold text-gray-500">
                                <span>Subtotal Produk</span>
                                <span class="text-gray-900">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-semibold text-gray-500 pb-4 border-b border-gray-200">
                                <span>Total Ongkos Kirim</span>
                                <span class="text-gray-900">Rp15.000</span>
                            </div>
                            <div class="flex justify-between items-end pt-2">
                                <span class="text-sm font-bold text-gray-900">Total Pembayaran</span>
                                <span class="text-3xl font-black text-gray-900">Rp{{ number_format($total + 15000, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 bg-gray-900 text-right flex flex-col md:flex-row justify-between items-center gap-6 rounded-b-2xl">
                    <p class="text-xs text-gray-400 font-medium">Dengan mengklik 'Buat Pesanan', Anda menyetujui <span class="text-yellow-400 hover:text-yellow-300 cursor-pointer underline underline-offset-2">Syarat & Ketentuan</span> Mini Workshop</p>
                    <button type="submit"
                        @click.prevent="if(metode === 'transfer_bank' && bank === '') { alert('Silakan pilih bank tujuan transfer terlebih dahulu.'); } else { $el.closest('form').submit(); }"
                        class="w-full md:w-auto px-10 py-4 bg-yellow-400 text-gray-900 font-black text-lg rounded-xl shadow-lg hover:bg-yellow-500 hover:scale-105 hover:shadow-yellow-400/20 active:scale-95 transition-all duration-200">
                        Buat Pesanan Sekarang
                    </button>
                </div>
            </div>
        </form>
    </main>

    @include('layouts.footer')



</body>
</html>
</html>
