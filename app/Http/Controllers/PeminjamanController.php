<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\LogAktivitas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\PeminjamanCreated;
use PDF;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    private function getActiveSemesterId()
    {
        return session('active_semester_id');
    }

    private function requireSpecificSemester()
    {
        $activeId = $this->getActiveSemesterId();
        if (!$activeId || $activeId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Silakan pilih semester tertentu (bukan "Semua Semester") untuk melakukan transaksi peminjaman.');
        }
        if (!Semester::where('id_semester', $activeId)->exists()) {
            Session::forget('active_semester_id');
            return redirect()->route('pilih-semester')
                ->with('error', 'Semester tidak valid. Silakan pilih semester lagi.');
        }
        return $activeId;
    }

    public function index(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $query = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->orderBy('created_at', 'desc');

        if ($activeSemesterId != 0) {
            $query->where('id_semester', $activeSemesterId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        $totalAktifQuery = Peminjaman::where('status_transaksi', 'aktif');
        if ($activeSemesterId != 0) {
            $totalAktifQuery->where('id_semester', $activeSemesterId);
        }
        $totalAktif = $totalAktifQuery->count();

        $totalBarangDipinjamQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'aktif');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        });
        $totalBarangDipinjam = $totalBarangDipinjamQuery->sum('jumlah');

        $totalBarangSudahKembaliQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'aktif');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        })->where('status_item', 'kembali');
        $totalBarangSudahKembali = $totalBarangSudahKembaliQuery->sum('jumlah');
        $totalBarangBelumKembali = $totalBarangDipinjam - $totalBarangSudahKembali;

        foreach ($peminjaman as $p) {
            $p->total_dipinjam = $p->details->sum('jumlah');
            $p->total_sudah_kembali = $p->details->where('status_item', 'kembali')->sum('jumlah');
            $p->total_belum_kembali = $p->total_dipinjam - $p->total_sudah_kembali;
        }

        return view('peminjaman.index', compact('peminjaman', 'totalAktif', 'totalBarangDipinjam', 'totalBarangSudahKembali', 'totalBarangBelumKembali'));
    }

    public function riwayat(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $query = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'selesai')
            ->orderBy('created_at', 'desc');

        if ($activeSemesterId != 0) {
            $query->where('id_semester', $activeSemesterId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_awal') || $request->filled('tanggal_akhir')) {
            $query->whereHas('details', function ($q) use ($request) {
                if ($request->filled('tanggal_awal')) {
                    $q->where('tanggal_kembali_aktual', '>=', $request->tanggal_awal);
                }
                if ($request->filled('tanggal_akhir')) {
                    $q->where('tanggal_kembali_aktual', '<=', $request->tanggal_akhir);
                }
            });
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        $totalSelesaiQuery = Peminjaman::where('status_transaksi', 'selesai');
        if ($activeSemesterId != 0) {
            $totalSelesaiQuery->where('id_semester', $activeSemesterId);
        }
        $totalSelesai = $totalSelesaiQuery->count();

        $totalBarangPernahDipinjamQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'selesai');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        });
        $totalBarangPernahDipinjam = $totalBarangPernahDipinjamQuery->sum('jumlah');

        $totalPengembalianQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'selesai');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        })->where('status_item', 'kembali');
        $totalPengembalian = $totalPengembalianQuery->count();

        $rusakSetelahPeminjamanQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'selesai');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        })->where('kondisi_setelah', 'rusak');
        $rusakSetelahPeminjaman = $rusakSetelahPeminjamanQuery->sum('jumlah');

        $hilangSetelahPeminjamanQuery = PeminjamanDetail::whereHas('peminjaman', function ($q) use ($activeSemesterId) {
            $q->where('status_transaksi', 'selesai');
            if ($activeSemesterId != 0) {
                $q->where('id_semester', $activeSemesterId);
            }
        })->where('kondisi_setelah', 'hilang');
        $hilangSetelahPeminjaman = $hilangSetelahPeminjamanQuery->sum('jumlah');

        return view('peminjaman.riwayat', compact('peminjaman', 'totalSelesai', 'totalBarangPernahDipinjam', 'totalPengembalian', 'rusakSetelahPeminjaman', 'hilangSetelahPeminjaman'));
    }

    public function create()
    {
        $required = $this->requireSpecificSemester();
        if ($required instanceof \Illuminate\Http\RedirectResponse) {
            return $required;
        }

        $barang = Barang::orderBy('nama_barang')->get();
        return view('peminjaman.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Pilih semester tertentu terlebih dahulu untuk mencatat peminjaman.');
        }

        $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'tanggal_penggunaan' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_penggunaan',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'catatan_awal' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);
                if ($barang->jumlah_baik < $item['jumlah']) {
                    return back()->with('error', "Stok baik tidak mencukupi untuk barang {$barang->nama_barang}. Tersedia: {$barang->jumlah_baik}");
                }
            }

            $data = $request->except(['items', 'surat_peminjaman']);
            $data['kode_transaksi'] = Peminjaman::generateKodeTransaksi();
            $data['id_user'] = Auth::id();
            $data['status_transaksi'] = 'aktif';
            $data['id_semester'] = $activeSemesterId;
            $data['id_lab'] = Auth::user()->id_lab;

            if ($request->hasFile('surat_peminjaman')) {
                $data['surat_peminjaman'] = $request->file('surat_peminjaman')->store('surat_peminjaman', 'public');
            }

            $peminjaman = Peminjaman::create($data);

            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);
                $peminjaman->details()->create([
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'kondisi_awal' => 'baik',
                    'status_item' => 'dipinjam',
                    'id_semester' => $activeSemesterId,
                    'id_lab' => Auth::user()->id_lab,
                ]);
                $barang->decrement('jumlah_baik', $item['jumlah']);
                $barang->decrement('stok', $item['jumlah']);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Peminjaman Barang',
                'deskripsi' => "Peminjaman baru: {$peminjaman->kode_transaksi} oleh {$peminjaman->nama_peminjam}",
                'id_lab' => Auth::user()->id_lab,
            ]);

            DB::commit();

            try {
                Mail::to($peminjaman->email)->send(new PeminjamanCreated($peminjaman));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email konfirmasi peminjaman: ' . $e->getMessage());
            }

            return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan. Email konfirmasi telah dikirim ke peminjam.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with('details.barang')->findOrFail($id);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function formPengembalian($id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $peminjaman = Peminjaman::where('status_transaksi', 'aktif')
            ->with(['details' => function ($q) {
                $q->where('status_item', 'dipinjam');
            }, 'details.barang'])
            ->findOrFail($id);

        if ($activeSemesterId != 0 && $peminjaman->id_semester != $activeSemesterId) {
            return redirect()->route('peminjaman.index')->with('error', 'Peminjaman tidak ditemukan atau tidak dapat diakses pada semester saat ini.');
        }

        return view('peminjaman.pengembalian', compact('peminjaman'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $peminjaman = Peminjaman::findOrFail($id);
        if ($peminjaman->status_transaksi != 'aktif') {
            return back()->with('error', 'Transaksi sudah selesai.');
        }

        if ($activeSemesterId != 0 && $peminjaman->id_semester != $activeSemesterId) {
            return back()->with('error', 'Anda tidak dapat memproses pengembalian peminjaman dari semester lain.');
        }

        $request->validate([
            'items' => 'sometimes|array',
            'items.*.id_detail' => 'exists:peminjaman_detail,id_detail',
            'items.*.kondisi_setelah' => 'required|in:baik,rusak,hilang',
            'items.*.catatan_kembali' => 'nullable|string',
            'tanggal_kembali' => 'required|date',
            'kembalikan_semua' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('kembalikan_semua') && $request->kembalikan_semua == 1) {
                $itemsToReturn = $peminjaman->details()->where('status_item', 'dipinjam')->get();
                $selectedDetails = [];
                foreach ($itemsToReturn as $detail) {
                    $selectedDetails[] = [
                        'id_detail' => $detail->id_detail,
                        'kondisi_setelah' => $request->input('default_kondisi', 'baik'),
                        'catatan_kembali' => $request->input('default_catatan', ''),
                    ];
                }
            } else {
                $selectedDetails = $request->items;
            }

            foreach ($selectedDetails as $item) {
                $detail = PeminjamanDetail::find($item['id_detail']);
                if ($detail && $detail->status_item == 'dipinjam') {
                    $detail->update([
                        'kondisi_setelah' => $item['kondisi_setelah'],
                        'catatan_kembali' => $item['catatan_kembali'] ?? null,
                        'tanggal_kembali_aktual' => $request->tanggal_kembali,
                        'status_item' => 'kembali',
                    ]);

                    $barang = Barang::find($detail->id_barang);
                    if ($item['kondisi_setelah'] == 'baik') {
                        $barang->increment('jumlah_baik', $detail->jumlah);
                    } elseif ($item['kondisi_setelah'] == 'rusak') {
                        $barang->increment('jumlah_rusak', $detail->jumlah);
                    } elseif ($item['kondisi_setelah'] == 'hilang') {
                        $barang->increment('jumlah_hilang', $detail->jumlah);
                    }
                    $barang->increment('stok', $detail->jumlah);
                }
            }

            if ($peminjaman->details()->where('status_item', 'dipinjam')->count() == 0) {
                $peminjaman->update(['status_transaksi' => 'selesai']);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Pengembalian Barang',
                'deskripsi' => "Pengembalian pada transaksi: {$peminjaman->kode_transaksi} oleh {$peminjaman->nama_peminjam}",
                'id_lab' => Auth::user()->id_lab,
            ]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Pengembalian berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $peminjaman = Peminjaman::with('details')->findOrFail($id);
        if ($activeSemesterId != 0 && $peminjaman->id_semester != $activeSemesterId) {
            return redirect()->route('peminjaman.index')->with('error', 'Anda hanya dapat menghapus data peminjaman pada semester yang sedang aktif.');
        }

        DB::beginTransaction();
        try {
            if ($peminjaman->status_transaksi == 'aktif') {
                foreach ($peminjaman->details as $detail) {
                    $barang = Barang::find($detail->id_barang);
                    $barang->increment('jumlah_baik', $detail->jumlah);
                    $barang->increment('stok', $detail->jumlah);
                }
            }

            if ($peminjaman->surat_peminjaman) {
                Storage::disk('public')->delete($peminjaman->surat_peminjaman);
            }

            $peminjaman->delete();

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Hapus Peminjaman',
                'deskripsi' => "Menghapus peminjaman: {$peminjaman->kode_transaksi}",
                'id_lab' => Auth::user()->id_lab,
            ]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportDetailPdf($id)
    {
        $peminjaman = Peminjaman::with('details.barang')->findOrFail($id);
        $safeFilename = 'detail_peminjaman_' . str_replace('/', '_', $peminjaman->kode_transaksi) . '.pdf';
        $pdf = PDF::loadView('laporan.pdf.detail_peminjaman', compact('peminjaman'));
        return $pdf->download($safeFilename);
    }

    public function exportDetailCsv($id)
    {
        $peminjaman = Peminjaman::with('details.barang')->findOrFail($id);
        $safeFilename = 'detail_peminjaman_' . str_replace('/', '_', $peminjaman->kode_transaksi) . '.csv';

        $headers = ['Nama Barang', 'Merk', 'Jumlah', 'Kondisi Setelah', 'Status Item', 'Tanggal Kembali', 'Catatan Kembali'];

        $callback = function () use ($peminjaman, $headers) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($peminjaman->details as $detail) {
                fputcsv($handle, [
                    $detail->barang->nama_barang,
                    $detail->barang->merk ?? '-',
                    $detail->jumlah,
                    $detail->kondisi_setelah ?? '-',
                    $detail->status_item,
                    $detail->tanggal_kembali_aktual ? Carbon::parse($detail->tanggal_kembali_aktual)->format('d/m/Y') : '-',
                    $detail->catatan_kembali ?? '-'
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"',
        ]);
    }

    /**
     * Ekspor detail peminjaman ke Excel menggunakan xlswriter
     */
    public function exportDetailExcel($id)
    {
        $peminjaman = Peminjaman::with('details.barang')->findOrFail($id);

        // Buat direktori temp jika belum ada
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'detail_peminjaman_' . str_replace('/', '_', $peminjaman->kode_transaksi) . '.xlsx';
        $filePath = $tempDir . '/' . $filename;

        // Inisialisasi Excel dengan xlswriter
        $excel = new \Vtiful\Kernel\Excel(['path' => $tempDir]);

        // Data untuk sheet
        $data = [];

        // Header informasi peminjaman
        $data[] = ['Detail Peminjaman', '', '', '', '', '', '', '', ''];
        $data[] = [];
        $data[] = ['Kode Transaksi:', $peminjaman->kode_transaksi, 'Peminjam:', $peminjaman->nama_peminjam, 'Email:', $peminjaman->email];
        $data[] = ['Tanggal Penggunaan:', Carbon::parse($peminjaman->tanggal_penggunaan)->format('d/m/Y'), 'Tanggal Jatuh Tempo:', Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d/m/Y'), 'Status Transaksi:', ucfirst($peminjaman->status_transaksi)];
        $data[] = ['Catatan Awal:', $peminjaman->catatan_awal ?? '-', '', '', '', '', '', '', ''];
        $data[] = [];

        // Header tabel detail
        $data[] = ['No', 'Nama Barang', 'Merk', 'Jumlah', 'Kondisi Awal', 'Kondisi Setelah', 'Status Item', 'Tanggal Kembali', 'Catatan Kembali'];

        // Baris detail
        $no = 1;
        foreach ($peminjaman->details as $detail) {
            $data[] = [
                $no,
                $detail->barang->nama_barang ?? '-',
                $detail->barang->merk ?? '-',
                $detail->jumlah,
                ucfirst($detail->kondisi_awal),
                ucfirst($detail->kondisi_setelah ?? '-'),
                $detail->status_item == 'dipinjam' ? 'Dipinjam' : 'Kembali',
                $detail->tanggal_kembali_aktual ? Carbon::parse($detail->tanggal_kembali_aktual)->format('d/m/Y') : '-',
                $detail->catatan_kembali ?? '-',
            ];
            $no++;
        }

        // Tulis data ke file
        $excel->fileName($filename, 'Sheet1')
              ->header([]) // tidak ada header terpisah karena kita sudah memasukkan header manual
              ->data($data);

        // Simpan file
        $excel->output();

        // Kirim response download dan hapus file setelah selesai
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}