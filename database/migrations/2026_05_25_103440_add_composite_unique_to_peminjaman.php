<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Hapus unique constraint lama pada kolom kode_transaksi
            $table->dropUnique('peminjaman_kode_transaksi_unique');
            
            // Tambahkan composite unique (kode_transaksi, id_lab)
            $table->unique(['kode_transaksi', 'id_lab'], 'peminjaman_kode_transaksi_id_lab_unique');
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropUnique('peminjaman_kode_transaksi_id_lab_unique');
            $table->unique('kode_transaksi', 'peminjaman_kode_transaksi_unique');
        });
    }
};