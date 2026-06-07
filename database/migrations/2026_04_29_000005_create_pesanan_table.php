<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('tipe_pesanan')->default('online');
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->unsignedBigInteger('total_harga');
            $table->string('status_pesanan')->default('pending');
            $table->text('alamat_pengiriman')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
