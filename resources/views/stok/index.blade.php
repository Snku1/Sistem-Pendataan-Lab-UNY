@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen Stok</h2>
            <p class="text-muted">Pantau ketersediaan stok barang laboratorium</p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Stok</p>
                        <h3 class="fw-bold text-primary mb-0">{{ number_format($totalStok) }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-boxes text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Stok Baik</p>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($totalBaik) }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-check-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Stok Rusak</p>
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($totalRusak) }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Stok Hilang</p>
                        <h3 class="fw-bold text-danger mb-0">{{ number_format($totalHilang) }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fas fa-times-circle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahan info stok menipis/habis -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Stok Menipis (≤2)</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $stokMenipisCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="fas fa-times-circle text-danger fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Stok Habis</p>
                        <h3 class="fw-bold text-danger mb-0">{{ $stokHabisCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Cari Barang</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" value="{{ request('search') }}" placeholder="Nama barang, merk, atau kode...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Filter Stok</label>
                    <select name="filter" class="form-select form-select-sm rounded-pill">
                        <option value="">Semua</option>
                        <option value="menipis" {{ request('filter') == 'menipis' ? 'selected' : '' }}>Stok Menipis (≤3)</option>
                        <option value="habis" {{ request('filter') == 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary rounded-pill w-100">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-semibold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Daftar Stok Barang</h5>
                <small class="text-muted">
                    Menampilkan {{ $barang->firstItem() ?? 0 }} - {{ $barang->lastItem() ?? 0 }} dari total {{ $barang->total() }} barang
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" style="min-width: 800px; font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2">Kode</th>
                            <th>Nama Barang</th>
                            <th>Merk</th>
                            <th>Stok Total</th>
                            <th>Baik</th>
                            <th>Rusak</th>
                            <th>Hilang</th>
                            <th>Status</th>
                            <th class="pe-2">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barang as $b)
                        <tr>
                            <td class="ps-2">{{ $b->kode_barang }}</td>
                            <td class="fw-semibold">{{ $b->nama_barang }}</td>
                            <td>{{ $b->merk ?? '-' }}</td>
                            <td>{{ $b->stok }}</td>
                            <td class="text-success">{{ $b->jumlah_baik }}</td>
                            <td class="text-warning">{{ $b->jumlah_rusak }}</td>
                            <td class="text-danger">{{ $b->jumlah_hilang }}</td>
                            <td>
                                @if($b->stok == 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($b->stok <= 3)
                                    <span class="badge bg-warning">Menipis</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                            <td class="pe-2">{{ $b->lokasi->nama_lokasi ?? '-' }}</td>
                        <tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada data barang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $barang->firstItem() ?? 0 }} sampai {{ $barang->lastItem() ?? 0 }} dari total {{ $barang->total() }} barang
                </div>
                <div>
                    {{ $barang->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection