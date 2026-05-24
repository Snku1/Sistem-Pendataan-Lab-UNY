<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorium', function (Blueprint $table) {
            $table->id('id_lab');
            $table->string('nama_lab');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorium');
    }
};