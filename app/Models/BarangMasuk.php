<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';

    // app/Models/BarangMasuk.php

    protected $fillable = [
        'id_barang',
        'jumlah_masuk',
        'tanggal_masuk',
        'semester',
        'sumber',
        'id_user',
        'status',
        'bukti_foto',
        'id_penanggung_jawab',
        'kondisi_penerimaan',
        'catatan_pemeriksaan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke penanggung jawab (pemeriksa)
    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawab::class, 'id_penanggung_jawab');
    }
}
