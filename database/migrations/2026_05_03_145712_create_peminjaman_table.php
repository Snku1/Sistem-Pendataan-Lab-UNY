<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->string('kode_transaksi', 50)->unique();
            $table->string('nama_peminjam', 100);
            $table->string('nim', 20)->nullable();
            $table->string('email', 100);
            $table->date('tanggal_penggunaan');
            $table->date('tanggal_jatuh_tempo');
            $table->string('surat_peminjaman')->nullable(); // path file
            $table->text('catatan_awal')->nullable();
            $table->enum('status_transaksi', ['aktif', 'selesai'])->default('aktif');
            $table->unsignedBigInteger('id_user'); // petugas pencatat
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
};