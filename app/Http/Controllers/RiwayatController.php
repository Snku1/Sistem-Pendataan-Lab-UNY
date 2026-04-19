<?php

namespace App\Http\Controllers;

use App\Models\RiwayatStok;
use App\Models\RiwayatKondisi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Riwayat perubahan stok
     */
    public function stok()
    {
        $riwayat = RiwayatStok::with(['barang', 'user'])->orderBy('created_at', 'desc')->paginate(20);
        return view('riwayat.stok', compact('riwayat'));
    }
    
    /**
     * Riwayat perubahan kondisi
     */
    public function kondisi()
    {
        $riwayat = RiwayatKondisi::with(['barang', 'user'])->orderBy('created_at', 'desc')->paginate(20);
        return view('riwayat.kondisi', compact('riwayat'));
    }
    
    /**
     * Riwayat aktivitas sistem
     */
    public function aktivitas()
    {
        $logs = LogAktivitas::with('user')->orderBy('created_at', 'desc')->paginate(30);
        return view('riwayat.aktivitas', compact('logs'));
    }
}