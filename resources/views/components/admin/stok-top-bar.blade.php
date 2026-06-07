@props(['totalStokGabungan' => 0])

@if($totalStokGabungan < \App\Models\Produk::STOK_BATAS_RENDAH)
<a href="{{ route('admin.dashboard') }}"
   class="flex items-center gap-3 mx-6 mt-6 sm:mx-10 lg:mx-16 px-5 py-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 hover:bg-rose-100 transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
    <span class="text-xs font-bold">
        Total stok gabungan {{ $totalStokGabungan }} pcs (di bawah {{ \App\Models\Produk::STOK_BATAS_RENDAH }} pcs) — lihat dashboard
    </span>
</a>
@endif
