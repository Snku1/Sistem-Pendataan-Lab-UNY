<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class AdminLokasiController extends Controller
{
    public function index()
    {
        $lokasiList = Lokasi::orderBy('created_at', 'asc')->paginate(10);
        return view('admin.lokasi.index', compact('lokasiList'));
    }

    public function create()
    {
        return view('admin.lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
        ]);

        Lokasi::create($request->only('nama_lokasi'));

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return view('admin.lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
        ]);

        $lokasi = Lokasi::findOrFail($id);
        $lokasi->update($request->only('nama_lokasi'));

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}