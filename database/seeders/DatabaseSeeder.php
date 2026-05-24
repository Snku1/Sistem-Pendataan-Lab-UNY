<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan seeder penting karena foreign key constraints
        $this->call([
            UsersSeeder::class,           // User admin (wajib pertama)
            SemesterSeeder::class,        // Data semester (Ganjil/Genap)
            SettingSeeder::class,         // Pengaturan sistem
        ]);
    }
}