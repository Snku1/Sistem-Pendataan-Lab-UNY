@extends('layouts.app')

@section('title', 'Barang Datang')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Breadcrumb dengan tombol -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Barang Datang</h2>
            <p class="text-muted">Dashboard &gt; Barang Datang</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Penerimaan Barang
            </a>
        </div>
    </div>

    <!-- Card Statistik -->
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

    <!-- Judul Tabel & Deskripsi -->
    <div class="mb-3">
        <h5 class="fw-semibold">Manajemen Penerimaan Barang</h5>
        <p class="text-muted small">Kelola dan verifikasi setiap barang yang masuk ke laboratorium</p>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Cari Barang</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" value="{{ request('search') }}" placeholder="Nama barang...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill w-100">
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
                            <th class="ps-2">Nama Barang</th>
                            <th>Tanggal Datang</th>
                            <th>Supplier / Sumber</th>
                            <th>Jumlah</th>
                            <th>Pemeriksa</th>
                            <th>Bukti Foto</th>
                            <th>Status</th>
                            <th class="pe-2">Aksi</th>
                        </td>
                    </thead>
                    <tbody>
                        @forelse($barangMasuk as $bm)
                        <tr>
                            <td class="ps-2">{{ $bm->barang->nama_barang ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->translatedFormat('d M Y') }}</td>
                            <td>{{ $bm->sumber ?? '-' }}</td>
                            <td>{{ $bm->jumlah_masuk }} {{ $bm->barang->satuan ?? 'unit' }}</td>
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
                                <!-- Detail pemeriksaan (mata) -->
                                <a href="{{ route('barang-masuk.detail-pemeriksaan', $bm->id_masuk) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Detail Pemeriksaan">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- Tombol untuk mengisi kondisi awal sekaligus konfirmasi penerimaan (centang hijau) -->
                                <a href="{{ route('barang-masuk.kondisi-awal', $bm->id_masuk) }}" class="btn btn-sm btn-outline-success rounded-pill me-1" title="Isi Kondisi Awal & Konfirmasi">
                                    <i class="fas fa-check-circle"></i>
                                </a>
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