<x-admin-layout>
    <x-slot name="title">Kelola Akun Kasir</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Akun Kasir</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Akun Kasir</h1>
                <p class="text-gray-500 mt-1">Daftar staf kasir yang dapat mengakses sistem</p>
            </div>
            <a href="{{ route('admin.akun-kasir.create') }}" class="w-fit flex items-center gap-2 px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-2xl hover:bg-yellow-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kasir Baru
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl font-bold text-sm">
            {{ session('success') }}
        </div>
        @endif

        <!-- Table Container -->
        <div class="bg-white rounded-[32px] overflow-hidden shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">NO</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">NAMA KASIR</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">EMAIL</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">NO HP</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">TANGGAL BERGABUNG</th>
                            <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($kasir as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-6 text-sm font-bold text-gray-400">{{ ($kasir->currentPage() - 1) * $kasir->perPage() + $loop->iteration }}</td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>
                                    <div class="font-bold text-gray-900">{{ $item->nama }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm text-gray-600 font-medium">{{ $item->email }}</td>
                            <td class="px-8 py-6 text-sm text-gray-600 font-medium">{{ $item->no_hp }}</td>
                            <td class="px-8 py-6 text-sm font-bold text-gray-400">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.akun-kasir.edit', $item->id_user) }}" 
                                       class="px-4 py-2 bg-yellow-100 text-yellow-700 text-xs font-black rounded-xl hover:bg-yellow-400 hover:text-white transition uppercase tracking-widest">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.akun-kasir.destroy', $item->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun kasir ini?')">
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
                            <td colspan="6" class="px-8 py-20 text-center text-gray-400 font-bold">
                                Belum ada akun kasir. <a href="{{ route('admin.akun-kasir.create') }}" class="text-yellow-600 underline">Tambah baru sekarang.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($kasir->hasPages())
            <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
                {{ $kasir->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
