<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenanggungJawab extends Model
{
    use HasFactory;

    protected $table = 'penanggung_jawab';
    protected $primaryKey = 'id_pj';
    protected $fillable = ['nama_pj', 'no_kontak', 'email'];

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'barang_penanggungjawab', 'id_pj', 'id_barang');
    }
}