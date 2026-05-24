@extends('layouts.admin')

@section('title', 'Manajemen Laboratorium')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manajemen Laboratorium</h2>
    <a href="{{ route('admin.laboratorium.create') }}" class="btn btn-primary rounded-pill">
        <i class="fas fa-plus me-1"></i> Tambah Laboratorium
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Lab</th>
                        <th>Jumlah User</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laboratorium as $lab)
                    <tr>
                        <td>{{ $lab->id_lab }}</td>
                        <td class="fw-semibold">{{ $lab->nama_lab }}</td>
                        <td>{{ $lab->users()->count() }}</td>
                        <td>{{ $lab->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.laboratorium.edit', $lab->id_lab) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.laboratorium.destroy', $lab->id_lab) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin ingin menghapus laboratorium ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada laboratorium.</td>
                    </tr>
                    @endforelse
                </tbody>
            <table>
        </div>
    </div>
    @if($laboratorium->hasPages())
        <div class="card-footer bg-transparent">
            {{ $laboratorium->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection