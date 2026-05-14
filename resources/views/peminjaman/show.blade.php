@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Peminjaman</h2>
            <p class="text-muted">Kode Transaksi: {{ $peminjaman->kode_transaksi }}</p>
        </div>
        <div>
            <a href="{{ $peminjaman->status_transaksi == 'aktif' ? route('peminjaman.index') : route('peminjaman.riwayat') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Card Statistik -->
    @php
        $totalItem = $peminjaman->details->sum('jumlah');
        $totalDikembalikan = $peminjaman->details->where('status_item', 'kembali')->sum('jumlah');
        $totalDipinjam = $peminjaman->details->where('status_item', 'dipinjam')->sum('jumlah');
        $uniqueBarang = $peminjaman->details->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Barang</p>
                        <h3 class="fw-bold text-primary mb-0">{{ $totalItem }} unit</h3>
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
                        <p class="text-muted mb-1 small">Sudah Kembali</p>
                        <h3 class="fw-bold text-success mb-0">{{ $totalDikembalikan }} unit</h3>
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
                        <p class="text-muted mb-1 small">Masih Dipinjam</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $totalDipinjam }} unit</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-clock text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Jenis Barang</p>
                        <h3 class="fw-bold text-info mb-0">{{ $uniqueBarang }}</h3>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="fas fa-tag text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Informasi Peminjaman</h5>
                    <table class="table table-sm">
                        <tr><th width="150">Peminjam</th><td>{{ $peminjaman->nama_peminjam }}</td></tr>
                        <tr><th>NIM</th><td>{{ $peminjaman->nim ?? '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $peminjaman->email }}</td></tr>
                        <tr><th>Tanggal Penggunaan</th><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_penggunaan)->translatedFormat('d F Y') }}</td></tr>
                        <tr><th>Jatuh Tempo</th><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td></tr>
                        <tr><th>Catatan Awal</th><td>{{ $peminjaman->catatan_awal ?? '-' }}</td></tr>
                        <tr><th>Status</th><td><span class="badge {{ $peminjaman->status_transaksi == 'aktif' ? 'bg-warning' : 'bg-success' }}">{{ ucfirst($peminjaman->status_transaksi) }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Dokumen</h5>
                    @if($peminjaman->surat_peminjaman)
                        <a href="{{ Storage::url($peminjaman->surat_peminjaman) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                            <i class="fas fa-file-pdf"></i> Lihat Surat Peminjaman
                        </a>
                    @else
                        <p class="text-muted">Tidak ada surat peminjaman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Daftar Barang</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Kondisi Setelah</th>
                            <th>Tanggal Kembali</th>
                            <th>Catatan Kembali</th>
                            <th>Status Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjaman->details as $detail)
                        <tr>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ ucfirst($detail->kondisi_setelah ?? '-') }}</td>
                            <td>{{ $detail->tanggal_kembali_aktual ? \Carbon\Carbon::parse($detail->tanggal_kembali_aktual)->translatedFormat('d M Y') : '-' }}</td>
                            <td>{{ $detail->catatan_kembali ?? '-' }}</td>
                            <td>
                                @if($detail->status_item == 'dipinjam')
                                    <span class="badge bg-warning">Dipinjam</span>
                                @else
                                    <span class="badge bg-success">Kembali</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection