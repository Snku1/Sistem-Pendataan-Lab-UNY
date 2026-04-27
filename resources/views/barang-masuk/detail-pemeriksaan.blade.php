@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Penerimaan')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Pemeriksaan Penerimaan</h2>
            <p class="text-muted">ID Transaksi: TR-IN-{{ str_pad($barangMasuk->id_masuk, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Barang</label>
                            <p class="fw-semibold">{{ $barangMasuk->barang->nama_barang ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Datang</label>
                            <p class="fw-semibold">{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Jumlah Datang</label>
                            <p class="fw-semibold">{{ $barangMasuk->jumlah_masuk }} unit</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Supplier / Sumber Barang</label>
                            <p class="fw-semibold">{{ $barangMasuk->sumber ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Pemeriksa Barang</label>
                            <p class="fw-semibold">{{ $barangMasuk->penanggungJawab->nama_pj ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status Penerimaan</label>
                            <p>
                                @if($barangMasuk->status == 'menunggu')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">Menunggu Verifikasi</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Diterima</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Bukti Foto -->
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Bukti Foto</label>
                        <div class="mt-2">
                            @if($barangMasuk->bukti_foto)
                                <a href="{{ Storage::url($barangMasuk->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                    <i class="fas fa-image"></i> Lihat Foto
                                </a>
                            @else
                                <p class="text-muted">-</p>
                            @endif
                        </div>
                    </div>

                    <!-- Catatan Pemeriksaan -->
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Catatan Pemeriksaan</label>
                        <p class="mt-2">{{ $barangMasuk->catatan_pemeriksaan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Informasi Tambahan</h5>
                    <div class="mb-2">
                        <label class="text-muted small">Pencatat</label>
                        <p class="mb-0">{{ $barangMasuk->user->nama ?? '-' }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Tanggal Dicatat</label>
                        <p class="mb-0">{{ $barangMasuk->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Terakhir Diupdate</label>
                        <p class="mb-0">{{ $barangMasuk->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection