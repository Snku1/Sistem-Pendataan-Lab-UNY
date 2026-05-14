@extends('layouts.app')

@section('title', 'Detail Stok Opname')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Stok Opname</h2>
            <p class="text-muted">Kode: {{ $opname->kode_opname }}</p>
        </div>
        <div>
            <a href="{{ route('stok-opname.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5>Informasi Opname</h5>
                    <table class="table table-sm">
                        <tr><th>Kode</th><td>{{ $opname->kode_opname }}</tr>
                        <tr><th>Tanggal</th><td>{{ \Carbon\Carbon::parse($opname->tanggal_opname)->translatedFormat('d F Y') }}</tr>
                        <tr><th>Keterangan</th><td>{{ $opname->keterangan ?? '-' }}</tr>
                        <tr><th>Dibuat Oleh</th><td>{{ $opname->user->nama ?? '-' }}</tr>
                        <tr><th>Tanggal Input</th><td>{{ $opname->created_at->format('d M Y H:i') }}</tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-list me-2 text-primary"></i>Detail Barang</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opname->details as $detail)
                        <tr>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $detail->stok_sistem }}</td>
                            <td>{{ $detail->stok_fisik }}</td>
                            <td class="{{ $detail->selisih < 0 ? 'text-danger' : ($detail->selisih > 0 ? 'text-success' : '') }}">
                                {{ $detail->selisih }}
                            </td>
                            <td>{{ $detail->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection