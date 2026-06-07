<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            $table->string('nama_bank')->nullable()->after('alasan_penolakan');
            $table->string('no_rekening')->nullable()->after('nama_bank');
            $table->string('nama_pemilik_rekening')->nullable()->after('no_rekening');
            $table->string('bukti_transfer')->nullable()->after('nama_pemilik_rekening');
        });

        // Update the enum values using raw statement since altering enum can be tricky with Schema builder
        DB::statement("ALTER TABLE `retur` MODIFY `status_retur` ENUM('diajukan', 'menunggu_rekening', 'menunggu_barang', 'menunggu_transfer', 'uang_ditransfer', 'selesai', 'ditolak') NULL DEFAULT 'diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'no_rekening', 'nama_pemilik_rekening', 'bukti_transfer']);
        });

        // Revert enum
        DB::statement("ALTER TABLE `retur` MODIFY `status_retur` ENUM('diajukan','diproses','diterima','selesai','ditolak') NULL DEFAULT 'diajukan'");
    }
};
