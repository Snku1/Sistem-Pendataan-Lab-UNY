<?php

namespace App\Http\Controllers;

use App\Models\RiwayatStok;
use App\Models\LogAktivitas;
use App\Models\BarangMasuk;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    /**
     * Riwayat perubahan stok (halaman terpisah)
     */
    public function stok(Request $request)
    {
        $query = RiwayatStok::with(['barang', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_perubahan')) {
            $query->where('jenis_perubahan', $request->jenis_perubahan);
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $riwayat = $query->paginate(20)->withQueryString();
        $jenisList = RiwayatStok::select('jenis_perubahan')->distinct()->pluck('jenis_perubahan');

        return view('riwayat.stok', compact('riwayat', 'jenisList'));
    }

    /**
     * Riwayat aktivitas sistem (tab: Barang Masuk, Stok, Log Aktivitas)
     */
    public function aktivitas(Request $request)
    {
        // --- RIWAYAT BARANG MASUK ---
        $queryBarangMasuk = BarangMasuk::with(['barang', 'user', 'semester'])
            ->where('status', 'diterima')
            ->orderBy('tanggal_masuk', 'desc');

        if ($request->filled('search_bm')) {
            $search = $request->search_bm;
            $queryBarangMasuk->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tanggal_awal_bm')) {
            $queryBarangMasuk->whereDate('tanggal_masuk', '>=', $request->tanggal_awal_bm);
        }
        if ($request->filled('tanggal_akhir_bm')) {
            $queryBarangMasuk->whereDate('tanggal_masuk', '<=', $request->tanggal_akhir_bm);
        }
        if ($request->filled('id_semester_bm')) {
            $queryBarangMasuk->where('id_semester', $request->id_semester_bm);
        }
        $barangMasuk = $queryBarangMasuk->paginate(15)->withQueryString();
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('nama_semester', 'desc')->get();

        // --- RIWAYAT STOK ---
        $queryStok = RiwayatStok::with(['barang', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('search_stok')) {
            $search = $request->search_stok;
            $queryStok->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }
        if ($request->filled('jenis_perubahan')) {
            $queryStok->where('jenis_perubahan', $request->jenis_perubahan);
        }
        if ($request->filled('tanggal_awal_stok')) {
            $queryStok->whereDate('created_at', '>=', $request->tanggal_awal_stok);
        }
        if ($request->filled('tanggal_akhir_stok')) {
            $queryStok->whereDate('created_at', '<=', $request->tanggal_akhir_stok);
        }
        $riwayatStok = $queryStok->paginate(15)->withQueryString();
        $jenisList = RiwayatStok::select('jenis_perubahan')->distinct()->pluck('jenis_perubahan');

        // --- LOG AKTIVITAS ---
        $queryLog = LogAktivitas::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search_log')) {
            $search = $request->search_log;
            $queryLog->where(function ($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tanggal_awal_log')) {
            $queryLog->whereDate('created_at', '>=', $request->tanggal_awal_log);
        }
        if ($request->filled('tanggal_akhir_log')) {
            $queryLog->whereDate('created_at', '<=', $request->tanggal_akhir_log);
        }
        if ($request->filled('id_user')) {
            $queryLog->where('id_user', $request->id_user);
        }
        if ($request->filled('aktivitas')) {
            $queryLog->where('aktivitas', $request->aktivitas);
        }
        $logs = $queryLog->paginate(15)->withQueryString();

        // --- STATISTIK CEPAT ---
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $totalHariIni   = LogAktivitas::whereDate('created_at', $today)->count();
        $totalMingguIni = LogAktivitas::whereDate('created_at', '>=', $weekStart)->count();
        $totalBulanIni  = LogAktivitas::whereDate('created_at', '>=', $monthStart)->count();
        $totalUserAktif = LogAktivitas::distinct('id_user')->count('id_user');

        $userList = User::orderBy('nama')->get();
        $jenisAktivitasList = LogAktivitas::select('aktivitas')->distinct()->pluck('aktivitas');

        $tab = $request->get('tab', 'barang-masuk');

        return view('riwayat.aktivitas', compact(
            'barangMasuk', 'semesterList',
            'riwayatStok', 'jenisList',
            'logs',
            'totalHariIni', 'totalMingguIni', 'totalBulanIni', 'totalUserAktif',
            'userList', 'jenisAktivitasList',
            'tab'
        ));
    }

    /**
     * Ekspor riwayat aktivitas ke CSV
     */
    public function exportCsv(Request $request)
    {
        $query = LogAktivitas::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }
        if ($request->filled('aktivitas')) {
            $query->where('aktivitas', $request->aktivitas);
        }

        $logs = $query->get();
        $filename = 'riwayat_aktivitas_' . date('YmdHis') . '.csv';

        $headers = ['Waktu', 'User', 'Email', 'Aktivitas', 'Deskripsi'];
        $callback = function() use ($logs, $headers) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at,
                    $log->user->nama ?? 'Sistem',
                    $log->user->email ?? '-',
                    $log->aktivitas,
                    $log->deskripsi ?? ''
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}