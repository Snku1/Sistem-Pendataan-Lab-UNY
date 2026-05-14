<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('lokasi');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter stok menipis (stok total <= 3)
        if ($request->filled('filter') && $request->filter == 'menipis') {
            $query->where('stok', '<=', 3)->where('stok', '>', 0);
        }

        // Filter stok habis (stok total == 0)
        if ($request->filled('filter') && $request->filter == 'habis') {
            $query->where('stok', 0);
        }

        $barang = $query->orderBy('stok', 'asc')->paginate(10)->withQueryString();

        // Statistik
        $totalStok = Barang::sum('stok');
        $totalBaik = Barang::sum('jumlah_baik');
        $totalRusak = Barang::sum('jumlah_rusak');
        $totalHilang = Barang::sum('jumlah_hilang');
        $stokMenipisCount = Barang::where('stok', '<=', 2)->where('stok', '>', 0)->count();
        $stokHabisCount = Barang::where('stok', 0)->count();

        return view('stok.index', compact(
            'barang',
            'totalStok',
            'totalBaik',
            'totalRusak',
            'totalHilang',
            'stokMenipisCount',
            'stokHabisCount'
        ));
    }
}