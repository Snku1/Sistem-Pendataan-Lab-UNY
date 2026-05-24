@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manajemen Lokasi</h2>
    <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary rounded-pill">
        <i class="fas fa-plus me-1"></i> Tambah Lokasi
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Lokasi</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lokasiList as $lokasi)
                    <tr>
                        <td>{{ $lokasi->id_lokasi }}</td>
                        <td>{{ $lokasi->nama_lokasi }}</td>
                        <td>{{ $lokasi->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.lokasi.edit', $lokasi->id_lokasi) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.lokasi.destroy', $lokasi->id_lokasi) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus lokasi ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada lokasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($lokasiList->hasPages())
        <div class="card-footer bg-transparent">
            {{ $lokasiList->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection