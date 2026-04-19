<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Route home / landing page
Route::get('/', function () {
    return redirect('/login');
});

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// ==================== ROUTE YANG MEMERLUKAN LOGIN ====================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ==================== MANAJEMEN BARANG (CRUD) ====================
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');      // Menampilkan daftar barang
        Route::get('/create', [BarangController::class, 'create'])->name('create'); // Form tambah barang
        Route::post('/', [BarangController::class, 'store'])->name('store');     // Simpan barang baru
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');   // Form edit barang
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');    // Update barang
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy'); // Hapus barang
    });
    
    // ==================== BARANG MASUK ====================
    Route::prefix('barang-masuk')->name('barang-masuk.')->group(function () {
        Route::get('/', [BarangMasukController::class, 'index'])->name('index');      // Riwayat barang masuk
        Route::get('/create', [BarangMasukController::class, 'create'])->name('create'); // Form catat barang masuk
        Route::post('/', [BarangMasukController::class, 'store'])->name('store');     // Simpan barang masuk
        Route::delete('/{id}', [BarangMasukController::class, 'destroy'])->name('destroy'); // Hapus catatan barang masuk
    });
    
    // ==================== MONITORING KONDISI ====================
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/kondisi', [BarangController::class, 'monitoringKondisi'])->name('kondisi');
        Route::patch('/{id}/kondisi', [BarangController::class, 'updateKondisi'])->name('update-kondisi');
    });
    
    // ==================== MANAJEMEN STOK ====================
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [BarangController::class, 'manajemenStok'])->name('index');
        Route::get('/menipis', [BarangController::class, 'stokMenipis'])->name('menipis');
    });
    
    // ==================== RIWAYAT ====================
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/stok', [RiwayatController::class, 'stok'])->name('stok');
        Route::get('/kondisi', [RiwayatController::class, 'kondisi'])->name('kondisi');
        Route::get('/aktivitas', [RiwayatController::class, 'aktivitas'])->name('aktivitas');
        Route::get('/barang-masuk', [RiwayatController::class, 'barangMasuk'])->name('barang-masuk');
    });
    
    // ==================== LAPORAN ====================
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