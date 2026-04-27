<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/create', [BarangController::class, 'create'])->name('create');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('barang-masuk')->name('barang-masuk.')->group(function () {
        Route::get('/', [BarangMasukController::class, 'index'])->name('index');
        Route::get('/create', [BarangMasukController::class, 'create'])->name('create');
        Route::post('/', [BarangMasukController::class, 'store'])->name('store');
        Route::get('/{id}', [BarangMasukController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BarangMasukController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangMasukController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [BarangMasukController::class, 'updateStatus'])->name('update-status');
        Route::put('/{id}/detail', [BarangMasukController::class, 'updateDetail'])->name('update-detail');
        Route::delete('/{id}', [BarangMasukController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/detail-pemeriksaan', [BarangMasukController::class, 'detailPemeriksaan'])->name('detail-pemeriksaan');
        Route::get('/{id}/kondisi-awal', [BarangMasukController::class, 'editKondisiAwal'])->name('kondisi-awal');
        Route::put('/{id}/kondisi-awal', [BarangMasukController::class, 'updateKondisiAwal'])->name('update-kondisi-awal');
    });
    
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/kondisi', [BarangController::class, 'monitoringKondisi'])->name('kondisi');
        Route::patch('/{id}/kondisi', [BarangController::class, 'updateKondisi'])->name('update-kondisi');
    });
    
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [BarangController::class, 'manajemenStok'])->name('index');
        Route::get('/menipis', [BarangController::class, 'stokMenipis'])->name('menipis');
    });
    
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/stok', [RiwayatController::class, 'stok'])->name('stok');
        Route::get('/kondisi', [RiwayatController::class, 'kondisi'])->name('kondisi');
        Route::get('/aktivitas', [RiwayatController::class, 'aktivitas'])->name('aktivitas');
        Route::get('/barang-masuk', [RiwayatController::class, 'barangMasuk'])->name('barang-masuk');
    });
    
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
        Route::get('/barang-rusak', [LaporanController::class, 'barangRusak'])->name('barang-rusak');
        Route::get('/barang-hilang', [LaporanController::class, 'barangHilang'])->name('barang-hilang');
        Route::get('/semester', [LaporanController::class, 'perSemester'])->name('semester');
        Route::get('/export-pdf/{jenis}', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel/{jenis}', [LaporanController::class, 'exportExcel'])->name('export-excel');
    });
});