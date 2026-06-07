<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kategori dulu
        $kategori1 = Kategori::create(['nama_kategori' => 'Kaos']);
        $kategori2 = Kategori::create(['nama_kategori' => 'Celana']);

        // Buat produk
        Produk::create([
            'nama_produk' => 'Kaos Polos Hitam',
            'harga' => 50000,
            'stok' => 10,
            'deskripsi' => 'Kaos polos berkualitas tinggi',
            'status_produk' => 'aktif',
            'id_kategori' => $kategori1->id_kategori,
            'size' => 'M',
        ]);

        Produk::create([
            'nama_produk' => 'Celana Jeans',
            'harga' => 150000,
            'stok' => 5,
            'deskripsi' => 'Celana jeans premium',
            'status_produk' => 'aktif',
            'id_kategori' => $kategori2->id_kategori,
            'size' => '32',
        ]);
    }
}
