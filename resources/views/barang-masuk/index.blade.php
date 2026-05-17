@extends('layouts.app')

@section('title', 'Barang Datang')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen Penerimaan Barang</h2>
            <p class="text-muted mb-0">Kelola dan verifikasi setiap barang yang masuk ke laboratorium</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Penerimaan Barang
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Barang Hari Ini</p>
                        <h3 class="fw-bold text-primary mb-0">{{ number_format($todayTotal) }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-boxes text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Menunggu Konfirmasi</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $menungguCount }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-clock text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Sudah Masuk Stok</p>
                        <h3 class="fw-bold text-success mb-0">{{ $diterimaCount }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-check-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter (semua dalam satu baris) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <!-- Cari Barang -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Cari Barang</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" 
                           value="{{ request('search') }}" placeholder="Nama barang...">
                </div>
                <!-- Tanggal Awal -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control form-control-sm rounded-pill" 
                           value="{{ request('tanggal_awal') }}">
                </div>
                <!-- Tanggal Akhir -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm rounded-pill" 
                           value="{{ request('tanggal_akhir') }}">
                </div>
                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    </select>
                </div>
                <!-- Tombol Filter & Reset (dalam satu grup) -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-truck-loading me-2 text-primary"></i>Daftar Penerimaan Barang</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" style="min-width: 1000px; font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2">Tanggal Datang</th>
                            <th>Nama Barang</th>
                            <th>Supplier / Sumber</th>
                            <th>Jumlah</th>
                            <th>Pemeriksa</th>
                            <th>Bukti Foto</th>
                            <th>Status</th>
                            <th class="pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuk as $bm)
                        <tr>
                            <!-- TANGGAL dalam format Indonesia: 17 Mei 2026 -->
                            <td class="ps-2">{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->translatedFormat('d F Y') }}</td>
                            <td class="fw-semibold">{{ $bm->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $bm->sumber ?? '-' }}</td>
                            <td>{{ $bm->jumlah_masuk }} unit</td>
                            <td>{{ $bm->penanggungJawab->nama_pj ?? '-' }}</td>
                            <td>
                                @if($bm->bukti_foto)
                                <a href="{{ Storage::url($bm->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                    <i class="fas fa-image"></i> Lihat
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($bm->status == 'menunggu')
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">Menunggu</span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Diterima</span>
                                @endif
                            </td>
                            <td class="pe-2">
                                <!-- Tombol Detail Pemeriksaan -->
                                <a href="{{ route('barang-masuk.detail-pemeriksaan', $bm->id_masuk) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Detail Pemeriksaan">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Tombol Kondisi Awal (centang hijau) hanya jika status menunggu -->
                                @if($bm->status == 'menunggu')
                                <a href="{{ route('barang-masuk.kondisi-awal', $bm->id_masuk) }}" class="btn btn-sm btn-outline-success rounded-pill me-1" title="Isi Kondisi Awal & Konfirmasi">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @endif

                                <!-- Tombol Edit & Hapus hanya jika status menunggu -->
                                @if($bm->status == 'menunggu')
                                <a href="{{ route('barang-masuk.edit', $bm->id_masuk) }}" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('barang-masuk.destroy', $bm->id_masuk) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data penerimaan barang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $barangMasuk->firstItem() ?? 0 }} - {{ $barangMasuk->lastItem() ?? 0 }} dari {{ $barangMasuk->total() }} data
                </div>
                <div>{{ $barangMasuk->withQueryString()->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection