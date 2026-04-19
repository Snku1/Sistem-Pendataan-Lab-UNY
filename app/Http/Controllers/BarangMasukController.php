<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\RiwayatStok;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangMasuk = BarangMasuk::with(['barang', 'user'])->orderBy('tanggal_masuk', 'desc')->get();
        return view('barang-masuk.index', compact('barangMasuk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        return view('barang-masuk.create', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'semester' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->id_barang);
            $stokLama = $barang->stok;
            $stokBaru = $stokLama + $request->jumlah_masuk;
            
            // Simpan barang masuk
            $barangMasuk = BarangMasuk::create([
                'id_barang' => $request->id_barang,
                'jumlah_masuk' => $request->jumlah_masuk,
                'tanggal_masuk' => $request->tanggal_masuk,
                'semester' => $request->semester,
                'sumber' => $request->sumber,
                'id_user' => Auth::id(),
            ]);
            
            // Update stok barang
            $barang->update(['stok' => $stokBaru]);
            
            // Catat riwayat stok
            RiwayatStok::create([
                'id_barang' => $barang->id_barang,
                'stok_lama' => $stokLama,
                'stok_baru' => $stokBaru,
                'jenis_perubahan' => 'tambah',
                'alasan' => 'Barang masuk dari ' . ($request->sumber ?? 'tidak diketahui'),
                'id_user' => Auth::id(),
            ]);
            
            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Barang Masuk',
                'deskripsi' => "Menambahkan stok barang {$barang->nama_barang} sebanyak {$request->jumlah_masuk}",
            ]);
            
            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dicatat.');
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
        $barangMasuk = BarangMasuk::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $barang = $barangMasuk->barang;
            $stokLama = $barang->stok;
            $stokBaru = $stokLama - $barangMasuk->jumlah_masuk;
            if ($stokBaru < 0) $stokBaru = 0;
            
            // Update stok
            $barang->update(['stok' => $stokBaru]);
            
            // Catat riwayat stok
            RiwayatStok::create([
                'id_barang' => $barang->id_barang,
                'stok_lama' => $stokLama,
                'stok_baru' => $stokBaru,
                'jenis_perubahan' => 'kurang',
                'alasan' => 'Penghapusan data barang masuk',
                'id_user' => Auth::id(),
            ]);
            
            // Hapus data barang masuk
            $barangMasuk->delete();
            
            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Hapus Barang Masuk',
                'deskripsi' => "Menghapus catatan barang masuk untuk {$barang->nama_barang}",
            ]);
            
            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk dihapus dan stok dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}