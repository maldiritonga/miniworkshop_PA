<x-admin-layout title="Kelola Retur">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Kelola Retur</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Daftar pengajuan retur barang dari pelanggan.</p>
        </div>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">ID Retur</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">ID Pesanan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Alasan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Foto</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($retur as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-5">
                            <span class="font-bold text-gray-900 text-[13px]">#{{ $item->id_retur }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-[10px] font-black">
                                    {{ strtoupper(substr($item->pesanan->user->nama ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-[13px] font-bold text-gray-900">{{ $item->pesanan->user->nama ?? 'Walking Customer' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $item->pesanan->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                    @if($item->produk->gambar)
                                        <img src="{{ asset('images/products/'.$item->produk->gambar) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="text-[13px] font-bold text-gray-900">{{ $item->produk->nama_produk }}</div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[13px] text-gray-500 font-medium">#{{ $item->id_pesanan }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-[12px] text-gray-500 max-w-[180px] truncate" title="{{ $item->alasan_retur }}">
                                {{ $item->alasan_retur ?? '-' }}
                            </p>
                        </td>
                        <td class="px-8 py-5">
                            {{-- Thumbnail foto pertama --}}
                            @if($item->foto_bukti && count($item->foto_bukti) > 0)
                            <div class="flex items-center gap-1.5">
                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                                    <img src="{{ asset('images/retur/' . $item->foto_bukti[0]) }}"
                                        class="w-full h-full object-cover">
                                </div>
                                @if(count($item->foto_bukti) > 1)
                                <span class="text-[10px] font-bold text-gray-400">+{{ count($item->foto_bukti) - 1 }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-[11px] text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[12px] text-gray-400 font-medium">{{ $item->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $statusClass = [
                                    'diajukan'  => 'bg-yellow-50 text-yellow-600',
                                    'diproses'  => 'bg-blue-50 text-blue-600',
                                    'diterima'  => 'bg-indigo-50 text-indigo-600',
                                    'selesai'   => 'bg-green-50 text-green-600',
                                    'ditolak'   => 'bg-red-50 text-red-600',
                                ][$item->status_retur] ?? 'bg-gray-50 text-gray-600';
                                $statusLabel = [
                                    'diajukan' => 'Diajukan',
                                    'diproses' => 'Diproses',
                                    'diterima' => 'Diterima',
                                    'selesai'  => 'Selesai',
                                    'ditolak'  => 'Ditolak',
                                ][$item->status_retur] ?? ucfirst($item->status_retur);
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <a href="{{ route('admin.retur.show', $item->id_retur) }}"
                                class="p-2 text-gray-400 hover:text-blue-600 transition inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-8 py-20 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-400">Belum ada pengajuan retur.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($retur->hasPages())
        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $retur->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
