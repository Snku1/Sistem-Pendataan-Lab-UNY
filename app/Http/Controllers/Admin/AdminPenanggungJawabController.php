<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenanggungJawab;
use App\Models\Laboratorium;
use Illuminate\Http\Request;

class AdminPenanggungJawabController extends Controller
{
    public function index()
    {
        $list = PenanggungJawab::with('laboratorium')->orderBy('created_at', 'asc')->paginate(10);
        return view('admin.penanggung-jawab.index', compact('list'));
    }

    public function create()
    {
        $laboratorium = Laboratorium::orderBy('nama_lab')->get();
        return view('admin.penanggung-jawab.create', compact('laboratorium'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pj' => 'required|string|max:255',
            'no_kontak' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'id_lab' => 'nullable|exists:laboratorium,id_lab',
        ]);

        PenanggungJawab::create($request->only(['nama_pj', 'no_kontak', 'email', 'id_lab']));

        return redirect()->route('admin.penanggung-jawab.index')->with('success', 'Penanggung jawab berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pj = PenanggungJawab::findOrFail($id);
        $laboratorium = Laboratorium::orderBy('nama_lab')->get();
        return view('admin.penanggung-jawab.edit', compact('pj', 'laboratorium'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pj' => 'required|string|max:255',
            'no_kontak' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'id_lab' => 'nullable|exists:laboratorium,id_lab',
        ]);

        $pj = PenanggungJawab::findOrFail($id);
        $pj->update($request->only(['nama_pj', 'no_kontak', 'email', 'id_lab']));

        return redirect()->route('admin.penanggung-jawab.index')->with('success', 'Penanggung jawab berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pj = PenanggungJawab::findOrFail($id);
        $pj->delete();

        return redirect()->route('admin.penanggung-jawab.index')->with('success', 'Penanggung jawab berhasil dihapus.');
    }
}