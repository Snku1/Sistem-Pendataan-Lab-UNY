<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\Laboratorium;

return new class extends Migration
{
    public function up()
    {
        // Ambil id_lab dari laboratorium pertama (atau buat jika belum ada)
        $lab = Laboratorium::first();
        if (!$lab) {
            // Buat lab default jika belum ada
            $lab = Laboratorium::create([
                'nama_lab' => 'Default Lab',
                'lokasi' => 'Default Location',
                'id_penanggung_jawab' => null
            ]);
        }

        // Isi id_lab untuk tabel yang memiliki data (jika masih null)
        $tables = [
            'penanggung_jawab',
            'settings',
            'peminjaman_detail',
            'stok_opname_detail',
            'riwayat_stok',
            'riwayat_kondisi',
            'log_aktivitas'
        ];

        foreach ($tables as $table) {
            DB::table($table)->whereNull('id_lab')->update(['id_lab' => $lab->id_lab]);
        }
    }

    public function down()
    {
        // Tidak perlu rollback, bersifat data migration
    }
};