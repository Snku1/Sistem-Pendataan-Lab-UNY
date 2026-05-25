<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\LogAktivitas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StokOpnameController extends Controller
{
    private function getActiveSemesterId()
    {
        return session('active_semester_id');
    }

    private function requireSpecificSemester()
    {
        $activeId = $this->getActiveSemesterId();
        if (!$activeId || $activeId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Silakan pilih semester tertentu (bukan "Semua Semester") untuk melakukan stok opname.');
        }
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

        $query = StokOpname::with('user')->orderBy('created_at', 'desc');

        if ($activeSemesterId != 0) {
            $query->where('id_semester', $activeSemesterId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kode_opname', 'like', "%{$search}%");
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal_opname', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_opname', '<=', $request->tanggal_akhir);
        }

        $opnames = $query->paginate(10)->withQueryString();

        return view('stok-opname.index', compact('opnames'));
    }

    public function create()
    {
        $required = $this->requireSpecificSemester();
        if ($required instanceof \Illuminate\Http\RedirectResponse) {
            return $required;
        }

        $activeSemesterId = $this->getActiveSemesterId();
        $barang = Barang::where('id_semester', $activeSemesterId)
            ->orderBy('kode_barang')
            ->get();

        return view('stok-opname.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $activeSemesterId = $this->getActiveSemesterId();
        if (!$activeSemesterId || $activeSemesterId == 0) {
            return redirect()->route('semester.daftar')
                ->with('warning', 'Pilih semester tertentu terlebih dahulu untuk melakukan stok opname.');
        }

        $request->validate([
            'tanggal_opname' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.stok_fisik' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['tanggal_opname', 'keterangan']);
            $data['kode_opname'] = StokOpname::generateKodeOpname();
            $data['id_user'] = Auth::id();
            $data['status'] = 'selesai';
            $data['id_semester'] = $activeSemesterId;
            $data['id_lab'] = Auth::user()->id_lab;

            $opname = StokOpname::create($data);

            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);
                $stokSistem = $barang->stok;
                $stokFisik = $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                $keterangan = $selisih == 0 ? 'Sesuai' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan');

                StokOpnameDetail::create([
                    'id_opname'   => $opname->id_opname,
                    'id_barang'   => $item['id_barang'],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik'  => $stokFisik,
                    'selisih'     => $selisih,
                    'keterangan'  => $keterangan,
                    'catatan'     => $item['catatan'] ?? null,
                    'id_lab'      => Auth::user()->id_lab,
                ]);
            }

            LogAktivitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Stok Opname',
                'deskripsi' => "Stok opname baru: {$opname->kode_opname}",
                'id_lab' => Auth::user()->id_lab,
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
        $activeSemesterId = $this->getActiveSemesterId();
        if ($activeSemesterId === null) {
            return redirect()->route('pilih-semester');
        }

        $opname = StokOpname::findOrFail($id);
        if ($activeSemesterId != 0 && $opname->id_semester != $activeSemesterId) {
            return redirect()->route('stok-opname.index')->with('error', 'Anda hanya dapat menghapus data stok opname pada semester yang sedang aktif.');
        }

        $opname->delete();

        LogAktivitas::create([
            'id_user' => Auth::id(),
            'aktivitas' => 'Hapus Stok Opname',
            'deskripsi' => "Menghapus stok opname: {$opname->kode_opname}",
            'id_lab' => Auth::user()->id_lab,
        ]);

        return redirect()->route('stok-opname.index')->with('success', 'Data stok opname dihapus.');
    }
}