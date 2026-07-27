<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class StokController extends Controller
{
    // Halaman Produk Masuk — tambah stok
    public function masuk()
    {
        $produk = Produk::where('status_produk', 'aktif')->orderBy('nama_produk')->get();
        return view('admin.stok.masuk', compact('produk'));
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
            'keterangan'=> 'nullable|string|max:255',
        ], [
            'id_produk.required' => 'Pilih produk terlebih dahulu.',
            'jumlah.required'    => 'Jumlah wajib diisi.',
            'jumlah.min'         => 'Jumlah minimal 1.',
        ]);

        $produk = Produk::findOrFail($request->id_produk);
        $produk->increment('stok', $request->jumlah);

        \flash("Stok {$produk->nama_produk} berhasil ditambah sebanyak {$request->jumlah} pcs. Stok sekarang: {$produk->stok} pcs.")->success();
        return redirect()->route('admin.stok.masuk');
    }

    // Halaman Produk Keluar — kurangi stok (koreksi/retur ke supplier)
    public function keluar()
    {
        $produk = Produk::where('status_produk', 'aktif')->where('stok', '>', 0)->orderBy('nama_produk')->get();
        return view('admin.stok.keluar', compact('produk'));
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
            'keterangan'=> 'nullable|string|max:255',
        ], [
            'id_produk.required' => 'Pilih produk terlebih dahulu.',
            'jumlah.required'    => 'Jumlah wajib diisi.',
            'jumlah.min'         => 'Jumlah minimal 1.',
        ]);

        $produk = Produk::findOrFail($request->id_produk);

        if ($request->jumlah > $produk->stok) {
            \flash("Stok tidak cukup. Stok tersedia: {$produk->stok} pcs.")->error();
            return redirect()->back()->withInput();
        }

        $produk->decrement('stok', $request->jumlah);

        \flash("Stok {$produk->nama_produk} berhasil dikurangi sebanyak {$request->jumlah} pcs. Stok sekarang: {$produk->stok} pcs.")->success();
        return redirect()->route('admin.stok.keluar');
    }
}
