<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lokasi;

class LokasiSeeder extends Seeder
{
    public function run()
    {
        Lokasi::create([
            'nama_lokasi' => 'Laboratorium AV & TV, Departemen Pendidikan Teknik Elektronika dan Informatika FT UNY - Gedung IDB Lantai 2'
        ]);
    }
}