<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PeminjamanDetail;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'kode_transaksi',
        'nama_peminjam',
        'nim',
        'email',
        'tanggal_penggunaan',
        'tanggal_jatuh_tempo',
        'surat_peminjaman',
        'catatan_awal',
        'status_transaksi',
        'id_user',
        'id_semester',
        'last_overdue_reminder_date',  // <-- Tambahan untuk pencatatan pengingat overdue
    ];

    protected $casts = [
        'tanggal_penggunaan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'last_overdue_reminder_date' => 'date', // <-- Cast ke date
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function details()
    {
        return $this->hasMany(PeminjamanDetail::class, 'id_peminjaman');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    public static function generateKodeTransaksi()
    {
        $last = self::orderBy('id_peminjaman', 'desc')->first();
        $lastNumber = $last ? intval(substr($last->kode_transaksi, -4)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'TRX/' . date('Y/m') . '/' . $newNumber;
    }
}