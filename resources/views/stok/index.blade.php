@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')
<div class="container-fluid px-0">
    <!-- Header dengan informasi semester aktif -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen Stok</h2>
            <p class="text-muted mb-0">
                Data pergerakan stok berdasarkan periode yang dipilih
            </p>
        </div>
        <div>
            @php
                $activeSemesterId = session('active_semester_id');
                $semesterLabel = 'Semua Semester';
                if ($activeSemesterId && $activeSemesterId != 0) {
                    $semester = App\Models\Semester::find($activeSemesterId);
                    if ($semester) {
                        $semesterLabel = $semester->nama_semester . ' - ' . $semester->tahun_ajaran;
                    }
                } elseif ($activeSemesterId == 0) {
                    $semesterLabel = 'Semua Semester';
                }
            @endphp
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                <i class="fas fa-calendar-alt me-1"></i> {{ $semesterLabel }}
            </span>
        </div>
    </div>

    <!-- Info periode yang sedang ditampilkan -->
    <div class="alert alert-light border rounded-4 shadow-sm mb-4 py-2 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="fas fa-chart-line text-primary me-2"></i>
                <strong>Periode laporan:</strong> 
                {{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d F Y') }} s.d. 
                {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}
            </div>
            @if($activeSemesterId && $activeSemesterId != 0)
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i> 
                    Rentang tanggal mengikuti semester aktif (bisa diubah manual di bawah)
                </div>
            @endif
        </div>
    </div>

    <!-- Card Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Stok Akhir</p>
                        <h3 class="fw-bold text-primary mb-0">{{ number_format($totalStokAkhir) }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-boxes text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Stok Masuk Periode</p>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($totalStokMasuk) }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-arrow-down text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Stok Keluar Periode</p>
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($totalStokKeluar) }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-arrow-up text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Barang Stok Menipis (≤2)</p>
                        <h3 class="fw-bold text-danger mb-0">{{ number_format($stokMenipisCount) }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Barang Stok Habis</p>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stokHabisCount) }}</h3>
                    </div>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="fas fa-times-circle text-dark fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter: Pencarian + Rentang Tanggal -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Cari Barang</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" 
                           value="{{ request('search') }}" placeholder="Kode atau nama barang...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control form-control-sm rounded-pill" 
                           value="{{ $tanggalAwal }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm rounded-pill" 
                           value="{{ $tanggalAkhir }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary rounded-pill flex-grow-1">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i> 
                Data stok masuk dan keluar dihitung berdasarkan rentang tanggal di atas. Stok awal dihitung secara otomatis dari data sebelum tanggal awal.
            </small>
        </div>
    </div>

    <!-- Tabel Rekap Stok -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-semibold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Rekap Stok Barang</h5>
                <small class="text-muted">
                    Menampilkan {{ $rekap->firstItem() ?? 0 }} - {{ $rekap->lastItem() ?? 0 }} dari total {{ $rekap->total() }} barang
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
                            <th>Stok Awal</th>
                            <th>Stok Masuk</th>
                            <th>Stok Keluar</th>
                            <th class="pe-2">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $item)
                        <tr>
                            <td class="ps-2">{{ $item->kode }}</td>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td>{{ $item->merk }}</td>
                            <td class="text-end">{{ number_format($item->stok_awal) }}</td>
                            <td class="text-end">{{ number_format($item->stok_masuk) }}</td>
                            <td class="text-end">{{ number_format($item->stok_keluar) }}</td>
                            <td class="pe-2 text-end fw-bold {{ $item->stok_akhir <= 2 ? 'text-danger' : '' }}">
                                {{ number_format($item->stok_akhir) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada data barang</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end">{{ number_format(collect($rekap->items())->sum('stok_awal')) }}</td>
                            <td class="text-end">{{ number_format(collect($rekap->items())->sum('stok_masuk')) }}</td>
                            <td class="text-end">{{ number_format(collect($rekap->items())->sum('stok_keluar')) }}</td>
                            <td class="text-end">{{ number_format(collect($rekap->items())->sum('stok_akhir')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $rekap->firstItem() ?? 0 }} sampai {{ $rekap->lastItem() ?? 0 }} dari total {{ $rekap->total() }} barang
                </div>
                <div>
                    {{ $rekap->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection