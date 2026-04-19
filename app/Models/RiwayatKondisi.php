<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKondisi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kondisi';
    protected $fillable = [
        'id_barang', 'kondisi_lama', 'kondisi_baru', 'keterangan', 'id_user'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}