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
        Schema::table('produk', function (Blueprint $table) {
            $table->integer('diskon_persen')->default(0)->after('harga');
            $table->dateTime('diskon_mulai')->nullable()->after('diskon_persen');
            $table->dateTime('diskon_selesai')->nullable()->after('diskon_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['diskon_persen', 'diskon_mulai', 'diskon_selesai']);
        });
    }
};
