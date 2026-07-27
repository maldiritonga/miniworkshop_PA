<x-admin-layout title="Laporan Keuangan">
    <x-flash-message />

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            html, body { height: auto !important; overflow: visible !important; }
            .h-screen { height: auto !important; }
            .overflow-hidden, .overflow-y-auto { overflow: visible !important; }
            main { display: block !important; flex: none !important; }
            aside { display: none !important; }
            .flex-1 { flex: none !important; }
            .shadow-sm, .shadow-xl { box-shadow: none !important; }
            .rounded-\[2\.5rem\] { border-radius: 0 !important; }
            table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            th, td { border: 1px solid #d1d5db !important; }
        }
    </style>

    {{-- ── Toolbar (screen only) ── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 no-print">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Laporan Keuangan</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Rincian transaksi berdasarkan periode yang dipilih.</p>
        </div>
        <button onclick="window.print()"
            class="flex items-center gap-2 px-6 py-3 bg-gray-900 text-white text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-gray-800 transition shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak / Export
        </button>
    </div>

    {{-- ── Filter (screen only) ── --}}
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 mb-8 no-print">
        <form action="{{ route('admin.laporan.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="filter_applied" value="1">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full bg-gray-50 border-none rounded-2xl p-4 text-[13px] font-bold focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full bg-gray-50 border-none rounded-2xl p-4 text-[13px] font-bold focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Transaksi</label>
                <select name="tipe" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-[13px] font-bold focus:ring-2 focus:ring-blue-400">
                    <option value="semua"   {{ ($tipe ?? 'semua') === 'semua'   ? 'selected' : '' }}>Semua</option>
                    <option value="online"  {{ ($tipe ?? '') === 'online'        ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ ($tipe ?? '') === 'offline'       ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
            <button type="submit"
                class="py-4 bg-blue-600 text-white text-xs font-black rounded-2xl uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                Filter
            </button>
        </form>
    </div>

    {{-- ── DOKUMEN LAPORAN ── --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">

        {{-- Kop --}}
        <div class="px-10 py-8 border-b border-gray-100 text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                <span class="text-xl font-black uppercase tracking-tight text-gray-900">Mini Workshop</span>
            </div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pekanbaru, Riau · Sumatra</div>
            <div class="mt-4 text-lg font-black text-gray-900 uppercase tracking-wider">Laporan Keuangan</div>
            <div class="text-[13px] font-bold text-gray-500 mt-1">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                @if(($tipe ?? 'semua') !== 'semua')
                    · <span class="capitalize">{{ $tipe }}</span>
                @endif
            </div>
        </div>

        {{-- Ringkasan singkat --}}
        <div class="px-10 py-5 border-b border-gray-100 flex flex-wrap gap-6">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Transaksi: </span>
                <span class="text-[15px] font-black text-gray-900">{{ count($pesanan) }}</span>
            </div>
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pendapatan: </span>
                <span class="text-[15px] font-black text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Online: </span>
                <span class="text-[15px] font-black text-blue-600">Rp {{ number_format($totalOnline, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Offline: </span>
                <span class="text-[15px] font-black text-green-600">Rp {{ number_format($totalOffline, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- ── Tabel Rincian ── --}}
        <div class="px-10 py-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">No</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tanggal</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tipe</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Produk</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Qty</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest">Harga Satuan</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pesanan as $no => $order)
                            @php $firstRow = true; $rowspan = max($order->detail->count(), 1); @endphp
                            @forelse($order->detail as $item)
                            <tr class="hover:bg-gray-50/40">
                                @if($firstRow)
                                <td class="px-4 py-3 text-center font-bold text-gray-400 text-[12px] align-middle border-r border-gray-100" rowspan="{{ $rowspan }}">{{ $no + 1 }}</td>
                                <td class="px-4 py-3 align-middle border-r border-gray-100 whitespace-nowrap" rowspan="{{ $rowspan }}">
                                    <div class="font-bold text-gray-900 text-[12px]">{{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 font-black text-gray-900 text-[12px] align-middle border-r border-gray-100" rowspan="{{ $rowspan }}">#{{ $order->id_pesanan }}</td>
                                <td class="px-4 py-3 align-middle border-r border-gray-100" rowspan="{{ $rowspan }}">
                                    <div class="font-bold text-gray-900 text-[12px]">{{ $order->user->nama ?? 'Toko Offline' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $order->no_hp ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle border-r border-gray-100 text-center" rowspan="{{ $rowspan }}">
                                    @if($order->tipe_pesanan === 'offline')
                                        <span class="px-2 py-1 bg-green-50 text-green-700 rounded-lg text-[9px] font-black uppercase">Offline</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-[9px] font-black uppercase">Online</span>
                                    @endif
                                </td>
                                @php $firstRow = false; @endphp
                                @endif
                                <td class="px-4 py-3 font-medium text-gray-800 text-[12px]">{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</td>
                                <td class="px-4 py-3 text-center font-bold text-gray-700 text-[12px]">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 text-[12px]">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-black text-gray-900 text-[12px]">Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-4 py-3 text-center font-bold text-gray-400 text-[12px] align-middle border-r border-gray-100">{{ $no + 1 }}</td>
                                <td class="px-4 py-3 text-[12px] text-gray-600 border-r border-gray-100">{{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-black text-gray-900 text-[12px] border-r border-gray-100">#{{ $order->id_pesanan }}</td>
                                <td class="px-4 py-3 text-[12px] text-gray-700 border-r border-gray-100">{{ $order->user->nama ?? 'Toko Offline' }}</td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <span class="px-2 py-1 bg-gray-50 text-gray-500 rounded text-[9px] font-black uppercase">{{ $order->tipe_pesanan }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-[12px] italic" colspan="4">Tidak ada detail produk</td>
                            </tr>
                            @endforelse
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center text-gray-300 font-bold">
                                Tidak ada transaksi dalam periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($pesanan->isNotEmpty())
                    <tfoot>
                        <tr class="bg-gray-100 border-t-2 border-gray-300">
                            <td colspan="8" class="px-4 py-4 font-black text-gray-900 uppercase text-xs tracking-widest text-right">
                                Total Pendapatan ({{ count($pesanan) }} transaksi)
                            </td>
                            <td class="px-4 py-4 text-right font-black text-gray-900 text-base">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>

</x-admin-layout>
