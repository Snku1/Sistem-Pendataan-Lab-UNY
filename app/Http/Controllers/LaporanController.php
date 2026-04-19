<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan
     */
    public function index()
    {
        return view('laporan.index');
    }
    
    /**
     * Laporan stok barang
     */
    public function stok()
    {
        $barang = Barang::with('lokasi')->orderBy('nama_barang')->get();
        return view('laporan.stok', compact('barang'));
    }
    
    /**
     * Laporan barang rusak dan hilang
     */
    public function rusakHilang()
    {
        $barangRusak = Barang::where('kondisi', 'rusak')->with('lokasi')->get();
        $barangHilang = Barang::where('kondisi', 'hilang')->with('lokasi')->get();
        return view('laporan.rusak-hilang', compact('barangRusak', 'barangHilang'));
    }
    
    /**
     * Laporan inventaris per semester
     */
    public function semester(Request $request)
    {
        $semester = $request->input('semester');
        $barangMasuk = BarangMasuk::with(['barang', 'user'])
            ->when($semester, function ($query) use ($semester) {
                return $query->where('semester', 'like', "%{$semester}%");
            })
            ->orderBy('tanggal_masuk', 'desc')
            ->get();
            
        // Daftar semester unik untuk filter
        $semesterList = BarangMasuk::select('semester')
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester');
            
        return view('laporan.semester', compact('barangMasuk', 'semester', 'semesterList'));
    }
    
    /**
     * Laporan semua barang (export)
     * Nanti bisa ditambahkan export PDF/Excel
     */
    public function allBarang()
    {
        $barang = Barang::with(['lokasi', 'penanggungJawab'])->get();
        return view('laporan.all-barang', compact('barang'));
    }
}