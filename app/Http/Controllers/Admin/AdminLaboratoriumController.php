<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminLaboratoriumController extends Controller
{
    public function index()
    {
        $laboratorium = Laboratorium::orderBy('created_at', 'asc')->paginate(10);
        return view('admin.laboratorium.index', compact('laboratorium'));
    }

    public function create()
    {
        return view('admin.laboratorium.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lab' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Laboratorium::create($request->only(['nama_lab']));

        return redirect()->route('admin.laboratorium.index')
            ->with('success', 'Laboratorium berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $laboratorium = Laboratorium::findOrFail($id);
        return view('admin.laboratorium.edit', compact('laboratorium'));
    }

    public function update(Request $request, $id)
    {
        $laboratorium = Laboratorium::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_lab' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $laboratorium->update($request->only(['nama_lab']));

        return redirect()->route('admin.laboratorium.index')
            ->with('success', 'Laboratorium berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $laboratorium = Laboratorium::findOrFail($id);

        if ($laboratorium->users()->exists()) {
            return redirect()->route('admin.laboratorium.index')
                ->with('error', 'Tidak dapat menghapus laboratorium karena masih memiliki user terdaftar.');
        }

        $laboratorium->delete();

        return redirect()->route('admin.laboratorium.index')
            ->with('success', 'Laboratorium berhasil dihapus.');
    }
}