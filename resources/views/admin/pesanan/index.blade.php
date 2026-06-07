<x-admin-layout title="Mengelola Pesanan">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Mengelola Pesanan</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Pantau dan kelola semua pesanan pelanggan di sini.</p>
        </div>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">ID Pesanan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tipe</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pesanan as $order)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-5">
                            <span class="font-bold text-gray-900 text-[13px]">#{{ $order->id_pesanan }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-[10px] font-black">
                                    {{ strtoupper(substr($order->user->nama ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-[13px] font-bold text-gray-900">{{ $order->user->nama ?? 'Guest' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $order->user->email ?? 'Walking Customer' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[13px] text-gray-600 font-medium">{{ $order->tanggal_pesanan }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[13px] font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($order->tipe_pesanan == 'offline')
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest">Offline</span>
                            @else
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest">Online</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $statusClass = [
                                    'menunggu_pembayaran' => 'bg-yellow-50 text-yellow-600',
                                    'dikemas' => 'bg-blue-50 text-blue-600',
                                    'dikirim' => 'bg-purple-50 text-purple-600',
                                    'diantar' => 'bg-orange-50 text-orange-600',
                                    'selesai' => 'bg-green-50 text-green-600',
                                    'dibatalkan' => 'bg-red-50 text-red-600',
                                ][$order->status_pesanan] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                {{ str_replace('_', ' ', $order->status_pesanan) }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.pesanan.show', $order->id_pesanan) }}" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.pesanan.destroy', $order->id_pesanan) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-400">Belum ada pesanan masuk.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pesanan->hasPages())
        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $pesanan->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
