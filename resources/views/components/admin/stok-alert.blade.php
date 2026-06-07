@props([
    'isTotalStokRendah' => false,
    'totalStokGabungan' => 0,
])

@php $batas = \App\Models\Produk::STOK_BATAS_RENDAH; @endphp

@if($isTotalStokRendah)
<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 px-6 py-5 bg-gradient-to-r from-red-50 to-orange-50 border border-red-200/60 rounded-3xl shadow-sm relative overflow-hidden group">
    <!-- Decorative abstract shape -->
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-red-100 rounded-full blur-3xl opacity-50 pointer-events-none group-hover:opacity-80 transition-opacity duration-700"></div>

    <div class="flex items-start md:items-center gap-5 relative z-10">
        <div class="shrink-0 relative">
            <!-- Pulsing background -->
            <div class="absolute inset-0 bg-red-500 rounded-full animate-ping opacity-20"></div>
            <!-- Icon container -->
            <div class="relative w-12 h-12 rounded-full bg-white shadow-sm border border-red-100 flex items-center justify-center text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <div>
            <h4 class="text-xl font-black text-red-800 tracking-widest uppercase mb-1 drop-shadow-sm">STOK MENIPIS</h4>
            <p class="text-lg md:text-xl text-red-900 font-bold leading-relaxed">
                Peringatan: Stok produk hampir habis <span class="font-black text-red-700 bg-red-100/80 px-2 py-0.5 rounded-lg mx-1">tersisa {{ $totalStokGabungan }} pcs</span>, segera tambahkan produk baru.
            </p>
        </div>
    </div>
    
    <a href="{{ route('admin.produk.index') }}"
       class="shrink-0 relative z-10 inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-red-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/40 transform hover:-translate-y-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        WARNING
    </a>
</div>
@endif
