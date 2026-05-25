<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique('barang_kode_barang_unique');
            
            // Tambahkan composite unique (kode_barang, id_lab)
            $table->unique(['kode_barang', 'id_lab']);
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropUnique(['kode_barang', 'id_lab']);
            $table->unique('kode_barang');
        });
    }
};