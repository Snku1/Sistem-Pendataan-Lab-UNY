<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stok_opname_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_opname');
            $table->unsignedBigInteger('id_barang');
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_opname')->references('id_opname')->on('stok_opname')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_opname_detail');
    }
};