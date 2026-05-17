<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Semester;
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
        $totalPeminjamanSelesai = Peminjaman::where('status_transaksi', 'selesai')->count();
        $totalBarangSedangDipinjam = PeminjamanDetail::where('status_item', 'dipinjam')->sum('jumlah');
        $barangMasukMenunggu = BarangMasuk::where('status', 'menunggu')->count();

        // ==================== GRAFIK INVENTARIS PER SEMESTER ====================
        // Ambil semua semester, urutkan berdasarkan tahun ajaran dan jenis semester
        $semesters = Semester::orderBy('tahun_ajaran', 'asc')
            ->orderBy('nama_semester', 'asc')
            ->get();

        $semesterLabels = [];
        $semesterBaik = [];
        $semesterRusak = [];
        $semesterHilang = [];

        foreach ($semesters as $sem) {
            $semesterLabels[] = $sem->nama_semester . ' ' . $sem->tahun_ajaran;

            // Hitung total stok kondisi baik, rusak, hilang untuk semester tersebut
            $baik = Barang::where('id_semester', $sem->id_semester)->sum('jumlah_baik');
            $rusak = Barang::where('id_semester', $sem->id_semester)->sum('jumlah_rusak');
            $hilang = Barang::where('id_semester', $sem->id_semester)->sum('jumlah_hilang');

            $semesterBaik[] = $baik;
            $semesterRusak[] = $rusak;
            $semesterHilang[] = $hilang;
        }

        // ==================== GRAFIK BARANG MASUK (6 BULAN TERAKHIR) ====================
        $bulanLabels = [];
        $bulanData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $bulanLabels[] = $bulan->translatedFormat('M Y');
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
            $peminjamanLabels[] = $bulan->translatedFormat('M Y');
            $jumlah = Peminjaman::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->count();
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
            'semesterLabels', 'semesterBaik', 'semesterRusak', 'semesterHilang',
            'bulanLabels', 'bulanData',
            'peminjamanLabels', 'peminjamanData',
            'stokMenipis', 'barangRusakTerbaru', 'barangMasukTerbaru',
            'peminjamanJatuhTempo', 'recentActivities'
        ));
    }
}