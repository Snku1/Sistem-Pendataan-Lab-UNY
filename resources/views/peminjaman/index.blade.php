@extends('layouts.app')

@section('title', 'Barang Digunakan')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Peminjaman Barang</h2>
            <p class="text-muted">Kelola dan pantau seluruh barang laboratorium yang sedang digunakan saat ini</p>
        </div>
        <div>
            <a href="{{ route('peminjaman.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Peminjaman Barang
            </a>
        </div>
    </div>

    <!-- Card Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Peminjaman Aktif</p>
                        <h3 class="fw-bold text-primary mb-0">{{ $totalAktif ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-clipboard-list text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Barang Dipinjam</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $totalBarangDipinjam ?? 0 }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-boxes text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Belum Dikembalikan</p>
                        <h3 class="fw-bold text-danger mb-0">{{ $totalBarangBelumKembali ?? 0 }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fas fa-clock text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Sudah Dikembalikan</p>
                        <h3 class="fw-bold text-success mb-0">{{ $totalBarangSudahKembali ?? 0 }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-check-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-9">
                    <label class="form-label small fw-semibold">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" value="{{ request('search') }}" placeholder="Cari kode transaksi, nama peminjam, atau email...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill w-100">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Daftar Peminjaman Aktif</h5>
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
                            <th>Jatuh Tempo</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th class="pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman as $p)
                        <tr>
                            <td class="ps-2">{{ $p->kode_transaksi }}</td>
                            <td>{{ $p->nama_peminjam }} @if($p->nim) <br><small class="text-muted">{{ $p->nim }}</small>@endif</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_penggunaan)->translatedFormat('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</td>
                            <td>
                                {{ $p->total_belum_kembali }} / {{ $p->total_dipinjam }} unit
                                @if($p->total_sudah_kembali > 0)
                                <br><small class="text-muted">({{ $p->total_sudah_kembali }} sudah kembali)</small>
                                @endif
                            </td>
                            <td>
                                @if($p->total_belum_kembali == 0)
                                <span class="badge bg-success">Selesai</span>
                                @elseif($p->total_sudah_kembali > 0)
                                <span class="badge bg-info">Sebagian Kembali</span>
                                @else
                                <span class="badge bg-warning">Sedang Digunakan</span>
                                @endif
                            </td>
                            <td class="pe-2">
                                <a href="{{ route('peminjaman.show', $p->id_peminjaman) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('peminjaman.form-pengembalian', $p->id_peminjaman) }}" class="btn btn-sm btn-outline-success rounded-pill me-1" title="Kembalikan">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                                <form action="{{ route('peminjaman.destroy', $p->id_peminjaman) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus peminjaman ini? Stok akan dikembalikan.')" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada peminjaman aktif</td>
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