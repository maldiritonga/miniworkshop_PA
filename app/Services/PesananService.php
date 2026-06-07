<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class PesananService
{
    public function cancelAllExpired(): int
    {
        $count = 0;

        Pesanan::with(['detail.produk', 'pembayaran'])
            ->where('status_pesanan', 'menunggu_pembayaran')
            ->where('tipe_pesanan', 'online')
            ->where(function ($query) {
                $query->where('batas_pembayaran_at', '<=', now())
                    ->orWhere(function ($fallback) {
                        $fallback->whereNull('batas_pembayaran_at')
                            ->where('created_at', '<=', now()->subHours(Pesanan::BATAS_PEMBAYARAN_JAM));
                    });
            })
            ->orderBy('id_pesanan')
            ->each(function (Pesanan $pesanan) use (&$count) {
                if ($this->cancelIfExpired($pesanan)) {
                    $count++;
                }
            });

        return $count;
    }

    public function cancelIfExpired(Pesanan $pesanan): bool
    {
        if ($pesanan->status_pesanan !== 'menunggu_pembayaran') {
            return false;
        }

        if ($pesanan->pembayaran?->status_pembayaran === 'menunggu_konfirmasi') {
            return false;
        }

        if (! $pesanan->isPaymentExpired()) {
            return false;
        }

        return $this->batalkanDanKembalikanStok($pesanan);
    }

    public function batalkanDanKembalikanStok(Pesanan $pesanan): bool
    {
        if ($pesanan->status_pesanan !== 'menunggu_pembayaran') {
            return false;
        }

        if ($pesanan->pembayaran?->status_pembayaran === 'menunggu_konfirmasi') {
            return false;
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->loadMissing('detail.produk');

            foreach ($pesanan->detail as $item) {
                if (! $item->produk) {
                    continue;
                }

                $item->produk->increment('stok', $item->qty);
                $this->syncProdukStatus($item->produk->fresh());
            }

            $pesanan->update(['status_pesanan' => 'dibatalkan']);
        });

        return true;
    }

    public function syncProdukStatus(Produk $produk): void
    {
        $produk->update([
            'status_produk' => $produk->stok > 0 ? 'aktif' : 'nonaktif',
        ]);
    }
}
