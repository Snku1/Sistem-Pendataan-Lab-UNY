<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            LokasiSeeder::class,
            PenanggungJawabSeeder::class,
            BarangSeeder::class,
            BarangPenanggungJawabSeeder::class,
        ]);
    }
}