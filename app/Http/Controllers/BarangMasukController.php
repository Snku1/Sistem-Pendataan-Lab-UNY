<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PenanggungJawab;
use App\Models\RiwayatStok;
use App\Models\LogAktivitas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class BarangMasukController extends Controller
{
    /**
     * Ambil ID semester aktif dari session.
     * @return int|null (bisa 0, null, atau id semester)
     */
    private function getActiveSemesterId()
    {
        return session('active_semester_id');
    }

    /**
     * Pastikan semester aktif spesifik (bukan 0) dan valid. Jika tidak, redirect ke pilih semester.
     * @return int|null (id semester) jika valid, atau redirect response
     */
    private function requireSpecificSemester()
    {
        $activeId = $this->getActiveSemesterId();
        if (!$activeId || $activeId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Silakan pilih semester tertentu (bukan "Semua Semester") untuk melakukan transaksi ini.');
        }
        // Cek apakah semester masih ada di database
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

        $query = BarangMasuk::with(['barang', 'user', 'penanggungJawab', 'semester'])
            ->orderBy('tanggal_masuk', 'desc');

        // Filter berdasarkan semester aktif (kecuali jika user memilih filter semester tertentu di request)
        if ($request->filled('id_semester')) {
            $query->where('id_semester', $request->id_semester);
        } else {
            // Jika tidak ada filter manual, gunakan semester aktif (0 = semua semester)
            if ($activeSemesterId != 0) {
                $query->where('id_semester', $activeSemesterId);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_akhir);
        }

        $barangMasuk = $query->paginate(10)->withQueryString();

        $menungguCount = BarangMasuk::when($activeSemesterId != 0, fn($q) => $q->where('id_semester', $activeSemesterId))
            ->where('status', 'menunggu')->count();
        $diterimaCount = BarangMasuk::when($activeSemesterId != 0, fn($q) => $q->where('id_semester', $activeSemesterId))
            ->where('status', 'diterima')->count();
        $todayTotal = BarangMasuk::when($activeSemesterId != 0, fn($q) => $q->where('id_semester', $activeSemesterId))
            ->whereDate('tanggal_masuk', today())->sum('jumlah_masuk');

        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('nama_semester', 'desc')->get();

        return view('barang-masuk.index', compact('barangMasuk', 'menungguCount', 'diterimaCount', 'todayTotal', 'semesterList'));
    }

    public function create()
    {
        // Pastikan semester spesifik (bukan 0) karena kita akan menyimpan data barang masuk
        $required = $this->requireSpecificSemester();
        if ($required instanceof \Illuminate\Http\RedirectResponse) {
            return $required;
        }

        $barang = Barang::orderBy('nama_barang')->get();
        $penanggungJawabList = PenanggungJawab::orderBy('nama_pj')->get();
        // Tidak perlu mengirim semesterList ke view karena semester akan diambil dari session aktif
        return view('barang-masuk.create', compact('barang', 'penanggungJawabList'));
    }

    public function store(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        // Harus semester spesifik (bukan 0) untuk menyimpan
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Pilih semester tertentu terlebih dahulu untuk mencatat barang masuk.');
        }

        $request->validate([
            'tanggal_masuk' => 'required|date',
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
                $fotoPath = null;
                if (isset($item['foto']) && $item['foto'] instanceof \Illuminate\Http\UploadedFile) {
                    $fotoPath = $item['foto']->store('bukti_foto', 'public');
                }

                $data = [
                    'id_barang' => $item['id_barang'],
                    'jumlah_masuk' => $item['jumlah'],
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'id_semester' => $activeSemesterId, // gunakan semester aktif
                    'sumber' => $request->sumber,
                    'id_penanggung_jawab' => $request->id_penanggung_jawab,
                    'status' => $request->status,
                    'bukti_foto' => $fotoPath,
                    'id_user' => Auth::id(),
                ];
                $barangMasuk = BarangMasuk::create($data);

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
                        'id_semester' => $activeSemesterId,
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
        $barangMasuk = BarangMasuk::with(['barang', 'user', 'penanggungJawab', 'semester'])->findOrFail($id);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit($id)
    {
        $barangMasuk = BarangMasuk::with(['barang', 'penanggungJawab'])->findOrFail($id);

        if ($barangMasuk->status == 'diterima') {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Data dengan status diterima tidak dapat diedit.');
        }

        // Pastikan semester aktif spesifik dan sama dengan semester barang masuk
        $required = $this->requireSpecificSemester();
        if ($required instanceof \Illuminate\Http\RedirectResponse) {
            return $required;
        }
        $activeId = $this->getActiveSemesterId();
        if ($barangMasuk->id_semester != $activeId) {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Anda hanya dapat mengedit data barang masuk pada semester yang sedang aktif.');
        }

        $barang = Barang::orderBy('nama_barang')->get();
        $penanggungJawabList = PenanggungJawab::orderBy('nama_pj')->get();
        return view('barang-masuk.edit', compact('barangMasuk', 'barang', 'penanggungJawabList'));
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        if ($barangMasuk->status == 'diterima') {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Data sudah diterima, tidak dapat diedit.');
        }

        // Pastikan semester aktif spesifik dan sama
        $required = $this->requireSpecificSemester();
        if ($required instanceof \Illuminate\Http\RedirectResponse) {
            return $required;
        }
        $activeId = $this->getActiveSemesterId();
        if ($barangMasuk->id_semester != $activeId) {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Anda hanya dapat mengedit data pada semester yang sedang aktif.');
        }

        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
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

            if ($request->hasFile('bukti_foto')) {
                if ($barangMasuk->bukti_foto) {
                    Storage::disk('public')->delete($barangMasuk->bukti_foto);
                }
                $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_foto', 'public');
            }

            $barangMasuk->update($data);
            $barang = Barang::find($data['id_barang']);

            if ($idBarangLama == $data['id_barang']) {
                if ($statusLama == 'menunggu' && $statusBaru == 'diterima') {
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
                        'id_semester' => $activeId,
                    ]);
                } elseif ($statusLama == 'diterima' && $statusBaru == 'menunggu') {
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
                        'id_semester' => $activeId,
                    ]);
                } elseif ($statusLama == 'diterima' && $statusBaru == 'diterima') {
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
                            'id_semester' => $activeId,
                        ]);
                    }
                }
            } else {
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
                        'id_semester' => $activeId,
                    ]);
                }

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
                        'id_semester' => $activeId,
                    ]);
                }
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Edit Barang Masuk',
                'deskripsi' => "Mengedit penerimaan barang {$barangMasuk->barang->nama_barang}",
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Data penerimaan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
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
                    'id_semester' => $barangMasuk->id_semester,
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

        // Pastikan semester aktif sama (jika semester spesifik) atau izinkan jika admin dalam mode "Semua Semester"? Biarkan saja dengan pengecekan
        $activeId = $this->getActiveSemesterId();
        if ($activeId && $activeId != 0 && $barangMasuk->id_semester != $activeId) {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Anda hanya dapat menghapus data pada semester yang sedang aktif.');
        }

        DB::beginTransaction();
        try {
            $barang = $barangMasuk->barang;
            if ($barangMasuk->status == 'diterima') {
                $stokLama = $barang->stok;
                $stokBaru = $stokLama - $barangMasuk->jumlah_masuk;
                if ($stokBaru < 0) $stokBaru = 0;
                $barang->update(['stok' => $stokBaru]);
                $barang->decrement('jumlah_baik', $barangMasuk->jumlah_masuk);

                RiwayatStok::create([
                    'id_barang' => $barang->id_barang,
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'jenis_perubahan' => 'kurang',
                    'alasan' => 'Penghapusan data barang masuk (sudah diterima)',
                    'id_user' => Auth::id(),
                    'id_semester' => $barangMasuk->id_semester,
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
        $barangMasuk = BarangMasuk::with(['barang', 'user', 'penanggungJawab', 'semester'])->findOrFail($id);
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

        $request->validate([
            'kondisi_penerimaan' => 'required|in:baik,rusak,tidak_sesuai',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'catatan_pemeriksaan' => 'nullable|string',
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

            $barang = $barangMasuk->barang;
            $jumlah = $barangMasuk->jumlah_masuk;

            if ($kondisi == 'baik') {
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
                    'id_semester' => $barangMasuk->id_semester,
                ]);

                $message = 'Penerimaan barang telah dikonfirmasi (kondisi baik) dan stok berhasil ditambahkan.';
            } elseif ($kondisi == 'rusak') {
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
                    'id_semester' => $barangMasuk->id_semester,
                ]);

                $message = 'Penerimaan barang telah dikonfirmasi (kondisi rusak) dan stok berhasil ditambahkan.';
            } else {
                $barangMasuk->status = 'menunggu';
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