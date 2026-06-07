<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang_detail', function (Blueprint $table) {
            $table->id('id_keranjang_detail');
            $table->unsignedBigInteger('id_keranjang');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('qty');
            $table->timestamps();

            $table->foreign('id_keranjang')->references('id_keranjang')->on('keranjang')->cascadeOnDelete();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang_detail');
    }
};
