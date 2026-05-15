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

        // Filter pencarian nama barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal awal & akhir
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_akhir);
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
            'tanggal_masuk' => 'required|date',
            'semester' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'id_penanggung_jawab' => 'nullable|exists:penanggung_jawab,id_pj',
            'status' => 'required|in:menunggu,diterima',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $barangMasukRecords = [];
            foreach ($request->items as $item) {
                // Upload foto per item jika ada
                $fotoPath = null;
                if (isset($item['foto']) && $item['foto'] instanceof \Illuminate\Http\UploadedFile) {
                    $fotoPath = $item['foto']->store('bukti_foto', 'public');
                }

                $data = [
                    'id_barang' => $item['id_barang'],
                    'jumlah_masuk' => $item['jumlah'],
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'semester' => $request->semester,
                    'sumber' => $request->sumber,
                    'id_penanggung_jawab' => $request->id_penanggung_jawab,
                    'status' => $request->status,
                    'bukti_foto' => $fotoPath,
                    'id_user' => Auth::id(),
                ];
                $barangMasuk = BarangMasuk::create($data);

                // Jika status langsung diterima, update stok barang
                if ($request->status == 'diterima') {
                    $barang = Barang::find($item['id_barang']);
                    $stokLama = $barang->stok;
                    $stokBaru = $stokLama + $item['jumlah'];
                    $barang->update(['stok' => $stokBaru]);
                    $barang->increment('jumlah_baik', $item['jumlah']);

                    RiwayatStok::create([
                        'id_barang' => $barang->id_barang,
                        'stok_lama' => $stokLama,
                        'stok_baru' => $stokBaru,
                        'jenis_perubahan' => 'tambah',
                        'alasan' => 'Barang masuk (langsung diterima) dari ' . ($request->sumber ?? 'tidak diketahui'),
                        'id_user' => Auth::id(),
                    ]);
                }

                $barangMasukRecords[] = $barangMasuk;
            }

            $jumlahBarang = count($barangMasukRecords);
            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Tambah Barang Masuk (Multiple)',
                'deskripsi' => "Menambahkan {$jumlahBarang} penerimaan barang dengan status {$request->status}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', "{$jumlahBarang} penerimaan barang berhasil dicatat.");
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

        // Jika status sudah diterima, tolak akses edit
        if ($barangMasuk->status == 'diterima') {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Data dengan status diterima tidak dapat diedit.');
        }

        $barang = Barang::orderBy('nama_barang')->get();
        $penanggungJawabList = PenanggungJawab::orderBy('nama_pj')->get();
        return view('barang-masuk.edit', compact('barangMasuk', 'barang', 'penanggungJawabList'));
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        // Jika status sudah diterima, tolak proses update
        if ($barangMasuk->status == 'diterima') {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Data sudah diterima, tidak dapat diedit.');
        }

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
            $idBarangLama = $barangMasuk->id_barang;
            $statusLama = $barangMasuk->status;
            $statusBaru = $data['status'];

            // Jika ada upload foto baru
            if ($request->hasFile('bukti_foto')) {
                if ($barangMasuk->bukti_foto) {
                    Storage::disk('public')->delete($barangMasuk->bukti_foto);
                }
                $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_foto', 'public');
            }

            // Update data barang masuk
            $barangMasuk->update($data);

            // Ambil barang yang baru (bisa sama atau berbeda)
            $barang = Barang::find($data['id_barang']);

            // Kasus 1: Barang tidak berubah
            if ($idBarangLama == $data['id_barang']) {
                if ($statusLama == 'menunggu' && $statusBaru == 'diterima') {
                    // Dari menunggu ke diterima: tambah stok
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
                } elseif ($statusLama == 'diterima' && $statusBaru == 'menunggu') {
                    // Dari diterima ke menunggu: kurangi stok (hanya jika sebelumnya sudah ditambahkan)
                    $stokLama = $barang->stok;
                    $stokBaru = $stokLama - $jumlahLama;
                    if ($stokBaru < 0) $stokBaru = 0;
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
                } elseif ($statusLama == 'menunggu' && $statusBaru == 'menunggu') {
                    // Status tetap menunggu, tidak ada perubahan stok
                    // Bisa update jumlah? Tapi karena status menunggu, stok belum terpengaruh, jadi aman
                } elseif ($statusLama == 'diterima' && $statusBaru == 'diterima') {
                    // Status tetap diterima: ada perubahan jumlah
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
                }
            }
            // Kasus 2: Barang berubah
            else {
                // Kurangi stok barang lama jika status lama adalah 'diterima'
                if ($statusLama == 'diterima') {
                    $barangLama = Barang::find($idBarangLama);
                    $stokLamaBarangLama = $barangLama->stok;
                    $stokBaruBarangLama = $stokLamaBarangLama - $jumlahLama;
                    if ($stokBaruBarangLama < 0) $stokBaruBarangLama = 0;
                    $barangLama->update(['stok' => $stokBaruBarangLama]);
                    $barangLama->decrement('jumlah_baik', $jumlahLama);

                    RiwayatStok::create([
                        'id_barang' => $barangLama->id_barang,
                        'stok_lama' => $stokLamaBarangLama,
                        'stok_baru' => $stokBaruBarangLama,
                        'jenis_perubahan' => 'kurang',
                        'alasan' => 'Barang dipindahkan ke barang lain (edit penerimaan)',
                        'id_user' => Auth::id(),
                    ]);
                }

                // Tambah stok barang baru jika status baru adalah 'diterima'
                if ($statusBaru == 'diterima') {
                    $stokLamaBarangBaru = $barang->stok;
                    $stokBaruBarangBaru = $stokLamaBarangBaru + $data['jumlah_masuk'];
                    $barang->update(['stok' => $stokBaruBarangBaru]);
                    $barang->increment('jumlah_baik', $data['jumlah_masuk']);

                    RiwayatStok::create([
                        'id_barang' => $barang->id_barang,
                        'stok_lama' => $stokLamaBarangBaru,
                        'stok_baru' => $stokBaruBarangBaru,
                        'jenis_perubahan' => 'tambah',
                        'alasan' => 'Barang dipindahkan ke barang ini (edit penerimaan)',
                        'id_user' => Auth::id(),
                    ]);
                }
            }

            // Hapus baris sync yang salah
            // $barangMasuk->penanggungJawab()->sync(...); // TIDAK PERLU, hapus

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Edit Barang Masuk',
                'deskripsi' => "Mengedit penerimaan barang {$barangMasuk->barang->nama_barang}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Data penerimaan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Tambahkan log untuk debugging
            \Log::error('Error update barang masuk: ' . $e->getMessage());
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
