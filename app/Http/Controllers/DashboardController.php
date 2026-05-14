<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==================== STATISTIK UTAMA ====================
        $totalBarang = Barang::sum('stok');
        $barangBaik = Barang::sum('jumlah_baik');
        $barangRusak = Barang::sum('jumlah_rusak');
        $barangHilang = Barang::sum('jumlah_hilang');

        // ==================== STATISTIK TAMBAHAN ====================
        // Total transaksi peminjaman selesai (status_transaksi = 'selesai')
        $totalPeminjamanSelesai = Peminjaman::where('status_transaksi', 'selesai')->count();
        // Total barang sedang dipinjam (status_item = 'dipinjam')
        $totalBarangSedangDipinjam = PeminjamanDetail::where('status_item', 'dipinjam')->sum('jumlah');
        // Barang masuk yang masih menunggu konfirmasi
        $barangMasukMenunggu = BarangMasuk::where('status', 'menunggu')->count();

        // ==================== GRAFIK INVENTARIS PER SEMESTER ====================
        $semesterLabels = ['Semester Ganjil 2024/2025', 'Semester Genap 2024/2025', 'Semester Ganjil 2025/2026', 'Semester Genap 2025/2026'];
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
            $bulanLabels[] = $bulan->format('M Y');
            $jumlah = BarangMasuk::whereYear('tanggal_masuk', $bulan->year)
                ->whereMonth('tanggal_masuk', $bulan->month)
                ->sum('jumlah_masuk');
            $bulanData[] = $jumlah;
        }

        // ==================== GRAFIK AKTIVITAS PEMINJAMAN (per bulan) ====================
        $peminjamanLabels = [];
        $peminjamanData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $peminjamanLabels[] = $bulan->format('M Y');
            $jumlah = Peminjaman::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->count(); // jumlah transaksi peminjaman per bulan
            $peminjamanData[] = $jumlah;
        }

        // ==================== NOTIFIKASI & PEMINJAMAN JATUH TEMPO ====================
        $stokMenipis = Barang::where('stok', '<=', 3)->where('stok', '>', 0)->limit(5)->get();
        $barangRusakTerbaru = Barang::where('jumlah_rusak', '>', 0)->orderBy('updated_at', 'desc')->limit(3)->get();
        $barangMasukTerbaru = BarangMasuk::with('barang')->orderBy('tanggal_masuk', 'desc')->limit(5)->get();
        $peminjamanJatuhTempo = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(3)])
            ->limit(3)
            ->get();

        // ==================== RECENT ACTIVITY ====================
        $recentActivities = LogAktivitas::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('dashboard', compact(
            'totalBarang', 'barangBaik', 'barangRusak', 'barangHilang',
            'totalPeminjamanSelesai', 'totalBarangSedangDipinjam', 'barangMasukMenunggu',
            'semesterLabels', 'semesterData',
            'bulanLabels', 'bulanData',
            'peminjamanLabels', 'peminjamanData',
            'stokMenipis', 'barangRusakTerbaru', 'barangMasukTerbaru',
            'peminjamanJatuhTempo', 'recentActivities'
        ));
    }
}