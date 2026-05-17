<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'nama', 'email', 'password', 'role', 'email_verified_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_user');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_user');
    }

    public function riwayatKondisi()
    {
        return $this->hasMany(RiwayatKondisi::class, 'id_user');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_user');
    }

    // Relasi ke peminjaman (jika foreign key di tabel peminjaman adalah 'id_user')
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_user');
    }

    // Helper method untuk pengecekan role (mempermudah di blade/controller)
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }
}