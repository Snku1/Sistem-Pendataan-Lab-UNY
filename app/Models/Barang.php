<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'merk',
        'deskripsi',
        'kapasitas',
        'id_lokasi',
        'stok',
        'jumlah_baik',
        'jumlah_rusak',
        'jumlah_hilang',
        'keterangan',
        'semester',
        'tahun_ajaran'
    ];

    // Accessor untuk stok baik
    public function getStokBaikAttribute()
    {
        return $this->stok - ($this->jumlah_rusak ?? 0) - ($this->jumlah_hilang ?? 0);
    }


    // Relasi (tetap sama)
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function penanggungJawab()
    {
        return $this->belongsToMany(PenanggungJawab::class, 'barang_penanggungjawab', 'id_barang', 'id_pj');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_barang');
    }

    public function riwayatKondisi()
    {
        return $this->hasMany(RiwayatKondisi::class, 'id_barang');
    } 
}
