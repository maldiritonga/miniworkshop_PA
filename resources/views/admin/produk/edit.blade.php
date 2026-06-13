<x-admin-layout>
    <x-slot name="title">Edit Produk</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.produk.index') }}" class="hover:text-gray-900">Produk</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Edit</span>
        </nav>

        <div class="bg-white rounded-[32px] p-8 md:p-12 shadow-sm border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Produk</h1>

            <form action="{{ route('admin.produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Row 1: Nama & Kategori -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="nama_produk" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Produk</label>
                        <input type="text" name="nama_produk" id="nama_produk" required value="{{ old('nama_produk', $produk->nama_produk) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Nama produk">
                        <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
                    </div>

                    <div>
                        <label for="id_kategori" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Kategori</label>
                        <select name="id_kategori" id="id_kategori" required onchange="updateSizeOptions(this.value)"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900">
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ old('id_kategori', $produk->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('id_kategori')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 2: Harga & Size -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="harga" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Harga (Rp)</label>
                        <input type="number" name="harga" id="harga" required value="{{ old('harga', $produk->harga) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="150000">
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>

                    <div id="size-container">
                        <label for="size" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Size</label>
                        <select name="size" id="size"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900">
                            <option value="{{ $produk->size }}">{{ $produk->size }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('size')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 3: Stok & Deskripsi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="stok" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Stok Barang</label>
                        <input type="number" name="stok" id="stok" required value="{{ old('stok', $produk->stok) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Jumlah stok">
                        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                                  placeholder="Deskripsi singkat...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 4: Diskon -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label for="diskon_persen" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Diskon (%)</label>
                        <input type="number" name="diskon_persen" id="diskon_persen" min="0" max="100" value="{{ old('diskon_persen', $produk->diskon_persen) }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Contoh: 10">
                        <x-input-error :messages="$errors->get('diskon_persen')" class="mt-2" />
                    </div>

                    <div>
                        <label for="diskon_mulai" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tanggal Mulai Diskon</label>
                        <input type="datetime-local" name="diskon_mulai" id="diskon_mulai" value="{{ old('diskon_mulai', $produk->diskon_mulai ? \Carbon\Carbon::parse($produk->diskon_mulai)->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400">
                        <x-input-error :messages="$errors->get('diskon_mulai')" class="mt-2" />
                    </div>

                    <div>
                        <label for="diskon_selesai" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tanggal Selesai Diskon</label>
                        <input type="datetime-local" name="diskon_selesai" id="diskon_selesai" value="{{ old('diskon_selesai', $produk->diskon_selesai ? \Carbon\Carbon::parse($produk->diskon_selesai)->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400">
                        <x-input-error :messages="$errors->get('diskon_selesai')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 5: Gambar (Pake Padding) -->
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Gambar Produk</label>
                    <label class="relative block w-full py-16 bg-gray-50 rounded-[32px] border-2 border-dashed border-gray-200 overflow-hidden cursor-pointer group transition-all hover:bg-gray-100/50 text-center">
                        <input type="file" name="gambar" class="hidden" onchange="previewImage(this)">
                        <img id="image-preview" src="{{ $produk->gambar_url ?? '' }}" 
                             class="absolute inset-0 w-full h-full object-cover {{ $produk->gambar_url ? '' : 'hidden' }}">
                        
                        <div id="image-placeholder" class="relative z-10 flex flex-col items-center justify-center">
                            <div class="bg-white/90 backdrop-blur-sm p-4 rounded-2xl shadow-sm mb-3 group-hover:scale-110 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Klik Untuk Ganti Gambar</p>
                        </div>
                    </label>
                    <x-input-error :messages="$errors->get('gambar')" class="mt-2" />
                </div>

                <!-- Footer: Buttons aligned right -->
                <div class="pt-6 flex justify-end gap-4">
                    <a href="{{ route('admin.produk.index') }}" class="px-8 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-10 py-4 bg-yellow-400 text-gray-900 font-black rounded-2xl hover:bg-yellow-500 transition shadow-lg shadow-yellow-100 uppercase tracking-widest text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const sizeOptions = {
            '1': ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'], // Baju
            '2': ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'], // Celana
            '3': Array.from({length: 29}, (_, i) => (i + 17).toString()) // Sepatu (17-45)
        };

        function updateSizeOptions(categoryId, selectedSize = null) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="" disabled>Pilih Size</option>';
            
            if (sizeOptions[categoryId]) {
                sizeOptions[categoryId].forEach(size => {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    if (selectedSize && size == selectedSize) {
                        option.selected = true;
                    }
                    sizeSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = "-";
                option.textContent = "Tidak Ada Pilihan Size";
                sizeSelect.appendChild(option);
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            const categoryId = document.getElementById('id_kategori').value;
            const currentSize = "{{ $produk->size }}";
            if (categoryId) {
                updateSizeOptions(categoryId, currentSize);
            }
        });

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>
