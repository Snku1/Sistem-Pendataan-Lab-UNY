<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $labId = auth()->user()->id_lab;
        $settings = [
            'lab_name' => Setting::get('lab_name', 'Laboratorium UNY', $labId),
            'lab_address' => Setting::get('lab_address', '', $labId),
            'lab_phone' => Setting::get('lab_phone', '', $labId),
            'lab_email' => Setting::get('lab_email', '', $labId),
            'lab_logo' => Setting::get('lab_logo', null, $labId),
            'notification_enabled' => Setting::get('notification_enabled', '1', $labId),
            'notification_days_before' => Setting::get('notification_days_before', '2', $labId),
            'notification_sender_email' => Setting::get('notification_sender_email', 'labuny.com', $labId),
            'notification_sender_name' => Setting::get('notification_sender_name', 'Laboratorium UNY', $labId),
        ];

        return view('settings.index', compact('settings'));
    }

    public function updateLab(Request $request)
    {
        $labId = auth()->user()->id_lab;

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

        Setting::set('lab_name', $request->lab_name, $labId);
        Setting::set('lab_address', $request->lab_address, $labId);
        Setting::set('lab_phone', $request->lab_phone, $labId);
        Setting::set('lab_email', $request->lab_email, $labId);

        if ($request->hasFile('lab_logo')) {
            $oldLogo = Setting::get('lab_logo', null, $labId);
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('lab_logo')->store('logos', 'public');
            Setting::set('lab_logo', $path, $labId);
        }

        // Hapus cache mail_from_name (global) karena nama lab global berubah
        Cache::forget('mail_from_name');

        return redirect()->route('settings.index')->with('success', 'Informasi lab berhasil disimpan.');
    }

    public function updateNotification(Request $request)
    {
        $labId = auth()->user()->id_lab;

        $validator = Validator::make($request->all(), [
            'notification_enabled' => 'required|boolean',
            'notification_days_before' => 'required|integer|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('notification_enabled', $request->notification_enabled, $labId);
        Setting::set('notification_days_before', $request->notification_days_before, $labId);

        return redirect()->route('settings.index')->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

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