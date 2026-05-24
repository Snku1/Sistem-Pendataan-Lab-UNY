@extends('layouts.admin')

@section('title', 'Manajemen Penanggung Jawab')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manajemen Penanggung Jawab</h2>
    <a href="{{ route('admin.penanggung-jawab.create') }}" class="btn btn-primary rounded-pill">
        <i class="fas fa-plus me-1"></i> Tambah Penanggung Jawab
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No Kontak</th>
                        <th>Email</th>
                        <th>Laboratorium</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $pj)
                    <tr>
                        <td>{{ $pj->id_pj }}</td>
                        <td>{{ $pj->nama_pj }}</td>
                        <td>{{ $pj->no_kontak ?? '-' }}</td>
                        <td>{{ $pj->email ?? '-' }}</td>
                        <td>{{ $pj->laboratorium->nama_lab ?? 'Global' }}</td>
                        <td>
                            <a href="{{ route('admin.penanggung-jawab.edit', $pj->id_pj) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.penanggung-jawab.destroy', $pj->id_pj) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($list->hasPages())
        <div class="card-footer bg-transparent">
            {{ $list->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection