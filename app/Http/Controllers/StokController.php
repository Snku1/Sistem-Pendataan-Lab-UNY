<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PeminjamanDetail;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StokController extends Controller
{
    /**
     * Ambil objek semester aktif dari session.
     * @return Semester|null (null jika "Semua Semester" atau belum ada)
     */
    private function getActiveSemester()
    {
        $activeId = session('active_semester_id');
        if (!$activeId || $activeId == 0) {
            return null;
        }
        return Semester::find($activeId);
    }

    public function index(Request $request)
    {
        $activeSemester = $this->getActiveSemester();

        // Tentukan rentang tanggal default
        if ($activeSemester) {
            $defaultStart = $activeSemester->tanggal_mulai ?? date('Y-m-01');
            $defaultEnd   = $activeSemester->tanggal_selesai ?? date('Y-m-t');
        } else {
            $defaultStart = date('Y-m-01');
            $defaultEnd   = date('Y-m-t');
        }

        $tanggalAwal  = $request->tanggal_awal ?? $defaultStart;
        $tanggalAkhir = $request->tanggal_akhir ?? $defaultEnd;

        if ($tanggalAwal > $tanggalAkhir) {
            return back()->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
        }

        $barangs = Barang::orderBy('kode_barang')->get();
        $rekap = [];

        foreach ($barangs as $barang) {
            // Stok awal periode (sebelum tanggal awal)
            $stokAwal = $this->getStokAwal($barang->id_barang, $tanggalAwal, $activeSemester);

            // Stok masuk dalam periode (barang_masuk status diterima)
            $stokMasukQuery = BarangMasuk::where('id_barang', $barang->id_barang)
                ->where('status', 'diterima')
                ->whereBetween('tanggal_masuk', [$tanggalAwal, $tanggalAkhir]);

            if ($activeSemester) {
                $stokMasukQuery->where('id_semester', $activeSemester->id_semester);
            }
            $stokMasuk = $stokMasukQuery->sum('jumlah_masuk');

            // Stok keluar dalam periode (peminjaman yang sudah dikembalikan)
            $stokKeluarQuery = PeminjamanDetail::where('id_barang', $barang->id_barang)
                ->where('status_item', 'kembali')
                ->whereHas('peminjaman', function ($q) use ($tanggalAwal, $tanggalAkhir, $activeSemester) {
                    $q->whereBetween('tanggal_kembali_aktual', [$tanggalAwal, $tanggalAkhir]);
                    if ($activeSemester) {
                        $q->where('id_semester', $activeSemester->id_semester);
                    }
                });
            $stokKeluar = $stokKeluarQuery->sum('jumlah');

            $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

            $rekap[] = (object) [
                'kode'        => $barang->kode_barang,
                'nama'        => $barang->nama_barang,
                'merk'        => $barang->merk ?? '-',
                'stok_awal'   => $stokAwal,
                'stok_masuk'  => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir'  => $stokAkhir,
            ];
        }

        // Statistik card (berdasarkan data dalam rentang & semester)
        $totalStokAkhir  = collect($rekap)->sum('stok_akhir');
        $totalStokMasuk  = collect($rekap)->sum('stok_masuk');
        $totalStokKeluar = collect($rekap)->sum('stok_keluar');
        $stokMenipisCount = collect($rekap)->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 2)->count();
        $stokHabisCount   = collect($rekap)->where('stok_akhir', 0)->count();

        // Filter pencarian (opsional, hanya untuk tabel)
        if ($request->filled('search')) {
            $search = $request->search;
            $rekap = array_filter($rekap, function ($item) use ($search) {
                return stripos($item->kode, $search) !== false || stripos($item->nama, $search) !== false;
            });
        }

        // Pagination manual (karena $rekap sekarang array)
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $collection = collect($rekap);
        $total = $collection->count();
        $rekapPaginated = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rekap = new LengthAwarePaginator(
            $rekapPaginated,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('stok.index', compact(
            'rekap',
            'tanggalAwal',
            'tanggalAkhir',
            'totalStokAkhir',
            'totalStokMasuk',
            'totalStokKeluar',
            'stokMenipisCount',
            'stokHabisCount'
        ));
    }

    /**
     * Hitung stok awal pada tanggal tertentu (sebelum $tanggalAwal)
     * dengan mempertimbangkan semester aktif jika ada.
     */
    private function getStokAwal($idBarang, $tanggalAwal, $activeSemester = null)
    {
        $barang = Barang::find($idBarang);
        $stokSekarang = $barang->stok;

        // Total masuk setelah tanggal awal (termasuk hingga sekarang) dalam semester yang sama
        $masukSetelahQuery = BarangMasuk::where('id_barang', $idBarang)
            ->where('status', 'diterima')
            ->where('tanggal_masuk', '>=', $tanggalAwal);
        if ($activeSemester) {
            $masukSetelahQuery->where('id_semester', $activeSemester->id_semester);
        }
        $masukSetelah = $masukSetelahQuery->sum('jumlah_masuk');

        // Total keluar setelah tanggal awal (pengembalian)
        $keluarSetelahQuery = PeminjamanDetail::where('id_barang', $idBarang)
            ->where('status_item', 'kembali')
            ->whereHas('peminjaman', function ($q) use ($tanggalAwal, $activeSemester) {
                $q->where('tanggal_kembali_aktual', '>=', $tanggalAwal);
                if ($activeSemester) {
                    $q->where('id_semester', $activeSemester->id_semester);
                }
            });
        $keluarSetelah = $keluarSetelahQuery->sum('jumlah');

        // Stok awal = stok sekarang - masuk setelah + keluar setelah
        return $stokSekarang - $masukSetelah + $keluarSetelah;
    }
}