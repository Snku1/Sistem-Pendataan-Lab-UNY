<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->enum('kondisi_penerimaan', ['baik', 'rusak', 'tidak_sesuai'])->nullable()->after('status');
            $table->text('catatan_pemeriksaan')->nullable()->after('kondisi_penerimaan');
        });
    }

    public function down()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn(['kondisi_penerimaan', 'catatan_pemeriksaan']);
        });
    }
};