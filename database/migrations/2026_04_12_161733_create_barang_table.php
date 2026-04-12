<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('merk')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kapasitas')->nullable();

            $table->unsignedBigInteger('id_lokasi');
            $table->integer('stok')->default(0);
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('id_lokasi')->references('id_lokasi')->on('lokasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
