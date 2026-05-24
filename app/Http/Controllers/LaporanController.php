<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Lokasi;
use App\Models\Semester;
use Illuminate\Http\Request;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'barang');

        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('nama_semester', 'desc')->get();
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

        $tanggalAwal = $request->get('tanggal_awal', date('Y-m-01'));
        $tanggalAkhir = $request->get('tanggal_akhir', date('Y-m-t'));
        $semesterId = $request->get('id_semester');
        $lokasiId = $request->get('id_lokasi');
        $status = $request->get('status');

        $previewData = $this->getPreviewData($jenis, $tanggalAwal, $tanggalAkhir, $semesterId, $lokasiId, $status);
        $stats = $this->getStatistics($jenis, $tanggalAwal, $tanggalAkhir, $semesterId, $lokasiId);

        return view('laporan.index', compact(
            'jenis',
            'semesterList',
            'lokasiList',
            'tanggalAwal',
            'tanggalAkhir',
            'semesterId',
            'lokasiId',
            'status',
            'previewData',
            'stats'
        ));
    }

    private function getPreviewData($jenis, $tglAwal, $tglAkhir, $semesterId, $lokasiId, $status)
    {
        if ($jenis == 'manajemen-stok') {
            $data = $this->getStockMovementData($tglAwal, $tglAkhir, $semesterId, $lokasiId);
            return $data->take(5);
        }
        $query = $this->buildQuery($jenis, $tglAwal, $tglAkhir, $semesterId, $lokasiId, $status);
        return $query->limit(5)->get();
    }

    private function getStatistics($jenis, $tglAwal, $tglAkhir, $semesterId, $lokasiId)
    {
        switch ($jenis) {
            case 'barang':
                return [
                    'total' => Barang::count(),
                    'totalUnit' => Barang::sum('stok'),
                    'rusak' => Barang::sum('jumlah_rusak'),
                    'hilang' => Barang::sum('jumlah_hilang'),
                ];
            case 'barang-masuk':
                $query = BarangMasuk::whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir]);
                if ($semesterId)
                    $query->where('id_semester', $semesterId);
                return [
                    'totalTransaksi' => $query->count(),
                    'totalJumlah' => $query->sum('jumlah_masuk'),
                    'menunggu' => (clone $query)->where('status', 'menunggu')->count(),
                    'diterima' => (clone $query)->where('status', 'diterima')->count(),
                ];
            case 'riwayat-peminjaman':
                $query = Peminjaman::where('status_transaksi', 'selesai');
                if ($semesterId)
                    $query->where('id_semester', $semesterId);
                $totalUnit = PeminjamanDetail::whereHas('peminjaman', fn($q) => $q->where('status_transaksi', 'selesai'))
                    ->when($semesterId, fn($q) => $q->whereHas('peminjaman', fn($sq) => $sq->where('id_semester', $semesterId)))
                    ->sum('jumlah');
                $rusak = PeminjamanDetail::whereHas('peminjaman', fn($q) => $q->where('status_transaksi', 'selesai'))
                    ->where('kondisi_setelah', 'rusak')
                    ->when($semesterId, fn($q) => $q->whereHas('peminjaman', fn($sq) => $sq->where('id_semester', $semesterId)))
                    ->sum('jumlah');
                $hilang = PeminjamanDetail::whereHas('peminjaman', fn($q) => $q->where('status_transaksi', 'selesai'))
                    ->where('kondisi_setelah', 'hilang')
                    ->when($semesterId, fn($q) => $q->whereHas('peminjaman', fn($sq) => $sq->where('id_semester', $semesterId)))
                    ->sum('jumlah');
                return [
                    'totalSelesai' => $query->count(),
                    'totalUnit' => $totalUnit,
                    'rusak' => $rusak,
                    'hilang' => $hilang,
                ];
            case 'manajemen-stok':
                $data = $this->getStockMovementData($tglAwal, $tglAkhir, $semesterId, $lokasiId);
                return [
                    'totalBarang' => $data->count(),
                    'totalStok' => $data->sum('stok_akhir'),
                    'stokMenipis' => $data->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 2)->count(),
                    'stokHabis' => $data->where('stok_akhir', 0)->count(),
                ];
            default:
                return [];
        }
    }

    private function buildQuery($jenis, $tglAwal, $tglAkhir, $semesterId, $lokasiId, $status)
    {
        switch ($jenis) {
            case 'barang':
                $query = Barang::with(['lokasi', 'semester']);
                if ($semesterId)
                    $query->where('id_semester', $semesterId);
                if ($lokasiId)
                    $query->where('id_lokasi', $lokasiId);
                return $query->orderBy('created_at', 'desc');
            case 'barang-masuk':
                $query = BarangMasuk::with(['barang', 'user', 'penanggungJawab', 'semester'])
                    ->whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir]);
                if ($semesterId)
                    $query->where('id_semester', $semesterId);
                if ($status)
                    $query->where('status', $status);
                return $query->orderBy('tanggal_masuk', 'desc');
            case 'riwayat-peminjaman':
                $query = Peminjaman::with(['details.barang', 'user'])
                    ->where('status_transaksi', 'selesai');
                if ($semesterId)
                    $query->where('id_semester', $semesterId);
                if ($tglAwal && $tglAkhir) {
                    $query->whereHas('details', function ($q) use ($tglAwal, $tglAkhir) {
                        $q->whereBetween('tanggal_kembali_aktual', [$tglAwal, $tglAkhir]);
                    });
                }
                return $query->orderBy('updated_at', 'desc');
            case 'manajemen-stok':
                // Tidak digunakan karena sudah ditangani khusus di getPreviewData/getFullData
                return Barang::query();
            default:
                return Barang::query();
        }
    }

    /**
     * Mengambil data pergerakan stok untuk setiap barang dalam rentang tanggal dan semester/lokasi tertentu.
     * Hasil berupa collection of object dengan field: nama_barang, merk, stok_awal, stok_masuk, stok_keluar, stok_akhir.
     */
    private function getStockMovementData($tglAwal, $tglAkhir, $semesterId, $lokasiId)
    {
        $barangs = Barang::with(['lokasi', 'semester'])
            ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
            ->when($lokasiId, fn($q) => $q->where('id_lokasi', $lokasiId))
            ->orderBy('nama_barang')
            ->get();

        $rekap = [];
        foreach ($barangs as $barang) {
            // Stok awal = stok sebelum tanggal awal
            $stokAwal = $this->getStokAwal($barang->id_barang, $tglAwal, $semesterId);

            // Stok masuk dalam periode (barang_masuk status diterima)
            $stokMasuk = BarangMasuk::where('id_barang', $barang->id_barang)
                ->where('status', 'diterima')
                ->whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir])
                ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
                ->sum('jumlah_masuk');

            // Stok keluar dalam periode (peminjaman yang sudah dikembalikan)
            $stokKeluar = PeminjamanDetail::where('id_barang', $barang->id_barang)
                ->where('status_item', 'kembali')
                ->whereHas('peminjaman', function ($q) use ($tglAwal, $tglAkhir, $semesterId) {
                    $q->whereBetween('tanggal_kembali_aktual', [$tglAwal, $tglAkhir]);
                    if ($semesterId)
                        $q->where('id_semester', $semesterId);
                })
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

            $rekap[] = (object) [
                'nama_barang' => $barang->nama_barang,
                'merk' => $barang->merk ?? '-',
                'stok_awal' => $stokAwal,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        }
        return collect($rekap);
    }

    /**
     * Hitung stok awal pada tanggal tertentu (sebelum $tanggalAwal).
     */
    private function getStokAwal($idBarang, $tanggalAwal, $semesterId)
    {
        $barang = Barang::find($idBarang);
        $stokSekarang = $barang->stok;

        // Total masuk setelah tanggal awal (termasuk sampai sekarang) dalam semester yang sama
        $masukSetelah = BarangMasuk::where('id_barang', $idBarang)
            ->where('status', 'diterima')
            ->where('tanggal_masuk', '>=', $tanggalAwal)
            ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
            ->sum('jumlah_masuk');

        // Total keluar setelah tanggal awal (pengembalian)
        $keluarSetelah = PeminjamanDetail::where('id_barang', $idBarang)
            ->where('status_item', 'kembali')
            ->whereHas('peminjaman', function ($q) use ($tanggalAwal, $semesterId) {
                $q->where('tanggal_kembali_aktual', '>=', $tanggalAwal);
                if ($semesterId)
                    $q->where('id_semester', $semesterId);
            })
            ->sum('jumlah');

        // Stok awal = stok sekarang - masuk setelah + keluar setelah
        return $stokSekarang - $masukSetelah + $keluarSetelah;
    }

    // ========== EKSPOR PDF ==========
    public function exportPdf($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $pdf = PDF::loadView('laporan.pdf.' . $jenis, compact('data', 'request'));
        return $pdf->download('laporan_' . $jenis . '_' . date('YmdHis') . '.pdf');
    }

    // ========== EKSPOR CSV ==========
    public function exportCsv($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $filename = 'laporan_' . $jenis . '_' . date('YmdHis') . '.csv';

        $headers = $this->getCsvHeaders($jenis);

        $callback = function () use ($data, $jenis, $headers) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, $headers);

            foreach ($data as $item) {
                fputcsv($handle, $this->mapToCsvRow($jenis, $item));
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ========== EKSPOR EXCEL (xlswriter) ==========
    /**
     * Ekspor laporan ke file Excel (.xlsx) menggunakan ekstensi xlswriter
     */
    public function exportExcel($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $headers = $this->getCsvHeaders($jenis);

        $rows = [];
        foreach ($data as $item) {
            $rows[] = $this->mapToCsvRow($jenis, $item);
        }

        $filename = 'laporan_' . $jenis . '_' . date('YmdHis') . '.xlsx';

        // Tentukan folder sementara (bisa di storage/app/temp)
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Gunakan class asli dari ekstensi xlswriter
        $excel = new \Vtiful\Kernel\Excel(['path' => $tempDir]);
        $excel->fileName($filename, 'Sheet1');
        $excel->header($headers);
        $excel->data($rows);
        $excel->output();

        $filePath = $tempDir . '/' . $filename;
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function getCsvHeaders($jenis)
    {
        switch ($jenis) {
            case 'barang':
                return ['Kode', 'Nama Barang', 'Merk', 'Stok', 'Baik', 'Rusak', 'Hilang', 'Lokasi', 'Semester', 'Keterangan'];
            case 'barang-masuk':
                return ['Tanggal', 'Nama Barang', 'Jumlah', 'Sumber', 'Pemeriksa', 'Status', 'Catatan', 'Bukti Foto'];
            case 'riwayat-peminjaman':
                return ['Kode Transaksi', 'Peminjam', 'Tanggal Pinjam', 'Tanggal Kembali', 'Total Barang', 'Rusak', 'Hilang', 'Catatan'];
            case 'manajemen-stok':
                return ['Nama Barang', 'Merk', 'Stok Awal', 'Stok Masuk', 'Stok Keluar', 'Stok Akhir'];
            default:
                return [];
        }
    }

    private function mapToCsvRow($jenis, $item)
    {
        switch ($jenis) {
            case 'barang':
                return [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->merk ?? '-',
                    $item->stok,
                    $item->jumlah_baik,
                    $item->jumlah_rusak,
                    $item->jumlah_hilang,
                    $item->lokasi->nama_lokasi ?? '-',
                    $item->semester ? $item->semester->nama_semester . ' - ' . $item->semester->tahun_ajaran : '-',
                    $item->keterangan ?? '-'
                ];
            case 'barang-masuk':
                return [
                    $item->tanggal_masuk,
                    $item->barang->nama_barang ?? '-',
                    $item->jumlah_masuk,
                    $item->sumber ?? '-',
                    $item->penanggungJawab->nama_pj ?? '-',
                    $item->status,
                    $item->catatan_pemeriksaan ?? '-',
                    $item->bukti_foto ? 'Ada' : '-'
                ];
            case 'riwayat-peminjaman':
                $tglKembali = $item->details->where('status_item', 'kembali')->max('tanggal_kembali_aktual');
                $rusak = $item->details->where('kondisi_setelah', 'rusak')->sum('jumlah');
                $hilang = $item->details->where('kondisi_setelah', 'hilang')->sum('jumlah');
                return [
                    $item->kode_transaksi,
                    $item->nama_peminjam,
                    $item->tanggal_penggunaan,
                    $tglKembali ?? '-',
                    $item->details->sum('jumlah'),
                    $rusak,
                    $hilang,
                    $item->catatan_awal ?? '-'
                ];
            case 'manajemen-stok':
                return [
                    $item->nama_barang,
                    $item->merk,
                    $item->stok_awal,
                    $item->stok_masuk,
                    $item->stok_keluar,
                    $item->stok_akhir
                ];
            default:
                return [];
        }
    }

    public function getFullData($jenis, Request $request)
    {
        $tglAwal = $request->get('tanggal_awal', date('Y-m-01'));
        $tglAkhir = $request->get('tanggal_akhir', date('Y-m-t'));
        $semesterId = $request->get('id_semester');
        $lokasiId = $request->get('id_lokasi');
        $status = $request->get('status');

        if ($jenis == 'manajemen-stok') {
            return $this->getStockMovementData($tglAwal, $tglAkhir, $semesterId, $lokasiId);
        }
        $query = $this->buildQuery($jenis, $tglAwal, $tglAkhir, $semesterId, $lokasiId, $status);
        return $query->get();
    }
}
