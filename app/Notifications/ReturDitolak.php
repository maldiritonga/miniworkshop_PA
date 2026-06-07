<?php

namespace App\Notifications;

use App\Models\Retur;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReturDitolak extends Notification
{
    use Queueable;

    public function __construct(public Retur $retur) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul'      => 'Pengajuan Retur Ditolak',
            'pesan'      => 'Pengajuan retur produk "' . $this->retur->produk->nama_produk . '" ditolak oleh toko. Alasan penolakan: ' . ($this->retur->alasan_penolakan ?? '-'),
            'id_retur'   => $this->retur->id_retur,
            'id_pesanan' => $this->retur->id_pesanan,
            'show_label' => false,
            'id_produk'  => $this->retur->id_produk,
            'url'        => route('pesanan.show', $this->retur->id_pesanan) . '#retur-produk-' . $this->retur->id_produk,
        ];
    }
}
