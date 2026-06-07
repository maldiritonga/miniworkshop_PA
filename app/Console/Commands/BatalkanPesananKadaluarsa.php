<?php

namespace App\Console\Commands;

use App\Services\PesananService;
use Illuminate\Console\Command;

class BatalkanPesananKadaluarsa extends Command
{
    protected $signature = 'pesanan:batalkan-kadaluarsa';

    protected $description = 'Batalkan pesanan menunggu pembayaran yang sudah melewati batas 24 jam dan kembalikan stok';

    public function handle(PesananService $pesananService): int
    {
        $count = $pesananService->cancelAllExpired();

        $this->info("Pesanan kadaluarsa dibatalkan: {$count}");

        return self::SUCCESS;
    }
}
