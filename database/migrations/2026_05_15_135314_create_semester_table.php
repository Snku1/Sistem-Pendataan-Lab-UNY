<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->id('id_semester');
            $table->enum('nama_semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 20); // misal 2024/2025
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('semester');
    }
};