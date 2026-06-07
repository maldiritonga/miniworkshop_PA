<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('status_pesanan')->default('menunggu_pembayaran')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('status_pesanan', ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('pending')->change();
        });
    }
};
