<x-admin-layout title="Laporan Keuangan">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Laporan Keuangan</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Pantau performa penjualan online dan offline toko Anda.</p>
        </div>
        <button onclick="window.print()" class="px-6 py-3 bg-gray-900 text-white text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-800 transition shadow-lg no-print">
            Cetak Laporan
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 mb-8 no-print">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-[13px] font-bold focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-[13px] font-bold focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="py-4 bg-blue-600 text-white text-xs font-black rounded-2xl uppercase tracking-widest shadow-lg shadow-blue-100 hover:bg-blue-700 transition">
                Filter Data
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 rounded-[2.5rem] p-8 text-white shadow-xl shadow-gray-200">
            <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Total Pendapatan</div>
            <div class="text-3xl font-black tracking-tighter">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="mt-4 pt-4 border-t border-white/10 text-[11px] font-bold opacity-80">Gabungan Online & Offline</div>
        </div>
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Transaksi Online</div>
            <div class="text-3xl font-black text-gray-900 tracking-tighter">Rp {{ number_format($totalOnline, 0, ',', '.') }}</div>
            <div class="mt-4 pt-4 border-t border-gray-50 text-[11px] font-bold text-blue-600">Via Website</div>
        </div>
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Transaksi Offline</div>
            <div class="text-3xl font-black text-gray-900 tracking-tighter">Rp {{ number_format($totalOffline, 0, ',', '.') }}</div>
            <div class="mt-4 pt-4 border-t border-gray-50 text-[11px] font-bold text-green-600">Langsung di Toko</div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Rincian Transaksi</h3>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">{{ count($pesanan) }} Transaksi Ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipe</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pesanan as $order)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-5 font-bold text-gray-900 text-[13px]">#{{ $order->id_pesanan }}</td>
                        <td class="px-8 py-5">
                            @if($order->tipe_pesanan == 'offline')
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest">Offline</span>
                            @else
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest">Online</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="text-[13px] font-bold text-gray-900">{{ $order->user->nama ?? 'Guest' }}</div>
                            <div class="text-[10px] text-gray-400 font-medium">{{ $order->no_hp }}</div>
                        </td>
                        <td class="px-8 py-5 text-[13px] text-gray-600 font-medium">{{ $order->tanggal_pesanan }}</td>
                        <td class="px-8 py-5 font-black text-gray-900 text-[13px]">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td class="px-8 py-5">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ str_replace('_', ' ', $order->status_pesanan) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-gray-300 font-bold">Tidak ada data dalam periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .shadow-sm, .shadow-xl { shadow: none !important; }
            .rounded-[2.5rem] { border-radius: 1rem !important; }
        }
    </style>
</x-admin-layout>
