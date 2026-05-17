<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route home / landing page
Route::get('/', function () {
    return redirect('/login');
});

// Auth routes (dari Breeze)
require __DIR__ . '/auth.php';

// ==================== ROUTE YANG MEMERLUKAN LOGIN TAPI TIDAK PERLU SEMESTER ====================
Route::middleware(['auth'])->group(function () {
    // Halaman pilih semester (belum ada semester aktif)
    Route::get('/pilih-semester', [SemesterController::class, 'index'])->name('pilih-semester');

    // Route untuk menyimpan pilihan semester dari form biasa (hanya untuk id semester tertentu, tidak termasuk 0)
    Route::post('/pilih-semester', [SemesterController::class, 'store'])->name('pilih-semester.store');

    // Route untuk mengubah semester dari dropdown (AJAX) - mendukung id_semester = 0 (Semua Semester)
    Route::post('/set-semester', [SemesterController::class, 'setActive'])->name('set-semester');

    // Ganti semester (hapus session, redirect ke pilih semester)
    Route::get('/ganti-semester', [SemesterController::class, 'switch'])->name('ganti-semester');

    // API untuk mengambil daftar semester (digunakan oleh dropdown)
    Route::get('/semester/list', [SemesterController::class, 'listJson'])->name('semester.list');

    // ==================== MANAJEMEN SEMESTER (ADMIN) ====================
    Route::prefix('semester')->name('semester.')->group(function () {
        Route::get('/', [SemesterController::class, 'daftar'])->name('daftar');
        Route::get('/tambah', [SemesterController::class, 'tambah'])->name('tambah');
        Route::post('/tambah', [SemesterController::class, 'simpan'])->name('simpan');
        Route::get('/edit/{id}', [SemesterController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [SemesterController::class, 'update'])->name('update');
        Route::delete('/{id}', [SemesterController::class, 'hapus'])->name('hapus');
    });
});

// ==================== ROUTE YANG MEMERLUKAN LOGIN DAN SEMESTER AKTIF ====================
// Middleware check.semester harus mengizinkan nilai session active_semester_id = 0 (Semua Semester)
Route::middleware(['auth', 'check.semester'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================== MANAJEMEN BARANG (CRUD) ====================
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/create', [BarangController::class, 'create'])->name('create');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
    });

    // ==================== BARANG MASUK ====================
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

    // ==================== PEMINJAMAN ====================
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/', [PeminjamanController::class, 'index'])->name('index');
        Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])->name('riwayat');
        Route::get('/create', [PeminjamanController::class, 'create'])->name('create');
        Route::post('/', [PeminjamanController::class, 'store'])->name('store');
        Route::get('/{id}', [PeminjamanController::class, 'show'])->name('show');
        Route::get('/{id}/pengembalian', [PeminjamanController::class, 'formPengembalian'])->name('form-pengembalian');
        Route::put('/{id}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])->name('proses-pengembalian');
        Route::delete('/{id}', [PeminjamanController::class, 'destroy'])->name('destroy');

        // ==================== EKSPOR DETAIL PEMINJAMAN (PDF & CSV) ====================
        Route::get('/{id}/export-detail-pdf', [PeminjamanController::class, 'exportDetailPdf'])->name('export-detail-pdf');
        Route::get('/{id}/export-detail-csv', [PeminjamanController::class, 'exportDetailCsv'])->name('export-detail-csv');
    });

    // ==================== MANAJEMEN STOK ====================
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('index');
    });

    // ==================== STOK OPNAME ====================
    Route::prefix('stok-opname')->name('stok-opname.')->group(function () {
        Route::get('/', [StokOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StokOpnameController::class, 'create'])->name('create');
        Route::post('/', [StokOpnameController::class, 'store'])->name('store');
        Route::get('/{id}', [StokOpnameController::class, 'show'])->name('show');
        Route::delete('/{id}', [StokOpnameController::class, 'destroy'])->name('destroy');
    });

    // ==================== RIWAYAT ====================
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/stok', [RiwayatController::class, 'stok'])->name('stok');
        Route::get('/kondisi', [RiwayatController::class, 'kondisi'])->name('kondisi');
        Route::get('/aktivitas', [RiwayatController::class, 'aktivitas'])->name('aktivitas');
        Route::get('/aktivitas/export', [RiwayatController::class, 'exportCsv'])->name('aktivitas.export');
        Route::get('/barang-masuk', [RiwayatController::class, 'barangMasuk'])->name('barang-masuk');
        Route::get('/peminjaman', [RiwayatController::class, 'peminjaman'])->name('peminjaman');
    });

    // ==================== LAPORAN ====================
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-pdf/{jenis}', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-csv/{jenis}', [LaporanController::class, 'exportCsv'])->name('export-csv');
    });

    // ==================== MANAJEMEN USER (CRUD) ====================
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // ==================== PENGATURAN SISTEM ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/lab', [SettingController::class, 'updateLab'])->name('update.lab');
        Route::put('/notification', [SettingController::class, 'updateNotification'])->name('update.notification');
        Route::post('/send-notification', [SettingController::class, 'sendNotificationNow'])->name('send-notification'); // <-- TAMBAHKAN INI
    });

    // ==================== PROFIL USER ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        // Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy'); // opsional
    });
});