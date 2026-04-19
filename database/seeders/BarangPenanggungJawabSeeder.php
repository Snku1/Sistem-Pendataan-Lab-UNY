<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\PenanggungJawab;
use Illuminate\Support\Facades\DB;

class BarangPenanggungJawabSeeder extends Seeder
{
    public function run()
    {
        $barangs = Barang::all();
        $pjPonco = PenanggungJawab::where('email', 'poncowali@uny.ac.id')->first();
        $pjSiswi = PenanggungJawab::where('email', 'siswidwiayuriyanti@uny.ac.id')->first();

        foreach ($barangs as $barang) {
            DB::table('barang_penanggungjawab')->insert([
                [
                    'id_barang' => $barang->id_barang,
                    'id_pj' => $pjPonco->id_pj,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id_barang' => $barang->id_barang,
                    'id_pj' => $pjSiswi->id_pj,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }
    }
}