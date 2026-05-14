<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id('id_opname');
            $table->string('kode_opname')->unique();
            $table->date('tanggal_opname');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_opname');
    }
};