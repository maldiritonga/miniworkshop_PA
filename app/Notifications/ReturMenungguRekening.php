<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReturMenungguRekening extends Notification
{
    use Queueable;

    protected $retur;

    public function __construct($retur)
    {
        $this->retur = $retur;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'judul'      => 'Retur Diterima – Masukkan Nomor Rekening',
            'pesan'      => 'Pengajuan retur Anda telah diterima. Mohon masukkan nomor rekening bank Anda untuk proses pengembalian dana.',
            'id_pesanan' => $this->retur->id_pesanan,
            'id_produk'  => $this->retur->id_produk,
            'url'        => route('pesanan.show', $this->retur->id_pesanan),
        ];
    }
}
