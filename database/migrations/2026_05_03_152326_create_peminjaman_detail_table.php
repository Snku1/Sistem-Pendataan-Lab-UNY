<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peminjaman_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_peminjaman');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->enum('kondisi_setelah', ['baik', 'rusak', 'hilang'])->nullable();
            $table->text('catatan_kembali')->nullable();
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->enum('status_item', ['dipinjam', 'kembali'])->default('dipinjam');
            $table->timestamps();

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman_detail');
    }
};