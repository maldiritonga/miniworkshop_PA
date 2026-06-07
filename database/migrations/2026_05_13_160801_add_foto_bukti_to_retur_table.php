<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            // Menyimpan nama file foto sebagai JSON array (maks 3 foto)
            $table->json('foto_bukti')->nullable()->after('alasan_retur');
        });
    }

    public function down(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });
    }
};
