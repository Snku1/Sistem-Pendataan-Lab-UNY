<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('jumlah_rusak')->default(0)->after('stok');
            $table->integer('jumlah_hilang')->default(0)->after('jumlah_rusak');
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['jumlah_rusak', 'jumlah_hilang']);
        });
    }
};