<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            // Hapus unique constraint lama pada kolom 'key'
            $table->dropUnique('settings_key_unique');

            // Tambahkan composite unique (key, id_lab)
            $table->unique(['key', 'id_lab']);
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'id_lab']);
            $table->unique('key');
        });
    }
};