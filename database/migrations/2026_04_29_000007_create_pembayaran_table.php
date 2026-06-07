<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_pesanan');
            $table->string('metode_pembayaran');
            $table->string('status_pembayaran')->default('pending');
            $table->timestamp('tanggal_pembayaran')->nullable();
            $table->timestamps();

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
