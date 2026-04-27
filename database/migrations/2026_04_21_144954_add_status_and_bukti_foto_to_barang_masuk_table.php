<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'diterima'])->default('menunggu')->after('sumber');
            $table->string('bukti_foto')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn(['status', 'bukti_foto']);
        });
    }
};