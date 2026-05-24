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
        'nama', 'email', 'password', 'role', 'email_verified_at', 'id_lab'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User tidak perlu global scope, karena admin tidak difilter.
    // Tapi kita bisa menambahkan relasi dan helper

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

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_user');
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_lab');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKoorlap()
    {
        return $this->role === 'koorlap';
    }

    public function isTeknisi()
    {
        return $this->role === 'teknisi';
    }

    // Untuk kompatibilitas role lama
    public function isPetugas()
    {
        return $this->role === 'petugas' || $this->role === 'teknisi';
    }
}