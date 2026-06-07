<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('stok');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->enum('status_produk', ['aktif', 'nonaktif'])->default('aktif');
            $table->unsignedBigInteger('id_kategori');
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
