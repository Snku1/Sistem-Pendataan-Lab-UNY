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
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLaboratoriumController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLokasiController;
use App\Http\Controllers\Admin\AdminPenanggungJawabController;
use App\Http\Controllers\Admin\AdminSettingController;
use Illuminate\Support\Facades\Route;

// ==================== HALAMAN DEPAN ====================
Route::get('/', function () {
    return redirect('/login');
});

// ==================== ROUTE LOGIN KHUSUS ADMIN ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('logout');
});

// ==================== ROUTE AUTHENTIKASI UMUM (BREEZE) ====================
require __DIR__ . '/auth.php';

// ==================== ROUTE YANG MEMERLUKAN LOGIN TAPI BELUM PASTI SEMESTER ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/pilih-semester', [SemesterController::class, 'index'])->name('pilih-semester');
    Route::post('/pilih-semester', [SemesterController::class, 'store'])->name('pilih-semester.store');
    Route::post('/set-semester', [SemesterController::class, 'setActive'])->name('set-semester');
    Route::get('/ganti-semester', [SemesterController::class, 'switch'])->name('ganti-semester');
    Route::get('/semester/list', [SemesterController::class, 'listJson'])->name('semester.list');

    // Manajemen Semester
    Route::prefix('semester')->name('semester.')->group(function () {
        Route::get('/', [SemesterController::class, 'daftar'])->name('daftar');
        Route::get('/tambah', [SemesterController::class, 'tambah'])->name('tambah');
        Route::post('/tambah', [SemesterController::class, 'simpan'])->name('simpan');
        Route::get('/edit/{id}', [SemesterController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [SemesterController::class, 'update'])->name('update');
        Route::delete('/{id}', [SemesterController::class, 'hapus'])->name('hapus');
    });
});

// ==================== PANEL ADMIN (HANYA ROLE ADMIN) ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('laboratorium', AdminLaboratoriumController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('lokasi', AdminLokasiController::class);
    Route::resource('penanggung-jawab', AdminPenanggungJawabController::class);
    Route::get('settings/lab', [AdminSettingController::class, 'labForm'])->name('settings.lab');
    Route::put('settings/lab', [AdminSettingController::class, 'updateLab'])->name('settings.lab.update');
});

// ==================== DASHBOARD & APLIKASI UTAMA UNTUK KOORLAP DAN TEKNISI ====================
Route::middleware(['auth', 'role:koorlap,teknisi', 'check.semester'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/create', [BarangController::class, 'create'])->name('create');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
    });

    // Barang Masuk
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

    // Peminjaman
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/', [PeminjamanController::class, 'index'])->name('index');
        Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])->name('riwayat');
        Route::get('/create', [PeminjamanController::class, 'create'])->name('create');
        Route::post('/', [PeminjamanController::class, 'store'])->name('store');
        Route::get('/{id}', [PeminjamanController::class, 'show'])->name('show');
        Route::get('/{id}/pengembalian', [PeminjamanController::class, 'formPengembalian'])->name('form-pengembalian');
        Route::put('/{id}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])->name('proses-pengembalian');
        Route::delete('/{id}', [PeminjamanController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/export-detail-pdf', [PeminjamanController::class, 'exportDetailPdf'])->name('export-detail-pdf');
        Route::get('/{id}/export-detail-csv', [PeminjamanController::class, 'exportDetailCsv'])->name('export-detail-csv');
    });

    // Stok
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('index');
    });

    // Stok Opname
    Route::prefix('stok-opname')->name('stok-opname.')->group(function () {
        Route::get('/', [StokOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StokOpnameController::class, 'create'])->name('create');
        Route::post('/', [StokOpnameController::class, 'store'])->name('store');
        Route::get('/{id}', [StokOpnameController::class, 'show'])->name('show');
        Route::delete('/{id}', [StokOpnameController::class, 'destroy'])->name('destroy');
    });

    // Riwayat
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/stok', [RiwayatController::class, 'stok'])->name('stok');
        Route::get('/kondisi', [RiwayatController::class, 'kondisi'])->name('kondisi');
        Route::get('/aktivitas', [RiwayatController::class, 'aktivitas'])->name('aktivitas');
        Route::get('/aktivitas/export', [RiwayatController::class, 'exportCsv'])->name('aktivitas.export');
        Route::get('/barang-masuk', [RiwayatController::class, 'barangMasuk'])->name('barang-masuk');
        Route::get('/peminjaman', [RiwayatController::class, 'peminjaman'])->name('peminjaman');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-pdf/{jenis}', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-csv/{jenis}', [LaporanController::class, 'exportCsv'])->name('export-csv');
        Route::get('/export-excel/{jenis}', [LaporanController::class, 'exportExcel'])->name('export-excel'); // <-- TAMBAHKAN INI
    });

    // ==================== PENGATURAN SISTEM PER LAB (KOORLAP/TEKNISI) ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/lab', [SettingController::class, 'updateLab'])->name('update.lab');
        Route::put('/notification', [SettingController::class, 'updateNotification'])->name('update.notification');
        Route::post('/send-notification', [SettingController::class, 'sendNotificationNow'])->name('send-notification');
    });

    // ==================== MANAJEMEN USER (HANYA KOORLAP) ====================
    Route::prefix('user')->name('user.')->middleware('role:koorlap')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Profil (semua koorlap & teknisi)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});