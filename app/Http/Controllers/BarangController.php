<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Lokasi;
use App\Models\PenanggungJawab;
use App\Models\LogAktivitas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    private function generateKodeBarang()
    {
        $lastBarang = Barang::orderBy('id_barang', 'desc')->first();
        if ($lastBarang && $lastBarang->kode_barang) {
            $lastNumber = intval(substr($lastBarang->kode_barang, 2));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'BR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    private function getActiveSemesterId()
    {
        return session('active_semester_id');
    }

    public function index(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        // Jika belum ada pilihan semester, arahkan ke halaman pilih semester
        if ($activeSemesterId === null) {
            return redirect()->route('semester.daftar')->with('warning', 'Silakan pilih semester terlebih dahulu.');
        }

        $query = Barang::with(['lokasi', 'penanggungJawab', 'semester']);

        // Filter berdasarkan semester jika bukan "Semua Semester"
        if ($activeSemesterId != 0) {
            $query->where('id_semester', $activeSemesterId);
        }

        // Filter tambahan
        if ($request->filled('merk')) {
            $query->where('merk', $request->merk);
        }
        if ($request->filled('kategori')) {
            $query->whereHas('lokasi', fn($q) => $q->where('nama_lokasi', 'like', '%' . $request->kategori . '%'));
        }
        if ($request->filled('kondisi')) {
            $kondisi = $request->kondisi;
            if ($kondisi == 'baik') {
                $query->where('jumlah_rusak', 0)->where('jumlah_hilang', 0);
            } elseif ($kondisi == 'rusak') {
                $query->where('jumlah_rusak', '>', 0);
            } elseif ($kondisi == 'hilang') {
                $query->where('jumlah_hilang', '>', 0);
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $barang = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistik berdasarkan filter yang sama (gunakan clone query)
        $totalBarang = (clone $query)->sum('stok');
        $barangBaik = (clone $query)->sum('jumlah_baik');
        $barangRusak = (clone $query)->sum('jumlah_rusak');
        $barangHilang = (clone $query)->sum('jumlah_hilang');

        // Daftar merk yang tersedia (sesuai filter semester)
        $merkQuery = Barang::query();
        if ($activeSemesterId != 0) {
            $merkQuery->where('id_semester', $activeSemesterId);
        }
        $merkList = $merkQuery->select('merk')->distinct()->whereNotNull('merk')->pluck('merk');

        $kategoriList = Lokasi::select('nama_lokasi')->distinct()->pluck('nama_lokasi');
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('nama_semester', 'desc')->get();

        return view('barang.index', compact(
            'barang',
            'totalBarang',
            'barangBaik',
            'barangRusak',
            'barangHilang',
            'merkList',
            'kategoriList',
            'semesterList'
        ));
    }

    public function create()
    {
        $activeSemesterId = $this->getActiveSemesterId();
        // Untuk menambah barang, harus memilih semester tertentu (bukan "Semua Semester")
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')->with('warning', 'Pilih semester tertentu (bukan "Semua Semester") untuk menambah barang.');
        }

        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        $kodeBarangOtomatis = $this->generateKodeBarang();
        return view('barang.create', compact('lokasi', 'penanggungJawab', 'kodeBarangOtomatis'));
    }

    public function store(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')->with('warning', 'Pilih semester tertentu untuk menyimpan barang.');
        }

        $request->validate([
            'kode_barang' => 'nullable|string|max:255|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'jumlah_baik' => 'required|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'jumlah_hilang' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'penanggung_jawab' => 'array|exists:penanggung_jawab,id_pj',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('penanggung_jawab');
            if (empty($data['kode_barang'])) {
                $data['kode_barang'] = $this->generateKodeBarang();
            }
            $data['jumlah_rusak'] = $data['jumlah_rusak'] ?? 0;
            $data['jumlah_hilang'] = $data['jumlah_hilang'] ?? 0;
            $data['stok'] = $data['jumlah_baik'] + $data['jumlah_rusak'] + $data['jumlah_hilang'];
            $data['id_semester'] = $activeSemesterId;
            $data['id_lab'] = Auth::user()->id_lab;  // <---- TAMBAHKAN INI

            $barang = Barang::create($data);
            if ($request->has('penanggung_jawab')) {
                $barang->penanggungJawab()->attach($request->penanggung_jawab);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Menambah Barang',
                'deskripsi' => 'Menambah barang ' . $barang->nama_barang . ' dengan kode ' . $barang->kode_barang
            ]);

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Barang error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $barang = Barang::with(['lokasi', 'penanggungJawab', 'riwayatStok', 'riwayatKondisi', 'semester'])->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')->with('warning', 'Pilih semester tertentu untuk mengedit barang.');
        }

        $barang = Barang::with('penanggungJawab')->findOrFail($id);
        if ($barang->id_semester != $activeSemesterId) {
            return redirect()->route('barang.index')->with('error', 'Anda tidak dapat mengedit barang dari semester lain.');
        }

        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        return view('barang.edit', compact('barang', 'lokasi', 'penanggungJawab'));
    }

    public function update(Request $request, $id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')->with('warning', 'Pilih semester tertentu untuk mengupdate barang.');
        }

        $barang = Barang::findOrFail($id);
        if ($barang->id_semester != $activeSemesterId) {
            return redirect()->route('barang.index')->with('error', 'Anda tidak dapat mengedit barang dari semester lain.');
        }

        $validator = validator($request->all(), [
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang,' . $id . ',id_barang',
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'jumlah_baik' => 'required|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'jumlah_hilang' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'penanggung_jawab' => 'array|exists:penanggung_jawab,id_pj',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except('penanggung_jawab');
            $data['jumlah_rusak'] = $data['jumlah_rusak'] ?? 0;
            $data['jumlah_hilang'] = $data['jumlah_hilang'] ?? 0;
            $data['stok'] = $data['jumlah_baik'] + $data['jumlah_rusak'] + $data['jumlah_hilang'];

            $barang->update($data);
            $barang->penanggungJawab()->sync($request->penanggung_jawab ?? []);

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Mengedit Barang',
                'deskripsi' => 'Mengedit barang ' . $barang->nama_barang
            ]);

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')->with('warning', 'Pilih semester tertentu untuk menghapus barang.');
        }

        $barang = Barang::findOrFail($id);
        if ($barang->id_semester != $activeSemesterId) {
            return redirect()->route('barang.index')->with('error', 'Anda tidak dapat menghapus barang dari semester lain.');
        }

        $namaBarang = $barang->nama_barang;

        DB::beginTransaction();
        try {
            $barang->penanggungJawab()->detach();
            $barang->delete();

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Menghapus Barang',
                'deskripsi' => 'Menghapus barang ' . $namaBarang
            ]);

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Destroy Barang error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Metode untuk kondisi awal barang masuk (jika diperlukan)
    public function editKondisiAwal($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        return view('barang-masuk.kondisi-awal', compact('barangMasuk'));
    }

    public function updateKondisiAwal(Request $request, $id)
    {
        $request->validate([
            'kondisi_penerimaan' => 'required|in:baik,rusak,tidak_sesuai',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->update(['kondisi_penerimaan' => $request->kondisi_penerimaan]);

        LogAktivitas::create([
            'id_user' => Auth::id(),
            'aktivitas' => 'Update Kondisi Awal',
            'deskripsi' => 'Memperbarui kondisi awal penerimaan barang ID: ' . $id,
        ]);

        return redirect()->route('barang-masuk.index')->with('success', 'Kondisi awal barang berhasil diperbarui.');
    }
}