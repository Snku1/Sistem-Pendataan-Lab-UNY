<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==================== STATISTIK UTAMA ====================
        $totalBarang = Barang::sum('stok');
        $barangBaik = Barang::where('kondisi', 'baik')->sum('stok');
        $barangRusak = Barang::where('kondisi', 'rusak')->sum('stok');
        $barangHilang = Barang::where('kondisi', 'hilang')->sum('stok');
        
        // ==================== GRAFIK INVENTARIS PER SEMESTER ====================
        $semesterLabels = ['Semester Ganjil 2024/2025', 'Semester Genap 2024/2025', 'Semester Ganjil 2025/2026', 'Semester Genap 2025/2026'];
        
        // Data dari database (barang masuk per semester)
        $semesterData = [
            BarangMasuk::whereBetween('tanggal_masuk', ['2024-07-01', '2024-12-31'])->sum('jumlah_masuk'),
            BarangMasuk::whereBetween('tanggal_masuk', ['2025-01-01', '2025-06-30'])->sum('jumlah_masuk'),
            BarangMasuk::whereBetween('tanggal_masuk', ['2025-07-01', '2025-12-31'])->sum('jumlah_masuk'),
            BarangMasuk::whereBetween('tanggal_masuk', ['2026-01-01', '2026-06-30'])->sum('jumlah_masuk'),
        ];
        
        // ==================== GRAFIK BARANG MASUK (6 BULAN TERAKHIR) ====================
        $bulanLabels = [];
        $bulanData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $bulanLabels[] = $bulan->format('M');
            $jumlah = BarangMasuk::whereYear('tanggal_masuk', $bulan->year)
                ->whereMonth('tanggal_masuk', $bulan->month)
                ->sum('jumlah_masuk');
            $bulanData[] = $jumlah;
        }
        
        // ==================== GRAFIK PENGGUNAAN PRAKTIKUM ====================
        // Data dari tabel barang (nama barang tertentu)
        $praktikumLabels = ['Kamera Video', 'Mikroskop', 'Speaker'];
        $praktikumDigunakan = [
            Barang::where('nama_barang', 'like', '%Kamera Video%')->sum('stok') * 0.8,
            Barang::where('nama_barang', 'like', '%Tripod%')->sum('stok') * 0.7,
            Barang::where('nama_barang', 'like', '%Speaker%')->sum('stok') * 0.6,
        ];
        $praktikumKapasitas = [
            Barang::where('nama_barang', 'like', '%Kamera Video%')->sum('stok'),
            Barang::where('nama_barang', 'like', '%Tripod%')->sum('stok'),
            Barang::where('nama_barang', 'like', '%Speaker%')->sum('stok'),
        ];
        
        // ==================== QUICK NOTIFICATIONS ====================
        // 1. Barang stok menipis (stok <= 3)
        $stokMenipis = Barang::where('stok', '<=', 3)->where('stok', '>', 0)->limit(5)->get();
        
        // 2. Barang rusak terbaru
        $barangRusakTerbaru = Barang::where('kondisi', 'rusak')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
        
        // 3. Barang masuk terbaru
        $barangMasukTerbaru = BarangMasuk::with('barang')
            ->orderBy('tanggal_masuk', 'desc')
            ->limit(5)
            ->get();
        
        // ==================== RECENT ACTIVITY ====================
        $recentActivities = LogAktivitas::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact(
            'totalBarang',
            'barangBaik',
            'barangRusak',
            'barangHilang',
            'semesterLabels',
            'semesterData',
            'bulanLabels',
            'bulanData',
            'praktikumLabels',
            'praktikumDigunakan',
            'praktikumKapasitas',
            'stokMenipis',
            'barangRusakTerbaru',
            'barangMasukTerbaru',
            'recentActivities'
        ));
    }
}