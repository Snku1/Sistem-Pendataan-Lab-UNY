<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToStokOpnameDetailTable extends Migration
{
    public function up()
    {
        Schema::table('stok_opname_detail', function (Blueprint $table) {
            $table->enum('keterangan', ['Kelebihan', 'Kekurangan', 'Sesuai'])
                  ->nullable()
                  ->after('selisih');
        });
    }

    public function down()
    {
        Schema::table('stok_opname_detail', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}