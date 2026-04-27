<?php

namespace App\Console\Commands;

use App\Models\Barang;
use Illuminate\Console\Command;

class SyncKondisiBarang extends Command
{
    protected $signature = 'barang:sync-kondisi';
    protected $description = 'Sinkronisasi kondisi barang berdasarkan jumlah rusak dan hilang';

    public function handle()
    {
        $barangs = Barang::all();
        foreach ($barangs as $barang) {
            $barang->updateKondisiOtomatis();
            $barang->saveQuietly();
        }
        $this->info('Selesai mengupdate ' . $barangs->count() . ' barang.');
    }
}