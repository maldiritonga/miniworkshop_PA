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
            $table->string('kurir')->nullable()->after('alamat_pengiriman');
            $table->string('resi')->nullable()->after('kurir');
            $table->string('biteship_order_id')->nullable()->after('resi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['kurir', 'resi', 'biteship_order_id']);
        });
    }
};
