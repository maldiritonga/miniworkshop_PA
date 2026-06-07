<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori')->latest();

        if ($request->has('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $produk = $query->paginate(10);
        return view('admin.produk.index', compact('produk'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.produk.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'size' => 'nullable|string|max:10',
            'stok' => 'required|integer|min:0',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        
        // Status logic based on stock
        $data['status_produk'] = $request->stok > 0 ? 'aktif' : 'nonaktif';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['gambar'] = $filename;
        }

        Produk::create($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $kategori = Kategori::all();
        return view('admin.produk.edit', compact('produk', 'kategori'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'size' => 'nullable|string|max:10',
            'stok' => 'required|integer|min:0',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        
        // Status logic based on stock
        $data['status_produk'] = $request->stok > 0 ? 'aktif' : 'nonaktif';

        if ($request->hasFile('gambar')) {
            if ($produk->gambar && file_exists(public_path('images/products/' . $produk->gambar))) {
                unlink(public_path('images/products/' . $produk->gambar));
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['gambar'] = $filename;
        }

        $produk->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        if ($produk->gambar && file_exists(public_path('images/products/' . $produk->gambar))) {
            unlink(public_path('images/products/' . $produk->gambar));
        }
        $produk->delete();
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
