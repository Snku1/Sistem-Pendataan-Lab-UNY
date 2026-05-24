@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Header dengan sapaan dan tanggal -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Dashboard Admin</h2>
            <p class="text-muted">Selamat datang kembali, {{ Auth::user()->name ?? 'Admin' }}!</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <!-- Statistik Utama - Responsive Grid -->
    <div class="row g-4">
        <!-- Card Total User -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-2">Total Pengguna</p>
                            <h3 class="fw-bold display-6 text-primary mb-0">{{ number_format($totalUsers ?? 0) }}</h3>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-users me-1"></i> Seluruh akun terdaftar
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Lokasi -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-2">Total Lokasi</p>
                            <h3 class="fw-bold display-6 text-success mb-0">{{ number_format($totalLokasi ?? 0) }}</h3>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-map-marker-alt me-1"></i> Jumlah lokasi laboratorium
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Penanggung Jawab -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-2">Penanggung Jawab</p>
                            <h3 class="fw-bold display-6 text-warning mb-0">{{ number_format($totalPenanggungJawab ?? 0) }}</h3>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-user-tie me-1"></i> Penanggung jawab per laboratorium
                            </small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-user-tie fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opsi: Jika nanti ada data tambahan seperti total barang, bisa ditambah baris baru -->
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,0.08) !important;
    }
    .card {
        border: none;
        background: #ffffff;
    }
    @media (max-width: 768px) {
        .display-6 {
            font-size: 1.8rem;
        }
        .card-body {
            padding: 1.25rem !important;
        }
    }
</style>
@endsection