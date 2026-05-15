<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->string('nama_peminjam');
            $table->string('nim');
            $table->string('email');
            $table->date('tgl_penggunaan');
            $table->date('tgl_jatuh_tempo');
            $table->string('surat_peminjaman')->nullable();

            // Catatan saat baru menambahkan peminjaman (alasan pinjam/keperluan)
            $table->text('catatan_awal')->nullable();

            $table->enum('status_transaksi', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
};
