<x-admin-layout title="Barang Keluar">
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
        <a href="{{ route('admin.produk.barang-masuk') }}" class="px-6 py-2.5 rounded-2xl font-black text-sm bg-white text-gray-500 hover:bg-gray-50 border border-gray-200 transition">Barang Masuk</a>
        <a href="{{ route('admin.produk.barang-keluar') }}" class="px-6 py-2.5 rounded-2xl font-black text-sm bg-yellow-400 text-gray-900 shadow-sm">Barang Keluar</a>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">ID Pesanan</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Pembeli</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Qty</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Subtotal</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Tanggal Keluar</th>
                        <th class="px-8 py-5 text-[12px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($barangKeluar as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-5">
                            <a href="{{ route('admin.pesanan.show', $item->id_pesanan) }}" class="font-bold text-blue-600 hover:underline text-[15px]">
                                #{{ $item->id_pesanan }}
                            </a>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                @if($item->produk && $item->produk->gambar_url)
                                    <img src="{{ $item->produk->gambar_url }}" class="w-10 h-10 rounded-xl object-cover" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-[15px] font-bold text-gray-900">{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</div>
                                    @if($item->ukuran)
                                        <div class="text-[12px] text-gray-500">Ukuran: {{ $item->ukuran }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[14px] text-gray-600 font-medium">{{ $item->pesanan->user->nama ?? 'Guest' }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[15px] font-bold text-red-600 bg-red-50 px-3 py-1 rounded-full">-{{ $item->qty }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[15px] font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[14px] text-gray-600 font-medium">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($item->produk)
                            <a href="{{ route('admin.produk.duplicate', $item->produk->id_produk) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 text-yellow-700 text-xs font-black rounded-xl hover:bg-yellow-400 hover:text-white transition uppercase tracking-widest">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                                Restock / Edit
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-400">Belum ada barang keluar (belum ada pesanan yang selesai).</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangKeluar->hasPages())
        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $barangKeluar->links() }}
        </div>
        @endif
    </div>
    </div>
</x-admin-layout>
