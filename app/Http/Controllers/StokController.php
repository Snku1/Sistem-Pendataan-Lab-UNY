<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StokController extends Controller
{
    public function index(Request $request)
    {
        // Default periode: bulan ini
        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-t');

        $barangs = Barang::orderBy('kode_barang')->get();
        $rekap = [];

        foreach ($barangs as $barang) {
            // Stok awal = stok sebelum tanggal awal
            $stokAwal = $this->getStokAwal($barang->id_barang, $tanggalAwal);

            // Stok masuk dalam periode (barang_masuk status diterima)
            $stokMasuk = BarangMasuk::where('id_barang', $barang->id_barang)
                ->where('status', 'diterima')
                ->whereBetween('tanggal_masuk', [$tanggalAwal, $tanggalAkhir])
                ->sum('jumlah_masuk');

            // Stok keluar dalam periode (peminjaman yang sudah dikembalikan)
            $stokKeluar = PeminjamanDetail::where('id_barang', $barang->id_barang)
                ->where('status_item', 'kembali')
                ->whereHas('peminjaman', function($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal_kembali_aktual', [$tanggalAwal, $tanggalAkhir]);
                })
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

            $rekap[] = (object) [
                'kode' => $barang->kode_barang,
                'nama' => $barang->nama_barang,
                'merk' => $barang->merk ?? '-',
                'stok_awal' => $stokAwal,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        }

        // === STATISTIK CARD (berdasarkan seluruh data, belum difilter pencarian) ===
        $totalStokAkhir = collect($rekap)->sum('stok_akhir');
        $totalStokMasuk = collect($rekap)->sum('stok_masuk');
        $totalStokKeluar = collect($rekap)->sum('stok_keluar');
        $stokMenipisCount = collect($rekap)->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 2)->count();
        $stokHabisCount = collect($rekap)->where('stok_akhir', 0)->count();

        // Filter pencarian (opsional, hanya untuk tabel, tidak mempengaruhi card statistik)
        if ($request->filled('search')) {
            $search = $request->search;
            $rekap = array_filter($rekap, function($item) use ($search) {
                return stripos($item->kode, $search) !== false || stripos($item->nama, $search) !== false;
            });
        }

        // Pagination manual (karena koleksi)
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $collection = collect($rekap);
        $total = $collection->count();
        $rekapPaginated = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rekap = new LengthAwarePaginator(
            $rekapPaginated, $total, $perPage, $currentPage,
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

    private function getStokAwal($idBarang, $tanggalAwal)
    {
        $barang = Barang::find($idBarang);
        $stokSekarang = $barang->stok;
        $stokMasukPeriode = BarangMasuk::where('id_barang', $idBarang)
            ->where('status', 'diterima')
            ->whereBetween('tanggal_masuk', [$tanggalAwal, date('Y-m-d')])
            ->sum('jumlah_masuk');
        $stokKeluarPeriode = PeminjamanDetail::where('id_barang', $idBarang)
            ->where('status_item', 'kembali')
            ->whereHas('peminjaman', function($q) use ($tanggalAwal) {
                $q->where('tanggal_kembali_aktual', '>=', $tanggalAwal);
            })
            ->sum('jumlah');
        return $stokSekarang - $stokMasukPeriode + $stokKeluarPeriode;
    }
}