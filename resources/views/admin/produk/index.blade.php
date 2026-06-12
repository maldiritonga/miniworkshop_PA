<x-admin-layout>
    <x-slot name="title">Daftar Produk</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Produk</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Produk</h1>
                <p class="text-gray-500 mt-1">Kelola semua produk di toko</p>
            </div>
            <a href="{{ route('admin.produk.create') }}" class="w-fit flex items-center gap-2 px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-2xl hover:bg-yellow-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk Baru
            </a>
        </div>

        <x-flash-message />

        <!-- Search & Filter Area -->
        <div class="bg-gray-100 rounded-[28px] p-4 flex items-center">
            <form action="{{ route('admin.produk.index') }}" method="GET" class="w-full flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." 
                       class="w-full bg-transparent border-none focus:ring-0 text-gray-700 placeholder-gray-400 font-medium">
            </form>
        </div>

        <!-- Product Table Container -->
        <div class="bg-white rounded-[32px] overflow-hidden shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">NO</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">PRODUK</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">HARGA</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">SIZE</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">STOK</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">STATUS</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">TANGGAL</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($produk as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-6 text-sm font-bold text-gray-400">{{ ($produk->currentPage()-1) * $produk->perPage() + $loop->iteration }}</td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                        @if($item->gambar_url)
                                            <img src="{{ $item->gambar_url }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-[15px]">{{ $item->nama_produk }}</div>
                                        <div class="text-[11px] font-bold text-gray-400 uppercase">{{ $item->kategori->nama_kategori }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-yellow-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-600">{{ $item->size ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-900">{{ $item->stok }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @if($item->stok > 0 && $item->status_produk === 'aktif')
                                    <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-600 border border-green-100 uppercase">Tersedia</span>
                                @elseif($item->stok <= 0)
                                    <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase">Habis</span>
                                @else
                                    <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase">Tidak Tersedia</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-gray-400">
                                {{ $item->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-8 py-6" x-data="{ open: false, confirmDelete: false }">
                                <div class="flex items-center gap-3">
                                    <button @click="open = true" 
                                       class="px-4 py-2 bg-blue-100 text-blue-700 text-xs font-black rounded-xl hover:bg-blue-400 hover:text-white transition uppercase tracking-widest">
                                        Offline
                                    </button>
                                    <a href="{{ route('admin.produk.edit', $item->id_produk) }}" 
                                       class="px-4 py-2 bg-yellow-100 text-yellow-700 text-xs font-black rounded-xl hover:bg-yellow-400 hover:text-white transition uppercase tracking-widest">
                                        Edit
                                    </a>
                                    <button @click="confirmDelete = true" 
                                            class="px-4 py-2 bg-red-50 text-red-500 text-xs font-black rounded-xl hover:bg-red-500 hover:text-white transition uppercase tracking-widest">
                                        Hapus
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div @click.away="confirmDelete = false" class="bg-white rounded-[32px] p-8 w-full max-w-sm shadow-2xl text-center">
                                        <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Hapus</h3>
                                        <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus produk <span class="font-bold text-gray-900">{{ $item->nama_produk }}</span>? Data yang sudah dihapus tidak dapat dikembalikan.</p>
                                        
                                        <div class="flex gap-3">
                                            <button @click="confirmDelete = false" class="flex-1 py-3 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-200 transition">Batal</button>
                                            <form action="{{ route('admin.produk.destroy', $item->id_produk) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full py-3 bg-red-600 text-white text-xs font-black rounded-2xl shadow-lg shadow-red-200 hover:bg-red-700 transition uppercase tracking-widest">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Offline Sale Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div @click.away="open = false" class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Transaksi Offline</h3>
                                        <p class="text-sm text-gray-500 mb-6">Produk: <span class="font-bold text-gray-900">{{ $item->nama_produk }}</span></p>
                                        
                                        <form action="{{ route('admin.offline-transaction.store') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                                            
                                            <div>
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Jumlah Pembelian</label>
                                                <input type="number" name="qty" value="1" min="1" max="{{ $item->stok }}" required
                                                       class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-400">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Metode Pembayaran</label>
                                                <select name="metode_pembayaran" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-400">
                                                    <option value="cash">Cash</option>
                                                    <option value="transfer bank">Transfer Bank</option>
                                                    <option value="qris">QRIS</option>
                                                    <option value="debit">Debit</option>
                                                </select>
                                            </div>

                                            <div class="flex gap-3 pt-4">
                                                <button type="button" @click="open = false" class="flex-1 py-4 bg-gray-100 text-gray-500 text-xs font-black rounded-2xl uppercase tracking-widest">Batal</button>
                                                <button type="submit" class="flex-1 py-4 bg-blue-600 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-200 uppercase tracking-widest">Proses</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center text-gray-400 font-bold">
                                Belum ada produk. <a href="{{ route('admin.produk.create') }}" class="text-yellow-600">Tambah sekarang.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($produk->hasPages())
            <div class="px-8 py-6 border-t border-gray-50">
                {{ $produk->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
