<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        // Data semester untuk tahun ajaran 2024/2025
        Semester::create([
            'nama_semester' => 'Ganjil',
            'tahun_ajaran' => '2024/2025',
            'is_active' => true,
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31'
        ]);

        Semester::create([
            'nama_semester' => 'Genap',
            'tahun_ajaran' => '2024/2025',
            'is_active' => false,
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30'
        ]);

        // Data semester untuk tahun ajaran 2025/2026
        Semester::create([
            'nama_semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'is_active' => false,
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2025-12-31'
        ]);

        Semester::create([
            'nama_semester' => 'Genap',
            'tahun_ajaran' => '2025/2026',
            'is_active' => false,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-06-30'
        ]);
    }
}