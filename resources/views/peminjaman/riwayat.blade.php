@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Riwayat Peminjaman</h2>
            <p class="text-muted">Daftar peminjaman barang yang sudah selesai</p>
        </div>
        <div>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Aktif
            </a>
        </div>
    </div>

    <!-- Card Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Transaksi Selesai</p>
                        <h3 class="fw-bold text-primary mb-0">{{ $totalSelesai ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-check-circle text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Unit Dipinjam</p>
                        <h3 class="fw-bold text-success mb-0">{{ $totalBarangPernahDipinjam ?? 0 }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-boxes text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Rusak Setelah Peminjaman</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $rusakSetelahPeminjaman ?? 0 }} unit</h3>
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
                        <p class="text-muted mb-1 small">Hilang / Tidak Kembali</p>
                        <h3 class="fw-bold text-danger mb-0">{{ $hilangSetelahPeminjaman ?? 0 }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fas fa-times-circle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter (tanpa semester) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Cari Peminjaman</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" value="{{ request('search') }}" placeholder="Kode, nama, email...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-secondary rounded-pill flex-grow-1">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
            <small class="text-muted mt-2 d-block">Filter berdasarkan tanggal pengembalian barang (tanggal kembali aktual).</small>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-history me-2 text-primary"></i>Daftar Peminjaman Selesai</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" style="min-width: 1000px; font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2">Kode Transaksi</th>
                            <th>Peminjam</th>
                            <th>Email</th>
                            <th>Tgl Penggunaan</th>
                            <th>Tgl Kembali</th>
                            <th>Jumlah Item</th>
                            <th class="pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman as $p)
                        <tr>
                            <td class="ps-2">{{ $p->kode_transaksi }}</td>
                            <td>{{ $p->nama_peminjam }}@if($p->nim) <br><small class="text-muted">{{ $p->nim }}</small>@endif</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_penggunaan)->translatedFormat('d M Y') }}</td>
                            <td>
                                @php
                                $tglKembali = $p->details->where('status_item', 'kembali')->max('tanggal_kembali_aktual');
                                @endphp
                                {{ $tglKembali ? \Carbon\Carbon::parse($tglKembali)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td>{{ $p->details->sum('jumlah') }} unit ({{ $p->details->count() }} item)</td>
                            <td class="pe-2">
                                <a href="{{ route('peminjaman.show', $p->id_peminjaman) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada riwayat peminjaman</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $peminjaman->firstItem() ?? 0 }} sampai {{ $peminjaman->lastItem() ?? 0 }} dari {{ $peminjaman->total() }} data
                </div>
                <div>
                    {{ $peminjaman->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection