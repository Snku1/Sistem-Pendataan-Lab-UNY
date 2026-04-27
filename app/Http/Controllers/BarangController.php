<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Lokasi;
use App\Models\PenanggungJawab;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['lokasi', 'penanggungJawab']);

        if ($request->filled('merk')) {
            $query->where('merk', $request->merk);
        }
        if ($request->filled('kategori')) {
            $query->whereHas('lokasi', fn($q) => $q->where('nama_lokasi', 'like', '%' . $request->kategori . '%'));
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
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

        $totalBarang = Barang::sum('stok');
        $barangBaik = Barang::sum('jumlah_baik');
        $barangRusak = Barang::sum('jumlah_rusak');
        $barangHilang = Barang::sum('jumlah_hilang');

        $merkList = Barang::select('merk')->distinct()->whereNotNull('merk')->pluck('merk');
        $kategoriList = Lokasi::select('nama_lokasi')->distinct()->pluck('nama_lokasi');
        $tahunAjaranList = Barang::select('tahun_ajaran')->distinct()->whereNotNull('tahun_ajaran')->pluck('tahun_ajaran');

        return view('barang.index', compact(
            'barang',
            'totalBarang',
            'barangBaik',
            'barangRusak',
            'barangHilang',
            'merkList',
            'kategoriList',
            'tahunAjaranList'
        ));
    }

    public function create()
    {
        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        return view('barang.create', compact('lokasi', 'penanggungJawab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang',
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
            'semester' => 'nullable|in:Ganjil,Genap',
            'tahun_ajaran' => 'nullable|string|max:20'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('penanggung_jawab');
            $data['jumlah_rusak'] = $data['jumlah_rusak'] ?? 0;
            $data['jumlah_hilang'] = $data['jumlah_hilang'] ?? 0;
            $data['stok'] = $data['jumlah_baik'] + $data['jumlah_rusak'] + $data['jumlah_hilang'];

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
        $barang = Barang::with(['lokasi', 'penanggungJawab', 'riwayatStok', 'riwayatKondisi'])->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barang::with('penanggungJawab')->findOrFail($id);
        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        return view('barang.edit', compact('barang', 'lokasi', 'penanggungJawab'));
    }

    public function update(Request $request, $id)
    {
        // Debug: lihat data yang masuk
        // dd($request->all());

        $barang = Barang::findOrFail($id);

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
            'semester' => 'nullable|in:Ganjil,Genap',
            'tahun_ajaran' => 'nullable|string|max:20'
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
            // Simpan error ke log
            \Log::error('Update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
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
