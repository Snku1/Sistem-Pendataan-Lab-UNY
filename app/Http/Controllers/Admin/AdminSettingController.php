<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingController extends Controller
{
    public function labForm()
    {
        $labName = Setting::get('lab_name', 'Laboratorium UNY');
        return view('admin.settings.lab', compact('labName'));
    }

    public function updateLab(Request $request)
    {
        $request->validate([
            'lab_name' => 'required|string|max:255',
        ]);

        Setting::set('lab_name', $request->lab_name);

        // Hapus cache mail_from_name agar AppServiceProvider mengambil nilai baru
        Cache::forget('mail_from_name');

        return redirect()->route('admin.settings.lab')->with('success', 'Nama laboratorium berhasil diubah.');
    }
}