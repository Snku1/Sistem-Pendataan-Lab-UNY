<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StokOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'stok_opname_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_opname', 
        'id_barang', 
        'stok_sistem', 
        'stok_fisik', 
        'selisih', 
        'keterangan',
        'catatan',
        'id_semester',
        'id_lab'
    ];

    protected static function booted()
    {
        static::addGlobalScope('laboratorium', function ($builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('id_lab', Auth::user()->id_lab);
            }
        });
    }

    public function opname()
    {
        return $this->belongsTo(StokOpname::class, 'id_opname');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_lab');
    }
}