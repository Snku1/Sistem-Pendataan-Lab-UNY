<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';
    protected $primaryKey = 'id_semester';
    protected $fillable = ['nama_semester', 'tahun_ajaran', 'is_active', 'tanggal_mulai', 'tanggal_selesai'];

    // Relasi ke tabel transaksi
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_semester');
    }
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_semester');
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_semester');
    }
    public function peminjamanDetail()
    {
        return $this->hasMany(PeminjamanDetail::class, 'id_semester');
    }
    public function stokOpname()
    {
        return $this->hasMany(StokOpname::class, 'id_semester');
    }
    public function stokOpnameDetail()
    {
        return $this->hasMany(StokOpnameDetail::class, 'id_semester');
    }
    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_semester');
    }
}