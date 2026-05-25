<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stok_opname', function (Blueprint $table) {
            $table->dropUnique('stok_opname_kode_opname_unique');
            $table->unique(['kode_opname', 'id_lab']);
        });
    }

    public function down()
    {
        Schema::table('stok_opname', function (Blueprint $table) {
            $table->dropUnique(['kode_opname', 'id_lab']);
            $table->unique('kode_opname');
        });
    }
};