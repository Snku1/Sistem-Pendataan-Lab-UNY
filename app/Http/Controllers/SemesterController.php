<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SemesterController extends Controller
{
    // ========== UNTUK PEMILIHAN SEMESTER AKTIF ==========
    public function index()
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama_semester', 'desc')
            ->get();

        $activeSemester = null;
        if (Session::has('active_semester_id')) {
            $activeId = Session::get('active_semester_id');
            if ($activeId != 0) {
                $activeSemester = Semester::find($activeId);
            } else {
                // Untuk "Semua Semester", kita buat objek dummy
                $activeSemester = (object)['id_semester' => 0, 'nama_semester' => 'Semua Semester', 'tahun_ajaran' => ''];
            }
        }

        return view('semester.pilih', compact('semesterList', 'activeSemester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_semester' => 'required|exists:semester,id_semester'
        ]);

        Session::put('active_semester_id', $request->id_semester);
        return redirect()->intended('/dashboard')
            ->with('success', 'Semester aktif berhasil dipilih.');
    }

    /**
     * Method untuk mengubah semester dari dropdown (AJAX)
     * Menerima id_semester (bisa 0 untuk "Semua Semester")
     */
    public function setActive(Request $request)
    {
        $id = $request->id_semester;
        if ($id == 0) {
            Session::put('active_semester_id', 0);
            return response()->json(['success' => true]);
        }
        $request->validate([
            'id_semester' => 'required|exists:semester,id_semester'
        ]);
        Session::put('active_semester_id', $id);
        return response()->json(['success' => true]);
    }

    public function switch()
    {
        Session::forget('active_semester_id');
        return redirect()->route('pilih-semester')
            ->with('info', 'Silakan pilih semester baru.');
    }

    // ========== CRUD SEMESTER ==========
    public function daftar()
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama_semester', 'desc')
            ->get();

        return view('semester.daftar', compact('semesterList'));
    }

    public function tambah()
    {
        return view('semester.tambah');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nama_semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        if ($request->has('is_active')) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        Semester::create([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'is_active' => $request->is_active ?? false,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('semester.daftar')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        return view('semester.edit', compact('semester'));
    }

    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $request->validate([
            'nama_semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        if ($request->has('is_active')) {
            Semester::where('id_semester', '!=', $id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $semester->update([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'is_active' => $request->is_active ?? false,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('semester.daftar')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();
        return redirect()->route('semester.daftar')
            ->with('success', 'Semester berhasil dihapus.');
    }

    public function listJson()
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama_semester', 'desc')
            ->get(['id_semester', 'nama_semester', 'tahun_ajaran']);
        return response()->json($semesters);
    }
}