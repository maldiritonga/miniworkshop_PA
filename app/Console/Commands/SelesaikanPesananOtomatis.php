<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SelesaikanPesananOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:selesaikan-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah pesanan menjadi selesai setelah 2x24 jam dari waktu diantar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batasWaktu = Carbon::now()->subHours(48);

        $pesanans = Pesanan::where('status_pesanan', 'diantar')
            ->whereNotNull('pesanan_diantar_at')
            ->where('pesanan_diantar_at', '<=', $batasWaktu)
            ->get();

        if ($pesanans->isEmpty()) {
            $this->info('Tidak ada pesanan yang perlu diselesaikan secara otomatis.');
            return;
        }

        foreach ($pesanans as $pesanan) {
            $pesanan->update([
                'status_pesanan' => 'selesai',
            ]);
            Log::info("Pesanan ID {$pesanan->id_pesanan} otomatis diselesaikan setelah melewati 2x24 jam.");
        }

        $this->info("Berhasil menyelesaikan {$pesanans->count()} pesanan secara otomatis.");
    }
}
