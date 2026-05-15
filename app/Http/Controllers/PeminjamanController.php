<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'aktif')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->paginate(10)->withQueryString();
        $totalAktif = Peminjaman::where('status_transaksi', 'aktif')->count();
        $totalBarangDipinjam = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'aktif');
        })->sum('jumlah');
        $totalBarangSudahKembali = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'aktif');
        })->where('status_item', 'kembali')->sum('jumlah');
        $totalBarangBelumKembali = $totalBarangDipinjam - $totalBarangSudahKembali;

        // Hitung untuk setiap peminjaman: jumlah item yang masih dipinjam (belum kembali)
        foreach ($peminjaman as $p) {
            $p->total_dipinjam = $p->details->sum('jumlah');
            $p->total_sudah_kembali = $p->details->where('status_item', 'kembali')->sum('jumlah');
            $p->total_belum_kembali = $p->total_dipinjam - $p->total_sudah_kembali;
        }

        return view('peminjaman.index', compact('peminjaman', 'totalAktif', 'totalBarangDipinjam', 'totalBarangSudahKembali', 'totalBarangBelumKembali'));
    }

    public function riwayat(Request $request)
    {
        $query = Peminjaman::with('details.barang')
            ->where('status_transaksi', 'selesai')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter tanggal awal & akhir (berdasarkan tanggal kembali)
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

        // Statistik card
        $totalSelesai = Peminjaman::where('status_transaksi', 'selesai')->count();
        $totalBarangPernahDipinjam = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'selesai');
        })->sum('jumlah');

        // Jumlah pengembalian (transaksi detail yang sudah kembali)
        $totalPengembalian = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'selesai');
        })->where('status_item', 'kembali')->count();

        // Rusak setelah peminjaman (kondisi_setelah = 'rusak')
        $rusakSetelahPeminjaman = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'selesai');
        })->where('kondisi_setelah', 'rusak')->sum('jumlah');

        // Hilang setelah peminjaman (kondisi_setelah = 'hilang')
        $hilangSetelahPeminjaman = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status_transaksi', 'selesai');
        })->where('kondisi_setelah', 'hilang')->sum('jumlah');

        return view('peminjaman.riwayat', compact('peminjaman', 'totalSelesai', 'totalBarangPernahDipinjam', 'totalPengembalian', 'rusakSetelahPeminjaman', 'hilangSetelahPeminjaman'));
    }

    public function create()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        return view('peminjaman.create', compact('barang'));
    }

    public function store(Request $request)
    {
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
            // HAPUS validasi items.*.kondisi_awal
        ]);

        DB::beginTransaction();
        try {
            // Cek stok baik untuk setiap barang
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

            if ($request->hasFile('surat_peminjaman')) {
                $data['surat_peminjaman'] = $request->file('surat_peminjaman')->store('surat_peminjaman', 'public');
            }

            $peminjaman = Peminjaman::create($data);

            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);

                $peminjaman->details()->create([
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'kondisi_awal' => 'baik', // Default 'baik'
                    'status_item' => 'dipinjam',
                ]);

                // Kurangi stok baik dan stok total
                $barang->decrement('jumlah_baik', $item['jumlah']);
                $barang->decrement('stok', $item['jumlah']);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Peminjaman Barang',
                'deskripsi' => "Peminjaman baru: {$peminjaman->kode_transaksi} oleh {$peminjaman->nama_peminjam}",
            ]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan.');
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
        $peminjaman = Peminjaman::with(['details' => function ($q) {
            $q->where('status_item', 'dipinjam');
        }, 'details.barang'])->where('status_transaksi', 'aktif')->findOrFail($id);
        return view('peminjaman.pengembalian', compact('peminjaman'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status_transaksi != 'aktif') {
            return back()->with('error', 'Transaksi sudah selesai.');
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
            // Jika klik "Kembalikan Semua", ambil semua detail yang masih dipinjam
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

            $allReturned = true;
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

            // Cek apakah semua detail sudah kembali
            if ($peminjaman->details()->where('status_item', 'dipinjam')->count() == 0) {
                $peminjaman->update(['status_transaksi' => 'selesai']);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Pengembalian Barang',
                'deskripsi' => "Pengembalian pada transaksi: {$peminjaman->kode_transaksi} oleh {$peminjaman->nama_peminjam}",
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
        $peminjaman = Peminjaman::with('details')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($peminjaman->status_transaksi == 'aktif') {
                foreach ($peminjaman->details as $detail) {
                    $barang = Barang::find($detail->id_barang);
                    // Kembalikan jumlah_baik (karena saat peminjaman diambil dari jumlah_baik)
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
            ]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
