<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail_pesanan');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('qty');
            $table->timestamps();

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->cascadeOnDelete();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
