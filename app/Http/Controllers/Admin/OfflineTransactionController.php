<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfflineTransactionController extends Controller
{
    public function index()
    {
        $produk = Produk::where('status_produk', 'aktif')
            ->where('stok', '>', 0)
            ->with('kategori')
            ->latest()
            ->get();

        return view('admin.offline-transaction.index', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'qty' => 'required|integer|min:1',
            'no_hp' => 'nullable|string',
            'metode_pembayaran' => 'required|string|in:cash,transfer bank,qris,debit',
        ]);

        $produk = Produk::findOrFail($request->id_produk);
        
        if ($produk->stok < $request->qty) {
            \flash('Stok tidak mencukupi untuk transaksi ini.')->error();
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            // Jika id_user kosong, kita gunakan ID admin/kasir yang sedang login sebagai penanggung jawab
            // atau buat logic khusus untuk "Walking Customer". Untuk sekarang gunakan Auth::id().
            $pesanan = Pesanan::create([
                'id_user' => null, // Offline guest transaction
                'no_hp' => $request->no_hp ?? '-',
                'tanggal_pesanan' => now(),
                'total_harga' => $produk->harga * $request->qty,
                'status_pesanan' => 'selesai',
                'tipe_pesanan' => 'offline',
                'alamat_pengiriman' => 'Transaksi Toko Offline',
            ]);

            DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk' => $produk->id_produk,
                'harga' => $produk->harga,
                'qty' => $request->qty,
            ]);

            Pembayaran::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'berhasil',
                'tanggal_pembayaran' => now(),
            ]);

            $produk->stok -= $request->qty;
            $produk->save();

            DB::commit();
            \flash('Transaksi offline berhasil! Stok telah diperbarui.')->success();
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
