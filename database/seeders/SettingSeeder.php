<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        // Informasi Laboratorium
        Setting::set('lab_name', 'Laboratorium AV & TV');
        Setting::set('lab_address', 'Gedung IDB Lantai 2, FT UNY');
        Setting::set('lab_phone', '(0274) 123456');

        // Pengaturan Notifikasi
        Setting::set('notification_enabled', '1');
        Setting::set('notification_days_before', '2');
    }
}