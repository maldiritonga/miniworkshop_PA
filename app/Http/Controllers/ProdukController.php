<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function show($id)
    {
        $produk = Produk::with('kategori')->findOrFail($id);
        return view('pelanggan.produk.show', compact('produk'));
    }
}
