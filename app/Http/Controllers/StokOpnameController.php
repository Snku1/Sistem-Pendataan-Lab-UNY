<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StokOpname::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kode_opname', 'like', "%{$search}%");
        }

        $opnames = $query->paginate(10)->withQueryString();

        return view('stok-opname.index', compact('opnames'));
    }

    public function create()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        return view('stok-opname.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_opname' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.stok_fisik' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['tanggal_opname', 'keterangan']);
            $data['kode_opname'] = StokOpname::generateKodeOpname();
            $data['id_user'] = Auth::id();
            $data['status'] = 'selesai'; // langsung selesai karena input langsung

            $opname = StokOpname::create($data);

            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);
                $stokSistem = $barang->stok;
                $stokFisik = $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                StokOpnameDetail::create([
                    'id_opname' => $opname->id_opname,
                    'id_barang' => $item['id_barang'],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Jika selisih tidak nol, kita bisa update stok sistem? Terserah kebijakan.
                // Biasanya stok opname hanya catatan, tidak otomatis update stok.
                // Jika ingin update stok, tambahkan kode di sini.
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Stok Opname',
                'deskripsi' => "Stok opname baru: {$opname->kode_opname}",
            ]);

            DB::commit();
            return redirect()->route('stok-opname.index')->with('success', 'Stok opname berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $opname = StokOpname::with('details.barang')->findOrFail($id);
        return view('stok-opname.show', compact('opname'));
    }

    public function destroy($id)
    {
        $opname = StokOpname::findOrFail($id);
        $opname->delete();

        LogAktivitas::create([
            'id_user' => Auth::id(),
            'aktivitas' => 'Hapus Stok Opname',
            'deskripsi' => "Menghapus stok opname: {$opname->kode_opname}",
        ]);

        return redirect()->route('stok-opname.index')->with('success', 'Data stok opname dihapus.');
    }
}