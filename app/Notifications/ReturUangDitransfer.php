<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReturUangDitransfer extends Notification
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
            'judul'      => 'Uang Retur Sudah Ditransfer',
            'pesan'      => 'Admin telah mentransfer uang pengembalian untuk retur produk Anda. Silakan cek dan konfirmasi penerimaan dana.',
            'id_pesanan' => $this->retur->id_pesanan,
            'id_produk'  => $this->retur->id_produk,
            'url'        => route('pesanan.show', $this->retur->id_pesanan),
        ];
    }
}
