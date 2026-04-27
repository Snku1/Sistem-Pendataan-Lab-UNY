<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_penanggung_jawab')->nullable()->after('id_user');
            $table->foreign('id_penanggung_jawab')->references('id_pj')->on('penanggung_jawab')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_penanggung_jawab']);
            $table->dropColumn('id_penanggung_jawab');
        });
    }
};