<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::set('lab_name', 'Laboratorium AV & TV');
        Setting::set('lab_address', 'Gedung IDB Lantai 2, FT UNY');
        Setting::set('lab_phone', '(0274) 123456');
        Setting::set('lab_email', 'lab@uny.ac.id');
        Setting::set('lab_logo', null);
        Setting::set('notification_enabled', '1');
        Setting::set('notification_days_before', '2');
        Setting::set('notification_sender_email', 'noreply@labavtv.com');
        Setting::set('notification_sender_name', 'Sistem Lab AV & TV');
    }
}