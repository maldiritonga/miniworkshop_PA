<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function image(string $filename)
    {
        $storagePath = 'produk/' . $filename;

        if (Storage::disk('public')->exists($storagePath)) {
            return response()->file(storage_path('app/public/' . $storagePath));
        }

        $legacyPath = public_path('images/products/' . $filename);

        if (file_exists($legacyPath)) {
            return response()->file($legacyPath);
        }

        abort(404);
    }

    public function show($slug)
    {
        $produk = Produk::with('kategori')->where('slug', $slug)->firstOrFail();
        return view('pelanggan.produk.show', compact('produk'));
    }
}
