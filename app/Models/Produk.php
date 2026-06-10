<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory;

    /** Batas stok rendah untuk produk fashion (stok minimal kisaran 2–6 pcs). */
    public const STOK_BATAS_RENDAH = 6;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $fillable = [
        'nama_produk',
        'harga',
        'size',
        'stok',
        'deskripsi',
        'gambar',
        'status_produk',
        'id_kategori',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status_produk', 'aktif');
    }

    public function scopeStokRendah($query)
    {
        return $query->where('stok', '<', self::STOK_BATAS_RENDAH);
    }

    public function isStokRendah(): bool
    {
        return $this->stok < self::STOK_BATAS_RENDAH;
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function keranjangDetails()
    {
        return $this->hasMany(KeranjangDetail::class, 'id_produk', 'id_produk');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (empty($this->gambar)) {
            return null;
        }

        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }

        if (Storage::disk('public')->exists('produk/' . $this->gambar)) {
            return route('produk.image', ['filename' => $this->gambar]);
        }

        if (file_exists(public_path('images/products/' . $this->gambar))) {
            return asset('images/products/' . $this->gambar);
        }

        return null;
    }
}
