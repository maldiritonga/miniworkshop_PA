<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->text('alasan_retur')->nullable();
            $table->enum('status_retur', ['diajukan', 'diproses', 'ditolak', 'selesai'])->default('diajukan')->nullable();
            $table->timestamps();

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->cascadeOnDelete();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
