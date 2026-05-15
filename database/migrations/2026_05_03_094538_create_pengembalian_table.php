<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_pengembalian');
            $table->unsignedBigInteger('id_peminjaman');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah_dikembalikan');
            $table->date('tanggal_pengembalian');
            $table->enum('kondisi_setelah', ['baik', 'rusak', 'hilang'])->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable(); // petugas
            $table->timestamps();

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengembalian');
    }
};