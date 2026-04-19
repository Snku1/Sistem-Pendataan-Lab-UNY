<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenanggungJawab;

class PenanggungJawabSeeder extends Seeder
{
    public function run()
    {
        PenanggungJawab::create([
            'nama_pj' => 'Dr. Ponco Walipranoto, S.Pd.T., M.Pd.',
            'no_kontak' => '081227210230',
            'email' => 'poncowali@uny.ac.id',
        ]);

        PenanggungJawab::create([
            'nama_pj' => 'Siswi Dwi Ayuriyanti',
            'no_kontak' => '085743040345',
            'email' => 'siswidwiayuriyanti@uny.ac.id',
        ]);
    }
}