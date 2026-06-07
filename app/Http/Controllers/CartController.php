<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\KeranjangDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $keranjang = Keranjang::where('id_user', $user->id_user)->first();
        
        $items = [];
        if ($keranjang) {
            $items = KeranjangDetail::with('produk')->where('id_keranjang', $keranjang->id_keranjang)->get();
        }

        return view('pelanggan.keranjang.index', compact('items'));
    }

    public function add(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        $produk = Produk::findOrFail($id);

        // Cek stok
        if ($produk->stok <= 0) {
            \flash('Maaf, produk ini sedang habis dan tidak dapat ditambahkan ke keranjang.')->error();
            return redirect()->back();
        }

        // Cari atau buat keranjang untuk user
        $keranjang = Keranjang::firstOrCreate(['id_user' => $user->id_user]);

        // Cek apakah produk sudah ada di keranjang
        $detail = KeranjangDetail::where('id_keranjang', $keranjang->id_keranjang)
                                 ->where('id_produk', $produk->id_produk)
                                 ->first();

        if ($detail) {
            $detail->qty += 1;
            $detail->save();
        } else {
            KeranjangDetail::create([
                'id_keranjang' => $keranjang->id_keranjang,
                'id_produk' => $produk->id_produk,
                'harga' => $produk->harga,
                'qty' => 1,
            ]);
        }

        \flash('Berhasil ditambahkan ke keranjang')->success();

        return redirect()->back();
    }

    public function remove($id)
    {
        $detail = KeranjangDetail::findOrFail($id);
        $detail->delete();

        \flash('Produk berhasil dihapus dari keranjang')->info();

        return redirect()->back();
    }
}
