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
            LokasiSeeder::class,          // Lokasi laboratorium
            SemesterSeeder::class,        // Data semester (Ganjil/Genap)
            PenanggungJawabSeeder::class, // Penanggung jawab barang
            SettingSeeder::class,         // Pengaturan sistem
            BarangSeeder::class,          // Data barang + relasi penanggung jawab (sudah di-handle di dalam)
            // BarangPenanggungJawabSeeder::class, // Tidak perlu karena BarangSeeder sudah attach relasi
        ]);
    }
}