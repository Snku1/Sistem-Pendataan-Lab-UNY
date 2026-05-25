<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Lokasi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class LaporanController extends Controller
{
    /**
     * Mendapatkan ID laboratorium user yang login (null untuk admin)
     */
    private function getLabId()
    {
        $user = Auth::user();
        return ($user && !$user->isAdmin()) ? $user->id_lab : null;
    }

    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'barang');

        $semesterList = Semester::orderBy('tahun_ajaran', 'asc')->orderBy('nama_semester', 'asc')->get();
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
        $labId = $this->getLabId();

        switch ($jenis) {
            case 'barang':
                $query = Barang::query();
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                if ($lokasiId) $query->where('id_lokasi', $lokasiId);
                return [
                    'total' => (clone $query)->count(),
                    'totalUnit' => (clone $query)->sum('stok'),
                    'rusak' => (clone $query)->sum('jumlah_rusak'),
                    'hilang' => (clone $query)->sum('jumlah_hilang'),
                ];
            case 'barang-masuk':
                $query = BarangMasuk::whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir]);
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                return [
                    'totalTransaksi' => (clone $query)->count(),
                    'totalJumlah' => (clone $query)->sum('jumlah_masuk'),
                    'menunggu' => (clone $query)->where('status', 'menunggu')->count(),
                    'diterima' => (clone $query)->where('status', 'diterima')->count(),
                ];
            case 'riwayat-peminjaman':
                $query = Peminjaman::where('status_transaksi', 'selesai');
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                $totalUnit = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($labId, $semesterId) {
                    $q->where('status_transaksi', 'selesai');
                    if ($labId) $q->where('id_lab', $labId);
                    if ($semesterId) $q->where('id_semester', $semesterId);
                })->sum('jumlah');
                $rusak = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($labId, $semesterId) {
                    $q->where('status_transaksi', 'selesai');
                    if ($labId) $q->where('id_lab', $labId);
                    if ($semesterId) $q->where('id_semester', $semesterId);
                })->where('kondisi_setelah', 'rusak')->sum('jumlah');
                $hilang = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($labId, $semesterId) {
                    $q->where('status_transaksi', 'selesai');
                    if ($labId) $q->where('id_lab', $labId);
                    if ($semesterId) $q->where('id_semester', $semesterId);
                })->where('kondisi_setelah', 'hilang')->sum('jumlah');
                return [
                    'totalSelesai' => (clone $query)->count(),
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
        $labId = $this->getLabId();

        switch ($jenis) {
            case 'barang':
                $query = Barang::with(['lokasi', 'semester']);
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                if ($lokasiId) $query->where('id_lokasi', $lokasiId);
                return $query->orderBy('created_at', 'asc');
            case 'barang-masuk':
                $query = BarangMasuk::with(['barang', 'user', 'penanggungJawab', 'semester'])
                    ->whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir]);
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                if ($status) $query->where('status', $status);
                return $query->orderBy('tanggal_masuk', 'asc');
            case 'riwayat-peminjaman':
                $query = Peminjaman::with(['details.barang', 'user'])
                    ->where('status_transaksi', 'selesai');
                if ($labId) $query->where('id_lab', $labId);
                if ($semesterId) $query->where('id_semester', $semesterId);
                if ($tglAwal && $tglAkhir) {
                    $query->whereHas('details', function ($q) use ($tglAwal, $tglAkhir) {
                        $q->whereBetween('tanggal_kembali_aktual', [$tglAwal, $tglAkhir]);
                    });
                }
                return $query->orderBy('updated_at', 'asc');
            case 'manajemen-stok':
                return Barang::query();
            default:
                return Barang::query();
        }
    }

    private function getStockMovementData($tglAwal, $tglAkhir, $semesterId, $lokasiId)
    {
        $labId = $this->getLabId();

        $barangs = Barang::with(['lokasi', 'semester'])
            ->when($labId, fn($q) => $q->where('id_lab', $labId))
            ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
            ->when($lokasiId, fn($q) => $q->where('id_lokasi', $lokasiId))
            ->orderBy('nama_barang')
            ->get();

        $rekap = [];
        foreach ($barangs as $barang) {
            $stokAwal = $this->getStokAwal($barang->id_barang, $tglAwal, $semesterId);

            $stokMasuk = BarangMasuk::where('id_barang', $barang->id_barang)
                ->where('status', 'diterima')
                ->whereBetween('tanggal_masuk', [$tglAwal, $tglAkhir])
                ->when($labId, fn($q) => $q->where('id_lab', $labId))
                ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
                ->sum('jumlah_masuk');

            $stokKeluar = PeminjamanDetail::where('id_barang', $barang->id_barang)
                ->where('status_item', 'kembali')
                ->whereHas('peminjaman', function ($q) use ($tglAwal, $tglAkhir, $labId, $semesterId) {
                    $q->whereBetween('tanggal_kembali_aktual', [$tglAwal, $tglAkhir]);
                    if ($labId) $q->where('id_lab', $labId);
                    if ($semesterId) $q->where('id_semester', $semesterId);
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

    private function getStokAwal($idBarang, $tanggalAwal, $semesterId)
    {
        $labId = $this->getLabId();
        $barang = Barang::find($idBarang);
        $stokSekarang = $barang->stok;

        $masukSetelah = BarangMasuk::where('id_barang', $idBarang)
            ->where('status', 'diterima')
            ->where('tanggal_masuk', '>=', $tanggalAwal)
            ->when($labId, fn($q) => $q->where('id_lab', $labId))
            ->when($semesterId, fn($q) => $q->where('id_semester', $semesterId))
            ->sum('jumlah_masuk');

        $keluarSetelah = PeminjamanDetail::where('id_barang', $idBarang)
            ->where('status_item', 'kembali')
            ->whereHas('peminjaman', function ($q) use ($tanggalAwal, $labId, $semesterId) {
                $q->where('tanggal_kembali_aktual', '>=', $tanggalAwal);
                if ($labId) $q->where('id_lab', $labId);
                if ($semesterId) $q->where('id_semester', $semesterId);
            })
            ->sum('jumlah');

        return $stokSekarang - $masukSetelah + $keluarSetelah;
    }

    // ========== EKSPOR PDF & CSV & EXCEL ==========
    public function exportPdf($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $pdf = PDF::loadView('laporan.pdf.' . $jenis, compact('data', 'request'));
        return $pdf->download('laporan_' . $jenis . '_' . date('YmdHis') . '.pdf');
    }

    public function exportCsv($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $filename = 'laporan_' . $jenis . '_' . date('YmdHis') . '.csv';

        $headers = $this->getCsvHeaders($jenis);

        $callback = function () use ($data, $jenis, $headers) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
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

    public function exportExcel($jenis, Request $request)
    {
        $data = $this->getFullData($jenis, $request);
        $headers = $this->getCsvHeaders($jenis);

        $rows = [];
        foreach ($data as $item) {
            $rows[] = $this->mapToCsvRow($jenis, $item);
        }

        $filename = 'laporan_' . $jenis . '_' . date('YmdHis') . '.xlsx';

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

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
                    $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') : '-',
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
                    $item->tanggal_penggunaan ? \Carbon\Carbon::parse($item->tanggal_penggunaan)->format('Y-m-d') : '-',
                    $tglKembali ? \Carbon\Carbon::parse($tglKembali)->format('Y-m-d') : '-',
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