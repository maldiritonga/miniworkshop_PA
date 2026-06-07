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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('bank_tujuan')->nullable()->after('metode_pembayaran');
            $table->string('bukti_pembayaran')->nullable()->after('tanggal_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['bank_tujuan', 'bukti_pembayaran']);
        });
    }
};
