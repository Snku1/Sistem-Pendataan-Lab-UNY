<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\PenanggungJawab;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang = Barang::with(['lokasi', 'penanggungJawab'])->orderBy('created_at', 'desc')->get();
        return view('barang.index', compact('barang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        return view('barang.create', compact('lokasi', 'penanggungJawab'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,hilang',
            'keterangan' => 'nullable|string',
            'penanggung_jawab' => 'array|exists:penanggung_jawab,id_pj'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::create($request->except('penanggung_jawab'));
            
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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barang = Barang::with(['lokasi', 'penanggungJawab', 'riwayatStok', 'riwayatKondisi'])->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barang = Barang::with('penanggungJawab')->findOrFail($id);
        $lokasi = Lokasi::all();
        $penanggungJawab = PenanggungJawab::all();
        return view('barang.edit', compact('barang', 'lokasi', 'penanggungJawab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang,' . $id . ',id_barang',
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,hilang',
            'keterangan' => 'nullable|string',
            'penanggung_jawab' => 'array|exists:penanggung_jawab,id_pj'
        ]);
        
        DB::beginTransaction();
        try {
            $barang->update($request->except('penanggung_jawab'));
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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Update kondisi barang (untuk monitoring kondisi)
     */
    public function updateKondisi(Request $request, $id)
    {
        $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'keterangan' => 'nullable|string'
        ]);
        
        $barang = Barang::findOrFail($id);
        $kondisiLama = $barang->kondisi;
        $kondisiBaru = $request->kondisi;
        
        if ($kondisiLama === $kondisiBaru) {
            return back()->with('info', 'Kondisi barang sama dengan sebelumnya.');
        }
        
        DB::beginTransaction();
        try {
            // Update kondisi barang
            $barang->update(['kondisi' => $kondisiBaru]);
            
            // Catat riwayat kondisi
            \App\Models\RiwayatKondisi::create([
                'id_barang' => $barang->id_barang,
                'kondisi_lama' => $kondisiLama,
                'kondisi_baru' => $kondisiBaru,
                'keterangan' => $request->keterangan,
                'id_user' => Auth::id()
            ]);
            
            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Update Kondisi Barang',
                'deskripsi' => "Mengubah kondisi barang {$barang->nama_barang} dari {$kondisiLama} menjadi {$kondisiBaru}"
            ]);
            
            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Kondisi barang berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}