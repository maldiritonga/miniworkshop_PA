<x-admin-layout>
    <x-slot name="title">Tambah Produk Baru</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.produk.index') }}" class="hover:text-gray-900">Produk</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Tambah Baru</span>
        </nav>

        <div class="bg-white rounded-[32px] p-8 md:p-12 shadow-sm border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Tambah Produk Baru</h1>

            <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Row 1: Nama & Kategori -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="nama_produk" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Produk</label>
                        <input type="text" name="nama_produk" id="nama_produk" required value="{{ old('nama_produk') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Nama produk">
                        <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
                    </div>

                    <div>
                        <label for="id_kategori" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Kategori</label>
                        <select name="id_kategori" id="id_kategori" required onchange="updateSizeOptions(this.value)"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900">
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
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
                        <input type="number" name="harga" id="harga" required value="{{ old('harga') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="150000">
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>

                    <div id="size-container">
                        <label for="size" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Size</label>
                        <select name="size" id="size"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900">
                            <option value="">Pilih Kategori...</option>
                        </select>
                        <x-input-error :messages="$errors->get('size')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 3: Stok & Deskripsi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="stok" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Stok Barang</label>
                        <input type="number" name="stok" id="stok" required value="{{ old('stok') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                               placeholder="Jumlah stok">
                        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-yellow-400 transition-all outline-none text-gray-900 placeholder-gray-400"
                                  placeholder="Deskripsi singkat...">{{ old('deskripsi') }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 4: Gambar (Pake Padding Biar Gak Gepeng) -->
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Gambar Produk</label>
                    <label class="relative block w-full py-16 bg-gray-50 rounded-[32px] border-2 border-dashed border-gray-200 overflow-hidden cursor-pointer group transition-all hover:bg-gray-100/50 text-center">
                        <input type="file" name="gambar" class="hidden" onchange="previewImage(this)">
                        <img id="image-preview" class="absolute inset-0 w-full h-full object-cover hidden">
                        
                        <div id="image-placeholder" class="relative z-10 flex flex-col items-center justify-center">
                            <div class="bg-white p-4 rounded-2xl shadow-sm mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Pilih Gambar Produk</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Klik untuk upload foto</p>
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
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
            </form>
        </div>
    </div>

    <script>
        const sizeOptions = {
            '1': ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'], // Baju
            '2': ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'], // Celana (Assuming same as Baju)
            '3': Array.from({length: 29}, (_, i) => (i + 17).toString()) // Sepatu (17-45)
        };

        function updateSizeOptions(categoryId) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="" disabled selected>Pilih Size</option>';
            
            if (sizeOptions[categoryId]) {
                sizeOptions[categoryId].forEach(size => {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    sizeSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = "-";
                option.textContent = "Tidak Ada Pilihan Size";
                sizeSelect.appendChild(option);
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>
