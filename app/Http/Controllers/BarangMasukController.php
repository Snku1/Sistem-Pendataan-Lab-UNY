<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PenanggungJawab;
use App\Models\RiwayatStok;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangMasuk::with(['barang', 'user', 'penanggungJawab'])->orderBy('tanggal_masuk', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $barangMasuk = $query->paginate(10)->withQueryString();

        $menungguCount = BarangMasuk::where('status', 'menunggu')->count();
        $diterimaCount = BarangMasuk::where('status', 'diterima')->count();
        $todayTotal = BarangMasuk::whereDate('tanggal_masuk', today())->sum('jumlah_masuk');

        return view('barang-masuk.index', compact('barangMasuk', 'menungguCount', 'diterimaCount', 'todayTotal'));
    }

    public function create()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        $penanggungJawabList = PenanggungJawab::orderBy('nama_pj')->get();
        return view('barang-masuk.create', compact('barang', 'penanggungJawabList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'semester' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'id_penanggung_jawab' => 'nullable|exists:penanggung_jawab,id_pj',
            'status' => 'required|in:menunggu,diterima',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('bukti_foto');
            $data['id_user'] = Auth::id();

            if ($request->hasFile('bukti_foto')) {
                $path = $request->file('bukti_foto')->store('bukti_foto', 'public');
                $data['bukti_foto'] = $path;
            }

            $barangMasuk = BarangMasuk::create($data);

            if ($data['status'] == 'diterima') {
                $barang = Barang::find($data['id_barang']);
                $stokLama = $barang->stok;
                $stokBaru = $stokLama + $data['jumlah_masuk'];
                $barang->update(['stok' => $stokBaru]);
                $barang->increment('jumlah_baik', $data['jumlah_masuk']);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'tambah',
                    'alasan' => 'Barang masuk (langsung diterima) dari ' . ($data['sumber'] ?? 'tidak diketahui'),
                    'id_user' => Auth::id(),
                ]);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Tambah Barang Masuk',
                'deskripsi' => "Menambahkan penerimaan barang {$barangMasuk->barang->nama_barang} sebanyak {$data['jumlah_masuk']} dengan status {$data['status']}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Penerimaan barang berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['barang', 'user', 'penanggungJawab'])->findOrFail($id);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit($id)
    {
        $barangMasuk = BarangMasuk::with(['barang', 'penanggungJawab'])->findOrFail($id);
        $barang = Barang::orderBy('nama_barang')->get();
        $penanggungJawabList = PenanggungJawab::orderBy('nama_pj')->get();
        return view('barang-masuk.edit', compact('barangMasuk', 'barang', 'penanggungJawabList'));
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'semester' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'id_penanggung_jawab' => 'nullable|exists:penanggung_jawab,id_pj',
            'status' => 'required|in:menunggu,diterima',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('bukti_foto');
            $jumlahLama = $barangMasuk->jumlah_masuk;
            $statusLama = $barangMasuk->status;

            if ($request->hasFile('bukti_foto')) {
                if ($barangMasuk->bukti_foto) {
                    Storage::disk('public')->delete($barangMasuk->bukti_foto);
                }
                $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_foto', 'public');
            }

            $barangMasuk->update($data);
            $barang = $barangMasuk->barang;

            if ($statusLama == 'diterima' && $data['status'] == 'diterima') {
                $selisih = $data['jumlah_masuk'] - $jumlahLama;
                if ($selisih != 0) {
                    $stokLama = $barang->stok;
                    $stokBaru = $stokLama + $selisih;
                    $barang->update(['stok' => $stokBaru]);
                    if ($selisih > 0) {
                        $barang->increment('jumlah_baik', $selisih);
                    } else {
                        $barang->decrement('jumlah_baik', abs($selisih));
                    }

                    RiwayatStok::create([
                        'id_barang' => $barang->id_barang,
                        'stok_lama' => $stokLama,
                        'stok_baru' => $stokBaru,
                        'jenis_perubahan' => $selisih > 0 ? 'tambah' : 'kurang',
                        'alasan' => 'Edit jumlah barang masuk (status tetap diterima)',
                        'id_user' => Auth::id(),
                    ]);
                }
            } elseif ($statusLama == 'menunggu' && $data['status'] == 'diterima') {
                $stokLama = $barang->stok;
                $stokBaru = $stokLama + $data['jumlah_masuk'];
                $barang->update(['stok' => $stokBaru]);
                $barang->increment('jumlah_baik', $data['jumlah_masuk']);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'tambah',
                    'alasan' => 'Status berubah dari menunggu ke diterima',
                    'id_user' => Auth::id(),
                ]);
            } elseif ($statusLama == 'diterima' && $data['status'] == 'menunggu') {
                $stokLama = $barang->stok;
                $stokBaru = $stokLama - $jumlahLama;
                if ($stokBaru < 0)
                    $stokBaru = 0;
                $barang->update(['stok' => $stokBaru]);
                $barang->decrement('jumlah_baik', $jumlahLama);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'kurang',
                    'alasan' => 'Status berubah dari diterima ke menunggu',
                    'id_user' => Auth::id(),
                ]);
            }

            $barangMasuk->penanggungJawab()->sync($request->penanggung_jawab ?? []);

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Edit Barang Masuk',
                'deskripsi' => "Mengedit penerimaan barang {$barang->nama_barang}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Data penerimaan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDetail(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'kondisi_penerimaan' => 'nullable|in:baik,rusak,tidak_sesuai',
            'catatan_pemeriksaan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'action' => 'required|in:update,confirm'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['kondisi_penerimaan', 'catatan_pemeriksaan']);

            if ($request->hasFile('bukti_foto')) {
                if ($barangMasuk->bukti_foto) {
                    Storage::disk('public')->delete($barangMasuk->bukti_foto);
                }
                $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_foto', 'public');
            }

            $barangMasuk->update($data);

            if ($request->action == 'confirm' && $barangMasuk->status == 'menunggu') {
                $barang = $barangMasuk->barang;
                $stokLama = $barang->stok;
                $stokBaru = $stokLama + $barangMasuk->jumlah_masuk;
                $barang->update(['stok' => $stokBaru]);
                $barang->increment('jumlah_baik', $barangMasuk->jumlah_masuk);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'tambah',
                    'alasan' => 'Barang masuk diterima (verifikasi) dari ' . ($barangMasuk->sumber ?? 'tidak diketahui'),
                    'id_user' => Auth::id(),
                ]);

                $barangMasuk->update(['status' => 'diterima']);

                LogAktivitas::create([
                    'id_user' => Auth::id(),
                    'aktivitas' => 'Konfirmasi Penerimaan Barang',
                    'deskripsi' => "Mengkonfirmasi penerimaan barang {$barang->nama_barang} sebanyak {$barangMasuk->jumlah_masuk} unit",
                ]);
            }

            DB::commit();
            return redirect()->route('barang-masuk.show', $id)->with('success', 'Detail penerimaan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        DB::beginTransaction();
        try {
            $barang = $barangMasuk->barang;
            if ($barangMasuk->status == 'diterima') {
                $stokLama = $barang->stok;
                $stokBaru = $stokLama - $barangMasuk->jumlah_masuk;
                if ($stokBaru < 0)
                    $stokBaru = 0;
                $barang->update(['stok' => $stokBaru]);
                $barang->decrement('jumlah_baik', $barangMasuk->jumlah_masuk);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'kurang',
                    'alasan' => 'Penghapusan data barang masuk (sudah diterima)',
                    'id_user' => Auth::id(),
                ]);
            }

            if ($barangMasuk->bukti_foto) {
                Storage::disk('public')->delete($barangMasuk->bukti_foto);
            }

            $barangMasuk->delete();

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Hapus Barang Masuk',
                'deskripsi' => "Menghapus catatan penerimaan barang untuk {$barang->nama_barang}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Data penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detailPemeriksaan($id)
    {
        $barangMasuk = BarangMasuk::with(['barang', 'user', 'penanggungJawab'])->findOrFail($id);
        return view('barang-masuk.detail-pemeriksaan', compact('barangMasuk'));
    }

    public function editKondisiAwal($id)
    {
        $barangMasuk = BarangMasuk::with(['barang', 'penanggungJawab'])->findOrFail($id);
        return view('barang-masuk.kondisi-awal', compact('barangMasuk'));
    }

    public function updateKondisiAwal(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $kondisi = $request->kondisi_penerimaan;

        // Validasi input
        $request->validate([
            'kondisi_penerimaan' => 'required|in:baik,rusak,tidak_sesuai',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'catatan_pemeriksaan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data kondisi, catatan, dan foto
            $data = $request->only(['kondisi_penerimaan', 'catatan_pemeriksaan']);
            if ($request->hasFile('bukti_foto')) {
                if ($barangMasuk->bukti_foto) {
                    Storage::disk('public')->delete($barangMasuk->bukti_foto);
                }
                $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_foto', 'public');
            }
            $barangMasuk->update($data);

            $barang = $barangMasuk->barang;
            $jumlah = $barangMasuk->jumlah_masuk;

            if ($kondisi == 'baik') {
                // Konfirmasi diterima, stok + jumlah, jumlah_baik + jumlah
                $barangMasuk->status = 'diterima';
                $barangMasuk->save();

                $barang->stok += $jumlah;
                $barang->jumlah_baik += $jumlah;
                $barang->save();

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $barang->stok - $jumlah,
                    'stok_baru' => $barang->stok,
                    'jenis_perubahan' => 'tambah',
                    'alasan' => 'Penerimaan barang (kondisi baik)',
                    'id_user' => Auth::id(),
                ]);

                $message = 'Penerimaan barang telah dikonfirmasi (kondisi baik) dan stok berhasil ditambahkan.';
            } elseif ($kondisi == 'rusak') {
                // Konfirmasi diterima, stok + jumlah, jumlah_rusak + jumlah
                $barangMasuk->status = 'diterima';
                $barangMasuk->save();

                $barang->stok += $jumlah;
                $barang->jumlah_rusak += $jumlah;
                $barang->save();

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $barang->stok - $jumlah,
                    'stok_baru' => $barang->stok,
                    'jenis_perubahan' => 'tambah',
                    'alasan' => 'Penerimaan barang (kondisi rusak)',
                    'id_user' => Auth::id(),
                ]);

                $message = 'Penerimaan barang telah dikonfirmasi (kondisi rusak) dan stok berhasil ditambahkan.';
            } else { // kondisi = 'tidak_sesuai'
                // Status tetap menunggu, tidak mengubah stok
                // Hanya menyimpan data kondisi dan catatan (sudah diupdate di atas)
                $barangMasuk->status = 'menunggu'; // pastikan tetap menunggu
                $barangMasuk->save();
                $message = 'Data kondisi disimpan, namun barang tidak diterima karena tidak sesuai.';
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Konfirmasi Penerimaan Barang',
                'deskripsi' => "Konfirmasi penerimaan barang {$barang->nama_barang} sebanyak {$jumlah} unit dengan kondisi {$kondisi}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updateKondisiAwal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
