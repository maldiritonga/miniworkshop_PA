<x-admin-layout title="Barang Masuk">
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
                <p class="text-gray-500 mt-1 mb-2">Kelola semua produk di toko</p>
            </div>
            <a href="{{ route('admin.produk.create') }}" class="w-fit flex items-center gap-2 px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-2xl hover:bg-yellow-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk Baru
            </a>
        </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-4 mb-8">
        <a href="{{ route('admin.produk.index') }}" class="px-6 py-2.5 rounded-2xl font-black text-sm bg-white text-gray-500 hover:bg-gray-50 border border-gray-200 transition">Semua Produk</a>
        <a href="{{ route('admin.produk.barang-masuk') }}" class="px-6 py-2.5 rounded-2xl font-black text-sm bg-yellow-400 text-gray-900 shadow-sm">Barang Masuk</a>
        <a href="{{ route('admin.produk.barang-keluar') }}" class="px-6 py-2.5 rounded-2xl font-black text-sm bg-white text-gray-500 hover:bg-gray-50 border border-gray-200 transition">Barang Keluar</a>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">ID Produk</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Harga</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Stok Awal</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Tanggal Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($produk as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-5">
                            <span class="font-bold text-gray-900 text-[15px]">#{{ $item->id_produk }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                @if($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" class="w-10 h-10 rounded-xl object-cover" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="text-[15px] font-bold text-gray-900">{{ $item->nama_produk }}</div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[14px] text-gray-600 font-medium">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[15px] font-bold text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[15px] font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">{{ $item->stok }} Pcs</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[14px] text-gray-600 font-medium">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-400">Belum ada barang masuk (produk baru).</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produk->hasPages())
        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $produk->links() }}
        </div>
        @endif
    </div>
    </div>
</x-admin-layout>
