<x-admin-layout title="Admin Dashboard">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Dasboard Admin</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Selamat datang kembali! Berikut adalah ringkasan aktivitas toko Anda.</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm">
                <select name="filter" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-200 rounded-2xl px-9 py-3.5 text-xs font-black uppercase tracking-widest text-gray-900 focus:ring-2 focus:ring-blue-600 transition cursor-pointer">
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
                <div class="text-[14px] font-black uppercase tracking-[0.15em] mb-3">Pendapatan</div>
                <div class="text-[22px] font-black tracking-tight leading-tight">Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</div>
                <div class="mt-5 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-700 shrink-0"></span>
                    <span class="text-[15px] font-black uppercase tracking-wide">
                        @if($stats['filter'] === 'today') Hari Ini @elseif($stats['filter'] === 'week') Minggu Ini @elseif($stats['filter'] === 'month') Bulan Ini @else Tahun Ini @endif
                    </span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[14px] font-black uppercase tracking-[0.15em] mb-3">Total Pesanan</div>
                <div class="text-[64px] font-black tracking-tighter leading-none">{{ $stats['total_pesanan'] }}</div>

                <div class="mt-5 pt-4 border-t border-gray-900/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[16px] font-black">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-700 shrink-0"></span>Selesai
                        </span>
                        <span class="text-[24px] font-black">{{ $stats['pesanan_selesai'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[16px] font-black">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-700 shrink-0"></span>Diproses
                        </span>
                        <span class="text-[24px] font-black">{{ $stats['pesanan_diproses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[16px] font-black">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-700 shrink-0"></span>Dibatalkan
                        </span>
                        <span class="text-[24px] font-black">{{ $stats['pesanan_dibatalkan'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[14px] font-black uppercase tracking-[0.15em] mb-3">Produk Tersedia</div>
                <div class="text-[64px] font-black tracking-tighter leading-none">{{ $stats['produk_katalog_count'] }}</div>

                <div class="mt-5 space-y-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-700 shrink-0"></span>
                        <span class="text-[16px] font-black uppercase tracking-wide">Aktif di Katalog</span>
                    </div>
                    <div class="text-[16px] font-black ml-4">
                        Produk Tersedia: <span class="text-[28px] font-black">{{ $stats['produk_total_stok'] }}</span> pcs
                    </div>
                </div>
            </div>
        </div>

        <!-- Returns Card -->
        <div class="bg-yellow-400 rounded-[2.5rem] p-8 text-gray-900 shadow-xl shadow-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="text-[14px] font-black uppercase tracking-[0.15em] mb-3">Permintaan Retur</div>
                <div class="text-[64px] font-black tracking-tighter leading-none">{{ $stats['total_retur'] }}</div>
                <div class="mt-5 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-700 shrink-0"></span>
                    <span class="text-[16px] font-black uppercase tracking-wide">Belum Diproses</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
        <!-- Grafik Penjualan -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="mb-8">
                <h3 class="text-[15px] font-black text-gray-900 uppercase tracking-widest">{{ $chartTitle }}</h3>
                <p class="text-[13px] font-bold text-gray-500 mt-1.5">Menampilkan total kuantitas produk (pcs) yang berhasil terjual pada periode ini.</p>
            </div>
            <div class="relative h-[22rem] w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Pesanan Terbaru -->
        <div class="lg:col-span-1 bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm flex flex-col h-full">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-[15px] font-black text-gray-900 uppercase tracking-widest">Pesanan Terbaru</h3>
                <a href="{{ route('admin.pesanan.index') }}" class="text-[12px] font-black text-blue-600 uppercase tracking-widest hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-6">
                @forelse($pesananTerbaru as $order)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[11px] font-black text-gray-500">
                            #{{ $order->id_pesanan }}
                        </div>
                        <div>
                            <div class="text-[14px] font-black text-gray-900">{{ $order->user->nama ?? 'Guest' }}</div>
                            <div class="text-[11px] font-bold text-gray-500">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right hidden sm:block">
                            <div class="text-[14px] font-black text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                            <span class="text-[10px] font-black uppercase text-blue-600 tracking-widest">{{ $order->tipe_pesanan }}</span>
                        </div>
                        <a href="{{ route('admin.pesanan.show', $order->id_pesanan) }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-yellow-400 hover:text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-xl transition">Detail</a>
                    </div>
                </div>
                @empty
                <p class="text-center text-[14px] font-black text-gray-400 py-4">Belum ada pesanan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Script for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Produk Terjual (Pcs)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#EAB308', // Tailwind yellow-500
                        backgroundColor: 'rgba(234, 179, 8, 0.1)', // Light yellow fill
                        borderWidth: 3,
                        pointBackgroundColor: '#EAB308',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return value + ' Pcs';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                                color: '#9ca3af',
                                stepSize: 1,
                                callback: function(value) {
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>
