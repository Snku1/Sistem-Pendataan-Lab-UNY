<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';
    protected $primaryKey = 'id_opname';

    protected $fillable = [
        'kode_opname', 
        'tanggal_opname', 
        'keterangan', 
        'status', 
        'id_user',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function details()
    {
        return $this->hasMany(StokOpnameDetail::class, 'id_opname');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_lab');
    }

    public static function generateKodeOpname()
    {
        $last = self::orderBy('id_opname', 'desc')->first();
        $lastNumber = $last ? intval(substr($last->kode_opname, -4)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'OPNAME/' . date('Y/m') . '/' . $newNumber;
    }
}