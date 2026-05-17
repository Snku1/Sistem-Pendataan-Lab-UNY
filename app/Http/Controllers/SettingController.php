<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan; // <-- Tambahkan ini

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'lab_name' => Setting::get('lab_name', 'Laboratorium AV & TV'),
            'lab_address' => Setting::get('lab_address', ''),
            'lab_phone' => Setting::get('lab_phone', ''),
            'lab_email' => Setting::get('lab_email', ''),
            'lab_logo' => Setting::get('lab_logo', null),
            'notification_enabled' => Setting::get('notification_enabled', '1'),
            'notification_days_before' => Setting::get('notification_days_before', '2'),
            'notification_sender_email' => Setting::get('notification_sender_email', 'noreply@labavtv.com'),
            'notification_sender_name' => Setting::get('notification_sender_name', 'Sistem Lab AV & TV'),
        ];

        return view('settings.index', compact('settings'));
    }

    // Update Informasi Laboratorium
    public function updateLab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lab_name' => 'required|string|max:255',
            'lab_address' => 'nullable|string',
            'lab_phone' => 'nullable|string|max:50',
            'lab_email' => 'nullable|email|max:255',
            'lab_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('lab_name', $request->lab_name);
        Setting::set('lab_address', $request->lab_address);
        Setting::set('lab_phone', $request->lab_phone);
        Setting::set('lab_email', $request->lab_email);

        if ($request->hasFile('lab_logo')) {
            $oldLogo = Setting::get('lab_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('lab_logo')->store('logos', 'public');
            Setting::set('lab_logo', $path);
        }

        return redirect()->route('settings.index')->with('success', 'Informasi lab berhasil disimpan.');
    }

    // Di SettingController.php, method updateNotification
    public function updateNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_enabled' => 'required|boolean',
            'notification_days_before' => 'required|integer|min:1|max:30',
            // Hapus baris 'notification_sender_email' dan 'notification_sender_name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('notification_enabled', $request->notification_enabled);
        Setting::set('notification_days_before', $request->notification_days_before);
        // Hapus Setting::set('notification_sender_email', ...) dan Setting::set('notification_sender_name', ...)

        return redirect()->route('settings.index')->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }
    /**
     * Kirim notifikasi jatuh tempo secara manual (real-time)
     */
    public function sendNotificationNow()
    {
        try {
            Artisan::call('reminder:due-date');
            $output = Artisan::output();
            return redirect()->back()->with('success', 'Notifikasi berhasil dikirim. ' . $output);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }
}
