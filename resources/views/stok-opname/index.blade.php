@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Stok Opname</h2>
            <p class="text-muted">Kelola dan pantau kegiatan stock opname</p>
        </div>
        <div>
            <a href="{{ route('stok-opname.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Opname
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Daftar Stok Opname</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Opname</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Dibuat Oleh</th>
                            <th>Jumlah Item</th>
                            <th class="pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($opnames as $op)
                        <tr>
                            <td>{{ $op->kode_opname }}</td>
                            <td>{{ \Carbon\Carbon::parse($op->tanggal_opname)->translatedFormat('d M Y') }}</td>
                            <td>{{ $op->keterangan ?? '-' }}</td>
                            <td>{{ $op->user->nama ?? '-' }}</td>
                            <td>{{ $op->details->count() }} item</td>
                            <td class="pe-2">
                                <a href="{{ route('stok-opname.show', $op->id_opname) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('stok-opname.destroy', $op->id_opname) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus data opname?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data stok opname</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $opnames->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection