<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('kondisi');
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik')->after('jumlah_hilang');
        });
    }
};