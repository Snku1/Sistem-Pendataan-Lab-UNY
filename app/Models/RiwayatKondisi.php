<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RiwayatKondisi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kondisi';
    protected $fillable = [
        'id_barang', 'kondisi_lama', 'kondisi_baru', 'keterangan', 'id_user', 'id_lab'
    ];

    protected static function booted()
    {
        static::addGlobalScope('laboratorium', function ($builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('id_lab', Auth::user()->id_lab);
            }
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_lab');
    }
}