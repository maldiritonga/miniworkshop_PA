<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum: tambah status 'diterima' di antara 'diproses' dan 'selesai'
        DB::statement("ALTER TABLE `retur` MODIFY `status_retur` ENUM('diajukan','diproses','diterima','selesai','ditolak') NULL DEFAULT 'diajukan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `retur` MODIFY `status_retur` ENUM('diajukan','diproses','ditolak','selesai') NULL DEFAULT 'diajukan'");
    }
};
