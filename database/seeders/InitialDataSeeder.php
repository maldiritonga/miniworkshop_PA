<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kategori dasar
        $kategoriBaju   = Kategori::firstOrCreate(['nama_kategori' => 'Baju']);
        $kategoriCelana = Kategori::firstOrCreate(['nama_kategori' => 'Celana']);
        $kategoriSepatu = Kategori::firstOrCreate(['nama_kategori' => 'Sepatu']);


        // Buat user admin jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // Buat produk dasar jika belum ada
        Produk::firstOrCreate(
            ['nama_produk' => 'Kaos Polos Hitam'],
            [
                'harga' => 50000,
                'stok' => 20,
                'deskripsi' => 'Kaos polos berkualitas tinggi warna hitam',
                'status_produk' => 'aktif',
                'id_kategori' => $kategoriKaos->id_kategori,
                'size' => 'M'
            ]
        );

        Produk::firstOrCreate(
            ['nama_produk' => 'Celana Jeans'],
            [
                'harga' => 150000,
                'stok' => 10,
                'deskripsi' => 'Celana jeans premium',
                'status_produk' => 'aktif',
                'id_kategori' => $kategoriCelana->id_kategori,
                'size' => '32'
            ]
        );

        Produk::firstOrCreate(
            ['nama_produk' => 'Jaket Hoodie'],
            [
                'harga' => 200000,
                'stok' => 5,
                'deskripsi' => 'Jaket hoodie nyaman untuk sehari-hari',
                'status_produk' => 'aktif',
                'id_kategori' => $kategoriJaket->id_kategori,
                'size' => 'L'
            ]
        );
    }
}
