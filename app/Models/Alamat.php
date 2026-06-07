<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat';
    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_user',
        'label',
        'nama_penerima',
        'no_hp',
        'alamat_lengkap',
        'is_utama',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
