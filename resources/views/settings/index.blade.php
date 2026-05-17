@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pengaturan Sistem</h2>
            <p class="text-muted mb-0">Kelola konfigurasi laboratorium dan notifikasi email.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <!-- Informasi Laboratorium -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-building me-2 text-primary"></i>Informasi Laboratorium</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update.lab') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Laboratorium</label>
                            <input type="text" name="lab_name" class="form-control rounded-pill @error('lab_name') is-invalid @enderror" 
                                   value="{{ old('lab_name', $settings['lab_name']) }}" required>
                            @error('lab_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="lab_address" class="form-control rounded-3 @error('lab_address') is-invalid @enderror" rows="2">{{ old('lab_address', $settings['lab_address']) }}</textarea>
                            @error('lab_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" name="lab_phone" class="form-control rounded-pill @error('lab_phone') is-invalid @enderror" 
                                   value="{{ old('lab_phone', $settings['lab_phone']) }}">
                            @error('lab_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Pengaturan Lab</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Pengaturan Notifikasi Email -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-envelope me-2 text-primary"></i>Notifikasi Email</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update.notification') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="notification_enabled" value="0">
                                <input type="checkbox" name="notification_enabled" class="form-check-input" id="notificationEnabled" value="1" 
                                       {{ $settings['notification_enabled'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="notificationEnabled">Aktifkan Notifikasi Email Jatuh Tempo</label>
                            </div>
                            <small class="text-muted">Mengirimkan email pengingat ke peminjam saat barang mendekati jatuh tempo.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kirim Pengingat (hari sebelum jatuh tempo)</label>
                            <input type="number" name="notification_days_before" class="form-control rounded-pill @error('notification_days_before') is-invalid @enderror" 
                                   value="{{ old('notification_days_before', $settings['notification_days_before']) }}" min="1" max="30">
                            <small class="text-muted">Contoh: 2 berarti email akan dikirim 2 hari sebelum tanggal jatuh tempo.</small>
                            @error('notification_days_before')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Notifikasi</button>
                    </form>

                    <hr class="my-4">

                    <form action="{{ route('settings.send-notification') }}" method="POST">
                        @csrf
                        <div class="alert alert-info bg-opacity-10 rounded-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Uji Cepat:</strong> Klik tombol di bawah untuk mengirim pengingat sekarang (tanpa menunggu jadwal harian).
                        </div>
                        <button type="submit" class="btn btn-warning rounded-pill px-4">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Notifikasi Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection