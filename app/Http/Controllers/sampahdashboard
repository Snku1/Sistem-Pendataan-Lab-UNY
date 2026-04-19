<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalBarang = Barang::sum('stok');
        $barangBaik = Barang::where('kondisi', 'baik')->sum('stok');
        $barangRusak = Barang::where('kondisi', 'rusak')->sum('stok');
        $barangHilang = Barang::where('kondisi', 'hilang')->sum('stok');
        
        // Data untuk grafik (barang masuk per bulan - 6 bulan terakhir)
        $bulanLabels = [];
        $bulanData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $bulanLabels[] = $bulan->format('M Y');
            $jumlah = BarangMasuk::whereYear('tanggal_masuk', $bulan->year)
                ->whereMonth('tanggal_masuk', $bulan->month)
                ->sum('jumlah_masuk');
            $bulanData[] = $jumlah;
        }
        
        // Barang dengan stok menipis (stok <= 2)
        $stokMenipis = Barang::where('stok', '<=', 2)->where('stok', '>', 0)->get();
        
        return view('dashboard', compact(
            'totalBarang', 'barangBaik', 'barangRusak', 'barangHilang',
            'bulanLabels', 'bulanData', 'stokMenipis'
        ));
    }
}