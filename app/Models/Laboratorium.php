<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
    use HasFactory;

    protected $table = 'laboratorium';
    protected $primaryKey = 'id_lab';
    protected $fillable = ['nama_lab']; // Hanya nama_lab

    // Hapus relasi penanggungJawab

    public function users()
    {
        return $this->hasMany(User::class, 'id_lab');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_lab');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_lab');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'id_lab');
    }

    public function peminjamanDetails()
    {
        return $this->hasMany(PeminjamanDetail::class, 'id_lab');
    }

    public function stokOpnames()
    {
        return $this->hasMany(StokOpname::class, 'id_lab');
    }

    public function stokOpnameDetails()
    {
        return $this->hasMany(StokOpnameDetail::class, 'id_lab');
    }

    public function riwayatStoks()
    {
        return $this->hasMany(RiwayatStok::class, 'id_lab');
    }

    public function riwayatKondisis()
    {
        return $this->hasMany(RiwayatKondisi::class, 'id_lab');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_lab');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'id_lab');
    }
}