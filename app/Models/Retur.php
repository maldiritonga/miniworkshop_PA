<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'alasan_retur',
        'foto_bukti',
        'alasan_penolakan',
        'status_retur',
        'nama_bank',
        'no_rekening',
        'nama_pemilik_rekening',
        'bukti_transfer',
    ];

    protected $casts = [
        'foto_bukti' => 'array',
    ];

    // Nilai enum sesuai database
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_MENUNGGU_REKENING = 'menunggu_rekening';
    const STATUS_MENUNGGU_BARANG = 'menunggu_barang';
    const STATUS_MENUNGGU_TRANSFER = 'menunggu_transfer';
    const STATUS_UANG_DITRANSFER = 'uang_ditransfer';
    const STATUS_SELESAI  = 'selesai';
    const STATUS_DITOLAK  = 'ditolak';

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function canPrintLabel(): bool
    {
        // Allow printing label after admin accepts the return (waiting for bank details) or when return is completed.
        return in_array($this->status_retur, [
            self::STATUS_MENUNGGU_REKENING, 
            self::STATUS_MENUNGGU_BARANG, 
            self::STATUS_MENUNGGU_TRANSFER, 
            self::STATUS_SELESAI, 
            self::STATUS_UANG_DITRANSFER
        ], true);
    }

    public static function statusBadge(string $status): array
    {
        return match ($status) {
            self::STATUS_DIAJUKAN => ['class' => 'bg-yellow-50 text-yellow-600', 'label' => 'Retur Diajukan'],
            self::STATUS_MENUNGGU_REKENING => ['class' => 'bg-indigo-50 text-indigo-600', 'label' => 'Menunggu Rekening'],
            self::STATUS_MENUNGGU_BARANG => ['class' => 'bg-purple-50 text-purple-600', 'label' => 'Menunggu Barang'],
            self::STATUS_MENUNGGU_TRANSFER => ['class' => 'bg-orange-50 text-orange-600', 'label' => 'Menunggu Transfer'],
            self::STATUS_UANG_DITRANSFER => ['class' => 'bg-blue-50 text-blue-600', 'label' => 'Uang Ditransfer'],
            self::STATUS_SELESAI  => ['class' => 'bg-green-50 text-green-600', 'label' => 'Retur Selesai'],
            self::STATUS_DITOLAK  => ['class' => 'bg-red-50 text-red-500', 'label' => 'Retur Ditolak'],
            default => ['class' => 'bg-gray-50 text-gray-500', 'label' => 'Retur'],
        };
    }
}
