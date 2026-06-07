<x-admin-layout title="Admin Dashboard">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Ringkasan Toko</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Selamat datang kembali! Berikut adalah ringkasan aktivitas toko Anda.</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm">
                <select name="filter" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-200 rounded-2xl px-6 py-3.5 text-xs font-black uppercase tracking-widest text-gray-800 focus:ring-2 focus:ring-blue-600 transition cursor-pointer">
                    <option value="today" {{ $stats['filter'] === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $stats['filter'] === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $stats['filter'] === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="year" {{ $stats['filter'] === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </form>
        </div>
    </div>

    <x-admin.stok-alert :is-total-stok-rendah="$isTotalStokRendah" :total-stok-gabungan="$totalStokGabungan" />

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Revenue Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Pendapatan</div>
                <div class="text-2xl font-black tracking-tighter">Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-600"></span>
                    <span class="text-[11px] font-bold opacity-80 uppercase">
                        @if($stats['filter'] === 'today') Hari Ini @elseif($stats['filter'] === 'week') Minggu Ini @elseif($stats['filter'] === 'month') Bulan Ini @else Tahun Ini @endif
                    </span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Total Pesanan</div>
                <div class="text-3xl font-black tracking-tighter">{{ $stats['total_pesanan'] }}</div>
                
                <div class="mt-4 pt-4 border-t border-gray-900/10 space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="flex items-center gap-2 opacity-75"><span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>Selesai</span>
                        <span class="font-bold">{{ $stats['pesanan_selesai'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="flex items-center gap-2 opacity-75"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>Diproses</span>
                        <span class="font-bold">{{ $stats['pesanan_diproses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="flex items-center gap-2 opacity-75"><span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>Dibatalkan</span>
                        <span class="font-bold">{{ $stats['pesanan_dibatalkan'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Produk Tersedia</div>
                <div class="text-3xl font-black tracking-tighter">{{ $stats['produk_katalog_count'] }}</div>
                
                <div class="mt-4 flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-[11px] font-bold opacity-80 uppercase">Aktif di Katalog</span>
                    </div>
                    <div class="text-[11px] font-bold opacity-60 ml-4">Total Stok: {{ $stats['produk_total_stok'] }} pcs</div>
                </div>
            </div>
        </div>

        <!-- Returns Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Permintaan Retur</div>
                <div class="text-3xl font-black tracking-tighter">{{ $stats['total_retur'] }}</div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                    <span class="text-[11px] font-bold opacity-80 uppercase">Belum Diproses</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Pesanan Terbaru</h3>
                <a href="{{ route('admin.pesanan.index') }}" class="text-[11px] font-black text-blue-600 uppercase tracking-widest hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-6">
                @forelse($pesananTerbaru as $order)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[10px] font-black text-gray-400">
                            #{{ $order->id_pesanan }}
                        </div>
                        <div>
                            <div class="text-[13px] font-bold text-gray-900">{{ $order->user->nama ?? 'Guest' }}</div>
                            <div class="text-[10px] text-gray-400 font-medium">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[13px] font-black text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                        <span class="text-[9px] font-black uppercase text-blue-600 tracking-widest">{{ $order->tipe_pesanan }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-sm font-bold text-gray-400 py-4">Belum ada pesanan.</p>
                @endforelse
            </div>
    </div>
</x-admin-layout>
