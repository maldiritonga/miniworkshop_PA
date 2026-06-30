<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    public const BATAS_PEMBAYARAN_JAM = 24;

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    protected $fillable = [
        'id_user',
        'no_hp',
        'tanggal_pesanan',
        'total_harga',
        'status_pesanan',
        'batas_pembayaran_at',
        'tipe_pesanan',
        'alamat_pengiriman',
        'catatan',
        'kurir',
        'resi',
        'biteship_order_id',
        'pesanan_diantar_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesanan' => 'datetime',
            'batas_pembayaran_at' => 'datetime',
            'pesanan_diantar_at' => 'datetime',
        ];
    }

    public function batasPembayaran(): Carbon
    {
        return $this->batas_pembayaran_at
            ?? $this->created_at->copy()->addHours(self::BATAS_PEMBAYARAN_JAM);
    }

    public function isPaymentExpired(): bool
    {
        return $this->status_pesanan === 'menunggu_pembayaran'
            && now()->gte($this->batasPembayaran());
    }

    public function canUploadBuktiPembayaran(): bool
    {
        if ($this->status_pesanan !== 'menunggu_pembayaran' || $this->isPaymentExpired()) {
            return false;
        }

        return in_array($this->pembayaran?->status_pembayaran, ['belum_dibayar', 'ditolak'], true);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function retur()
    {
        return $this->hasMany(Retur::class, 'id_pesanan', 'id_pesanan');
    }
}
