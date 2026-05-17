<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel barang
        Schema::table('barang', function (Blueprint $table) {
            if (Schema::hasColumn('barang', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('barang', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            $table->unsignedBigInteger('id_semester')->nullable()->after('keterangan');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
        });

        // 2. Tabel barang_masuk
        Schema::table('barang_masuk', function (Blueprint $table) {
            if (Schema::hasColumn('barang_masuk', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('barang_masuk', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            $table->unsignedBigInteger('id_semester')->nullable()->after('sumber');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
        });

        // 3. Tabel peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('peminjaman', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            $table->unsignedBigInteger('id_semester')->nullable()->after('tanggal_jatuh_tempo');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
        });

        // 4. Tabel peminjaman_detail
        Schema::table('peminjaman_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman_detail', 'id_semester')) {
                $table->unsignedBigInteger('id_semester')->nullable()->after('status_item');
                $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
            }
        });

        // 5. Tabel stok_opname
        Schema::table('stok_opname', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_opname', 'id_semester')) {
                $table->unsignedBigInteger('id_semester')->nullable()->after('keterangan');
                $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
            }
        });

        // 6. Tabel stok_opname_detail
        Schema::table('stok_opname_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_opname_detail', 'id_semester')) {
                $table->unsignedBigInteger('id_semester')->nullable()->after('catatan');
                $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
            }
        });

        // 7. Tabel riwayat_stok
        if (Schema::hasTable('riwayat_stok')) {
            Schema::table('riwayat_stok', function (Blueprint $table) {
                if (!Schema::hasColumn('riwayat_stok', 'id_semester')) {
                    $table->unsignedBigInteger('id_semester')->nullable()->after('alasan');
                    $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('set null');
                }
            });
        }
    }

    public function down()
    {
        $tables = ['barang', 'barang_masuk', 'peminjaman', 'peminjaman_detail', 'stok_opname', 'stok_opname_detail', 'riwayat_stok'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'id_semester')) {
                        $blueprint->dropForeign(['id_semester']);
                        $blueprint->dropColumn('id_semester');
                    }
                });
            }
        }
    }
};