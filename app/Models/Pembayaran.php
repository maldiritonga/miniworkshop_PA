<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'id_pesanan',
        'metode_pembayaran',
        'bank_tujuan',
        'status_pembayaran',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
