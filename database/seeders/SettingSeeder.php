<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        // Informasi Laboratorium
        Setting::set('lab_name', 'Laboratorium UNY');
        Setting::set('lab_address', '');
        Setting::set('lab_phone', '');

        // Pengaturan Notifikasi
        Setting::set('notification_enabled', '1');
        Setting::set('notification_days_before', '2');
    }
}