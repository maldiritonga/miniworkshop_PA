<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->timestamp('batas_pembayaran_at')->nullable()->after('status_pesanan');
        });

        $pesananMenunggu = DB::table('pesanan')
            ->where('status_pesanan', 'menunggu_pembayaran')
            ->whereNull('batas_pembayaran_at')
            ->get(['id_pesanan', 'created_at']);

        foreach ($pesananMenunggu as $pesanan) {
            DB::table('pesanan')
                ->where('id_pesanan', $pesanan->id_pesanan)
                ->update([
                    'batas_pembayaran_at' => \Carbon\Carbon::parse($pesanan->created_at)->addHours(24),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('batas_pembayaran_at');
        });
    }
};
