<!-- Help Button -->
<button type="button" @click="helpOpen = true" class="p-2 text-gray-700 hover:bg-gray-100 rounded-full transition" title="Bantuan Alur Pemesanan">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
</button>

<!-- Help Modal -->
<div x-show="helpOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
    <div @click.away="helpOpen = false" class="bg-white rounded-[2.5rem] p-8 w-full max-w-2xl shadow-2xl relative border border-gray-100 max-h-[90vh] overflow-y-auto">
        <!-- Close Button -->
        <button type="button" @click="helpOpen = false" class="absolute right-6 top-6 p-2 text-gray-400 hover:text-gray-900 rounded-full hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Alur Pemesanan</h3>
                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Langkah mudah berbelanja di Mini Workshop</p>
            </div>
        </div>

        <!-- Timeline steps -->
        <div class="space-y-6 relative before:absolute before:left-6 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
            <!-- Step 1 -->
            <div class="flex gap-4 relative">
                <div class="w-12 h-12 rounded-full bg-yellow-400 text-gray-900 font-black flex items-center justify-center shrink-0 z-10 shadow-sm text-sm border-4 border-white">1</div>
                <div class="pt-1">
                    <h4 class="font-bold text-gray-900 text-sm md:text-base">Pilih Produk Preloved</h4>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Jelajahi koleksi kami di halaman katalog. Pilih pakaian atau sepatu preloved premium yang Anda minati, lalu periksa detail deskripsi dan ukurannya.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4 relative">
                <div class="w-12 h-12 rounded-full bg-yellow-400 text-gray-900 font-black flex items-center justify-center shrink-0 z-10 shadow-sm text-sm border-4 border-white">2</div>
                <div class="pt-1">
                    <h4 class="font-bold text-gray-900 text-sm md:text-base">Masukkan Keranjang / Beli Sekarang</h4>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Klik "Tambah ke Keranjang" jika ingin berbelanja lebih banyak, atau langsung klik "Beli Sekarang" untuk memproses pembelian produk tunggal.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4 relative">
                <div class="w-12 h-12 rounded-full bg-yellow-400 text-gray-900 font-black flex items-center justify-center shrink-0 z-10 shadow-sm text-sm border-4 border-white">3</div>
                <div class="pt-1">
                    <h4 class="font-bold text-gray-900 text-sm md:text-base">Isi Alamat &amp; Hitung Ongkir</h4>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Masuk ke halaman checkout, isi alamat pengiriman lengkap Anda. Sistem kami terintegrasi dengan Biteship untuk menghitung ongkos kirim secara otomatis.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-4 relative">
                <div class="w-12 h-12 rounded-full bg-yellow-400 text-gray-900 font-black flex items-center justify-center shrink-0 z-10 shadow-sm text-sm border-4 border-white">4</div>
                <div class="pt-1">
                    <h4 class="font-bold text-gray-900 text-sm md:text-base">Lakukan Pembayaran &amp; Upload Bukti</h4>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Transfer total pembayaran sesuai rincian ke rekening yang tertera, lalu unggah bukti transfer di menu "Pesanan Saya" dalam waktu 24 jam agar pesanan tidak dibatalkan otomatis.</p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="flex gap-4 relative">
                <div class="w-12 h-12 rounded-full bg-yellow-400 text-gray-900 font-black flex items-center justify-center shrink-0 z-10 shadow-sm text-sm border-4 border-white">5</div>
                <div class="pt-1">
                    <h4 class="font-bold text-gray-900 text-sm md:text-base">Verifikasi &amp; Pengiriman</h4>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Admin akan memverifikasi bukti pembayaran Anda. Jika valid, admin akan memproses pengemasan, melakukan request pickup kurir, dan memperbarui nomor resi pengiriman.</p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
            <button type="button" @click="helpOpen = false" class="px-6 py-3 bg-gray-900 text-white font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-gray-800 transition">
                Mengerti
            </button>
        </div>
    </div>
</div>
