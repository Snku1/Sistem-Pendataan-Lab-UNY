<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // 1. Hapus foreign key constraints yang ada (untuk id_lab, id_lokasi, id_semester)
            $table->dropForeign(['id_lab']);
            $table->dropForeign(['id_lokasi']);
            $table->dropForeign(['id_semester']);

            // 2. Ubah kolom menjadi NOT NULL
            $table->unsignedBigInteger('id_lab')->nullable(false)->change();
            $table->unsignedBigInteger('id_semester')->nullable(false)->change();
            // id_lokasi mungkin sudah NOT NULL? Biarkan saja, jika belum bisa diubah juga
            $table->unsignedBigInteger('id_lokasi')->nullable(false)->change();

            // 3. Hapus unique index lama pada kode_barang (jika ada)
            //    Jika ada indeks unik dengan nama 'barang_kode_barang_unique', drop dulu
            $table->dropUnique('barang_kode_barang_unique');

            // 4. Tambahkan unique composite key (kode_barang, id_lab, id_semester)
            $table->unique(['kode_barang', 'id_lab', 'id_semester'], 'barang_kode_lab_semester_unique');

            // 5. Tambahkan kembali foreign key constraints dengan ON DELETE RESTRICT
            $table->foreign('id_lab')
                  ->references('id_lab')
                  ->on('laboratorium')
                  ->onDelete('restrict');
            $table->foreign('id_lokasi')
                  ->references('id_lokasi')
                  ->on('lokasi')
                  ->onDelete('restrict');
            $table->foreign('id_semester')
                  ->references('id_semester')
                  ->on('semester')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Hapus foreign key baru
            $table->dropForeign(['id_lab']);
            $table->dropForeign(['id_lokasi']);
            $table->dropForeign(['id_semester']);

            // Hapus unique composite key
            $table->dropUnique('barang_kode_lab_semester_unique');

            // Kembalikan kolom ke nullable
            $table->unsignedBigInteger('id_lab')->nullable()->change();
            $table->unsignedBigInteger('id_semester')->nullable()->change();
            $table->unsignedBigInteger('id_lokasi')->nullable()->change();

            // Kembalikan foreign key constraints seperti semula (dengan ON DELETE SET NULL)
            $table->foreign('id_lab')
                  ->references('id_lab')
                  ->on('laboratorium')
                  ->onDelete('set null');
            $table->foreign('id_lokasi')
                  ->references('id_lokasi')
                  ->on('lokasi')
                  ->onDelete('set null');
            $table->foreign('id_semester')
                  ->references('id_semester')
                  ->on('semester')
                  ->onDelete('set null');
        });
    }
};