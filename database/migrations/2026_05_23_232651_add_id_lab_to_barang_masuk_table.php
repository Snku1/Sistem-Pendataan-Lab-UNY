<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lab')->nullable()->after('id_masuk');
            $table->foreign('id_lab')->references('id_lab')->on('laboratorium')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_lab']);
            $table->dropColumn('id_lab');
        });
    }
};