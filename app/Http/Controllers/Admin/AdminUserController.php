<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('laboratorium')->orderBy('created_at', 'asc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $laboratorium = Laboratorium::orderBy('nama_lab')->get();
        return view('admin.users.create', compact('laboratorium'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,koorlap,teknisi',
            'id_lab' => 'required_if:role,!=,admin|nullable|exists:laboratorium,id_lab',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_lab' => ($request->role == 'admin') ? null : $request->id_lab,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $laboratorium = Laboratorium::orderBy('nama_lab')->get();
        return view('admin.users.edit', compact('user', 'laboratorium'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'role' => 'required|in:admin,koorlap,teknisi',
            'id_lab' => 'required_if:role,!=,admin|nullable|exists:laboratorium,id_lab',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'id_lab' => ($request->role == 'admin') ? null : $request->id_lab,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}