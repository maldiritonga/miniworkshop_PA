<x-admin-layout title="Kelola Akun Pelanggan">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Kelola Akun Pelanggan</h1>
            <p class="text-[13px] text-gray-500 font-medium mt-1">Pantau dan kelola status akun pelanggan yang terdaftar.</p>
        </div>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">No</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Pelanggan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Email</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">No HP</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tgl Daftar</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggan as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition {{ $item->is_blocked ? 'opacity-60' : '' }}">
                        <td class="px-8 py-5 text-sm font-bold text-gray-400">{{ ($pelanggan->currentPage() - 1) * $pelanggan->perPage() + $loop->iteration }}</td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full flex items-center justify-center font-black text-sm
                                    {{ $item->is_blocked ? 'bg-red-100 text-red-500' : 'bg-blue-50 text-blue-600' }}">
                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                </div>
                                <div class="font-bold text-gray-900 text-[13px]">{{ $item->nama }}</div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-[13px] text-gray-500 font-medium">{{ $item->email }}</td>
                        <td class="px-8 py-5 text-[13px] text-gray-500 font-medium">{{ $item->no_hp ?? '-' }}</td>
                        <td class="px-8 py-5 text-[13px] text-gray-400 font-medium">
                            {{ $item->created_at->format('d M Y') }}
                        </td>
                        <td class="px-8 py-5">
                            @if($item->is_blocked)
                                <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[9px] font-black uppercase tracking-widest">
                                    Diblokir
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest">
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.akun-pelanggan.toggle-block', $item->id_user) }}" method="POST"
                                    onsubmit="return confirm('{{ $item->is_blocked ? 'Aktifkan kembali akun ' . $item->nama . '?' : 'Blokir akun ' . $item->nama . '? Akun ini tidak akan bisa login.' }}')">
                                    @csrf
                                    @if($item->is_blocked)
                                        <button type="submit"
                                            class="px-4 py-2 bg-green-50 text-green-600 text-xs font-black rounded-xl hover:bg-green-500 hover:text-white transition uppercase tracking-widest">
                                            Aktifkan
                                        </button>
                                    @else
                                        <button type="submit"
                                            class="px-4 py-2 bg-orange-50 text-orange-500 text-xs font-black rounded-xl hover:bg-orange-500 hover:text-white transition uppercase tracking-widest">
                                            Blokir
                                        </button>
                                    @endif
                                </form>
                                <form action="{{ route('admin.akun-pelanggan.destroy', $item->id_user) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus akun {{ $item->nama }} secara permanen? Semua data terkait pengguna ini mungkin akan hilang dan tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-50 text-red-500 text-xs font-black rounded-xl hover:bg-red-500 hover:text-white transition uppercase tracking-widest">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-400">Belum ada akun pelanggan terdaftar.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pelanggan->hasPages())
        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $pelanggan->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
