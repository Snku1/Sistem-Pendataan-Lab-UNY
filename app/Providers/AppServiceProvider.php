<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set mail from name dari database setting
        try {
            $labName = Cache::rememberForever('mail_from_name', function () {
                return Setting::get('lab_name', 'Laboratorium UNY');
            });
            Config::set('mail.from.name', $labName);
        } catch (\Exception $e) {
            // Jika database belum siap (misal saat migrasi), gunakan default dari env
            Config::set('mail.from.name', env('MAIL_FROM_NAME', 'Laboratorium UNY'));
        }
    }
}