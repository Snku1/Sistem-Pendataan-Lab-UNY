<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Hapus entri setting yang tidak diperlukan lagi
        $keysToRemove = [
            'lab_logo',
            'lab_email',
            'notification_sender_email',
            'notification_sender_name',
        ];

        DB::table('settings')->whereIn('key', $keysToRemove)->delete();
    }

    public function down()
    {
        // Tidak perlu rollback karena data ini tidak kritis, bisa dikembalikan manual jika perlu
        // Namun jika ingin, Anda bisa menambahkan insert statement
    }
};