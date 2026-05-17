@extends('layouts.app')

@section('title', 'Manajemen Semester')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Manajemen Semester</h2>
        <a href="{{ route('semester.tambah') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i>Tambah Semester
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($semesterList as $idx => $sem)
                        <tr>
                            <td>{{ $idx+1 }}</td>
                            <td>{{ $sem->nama_semester }}</td>
                            <td>{{ $sem->tahun_ajaran }}</td>
                            <td>{{ $sem->tanggal_mulai ? \Carbon\Carbon::parse($sem->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $sem->tanggal_selesai ? \Carbon\Carbon::parse($sem->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($sem->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('semester.edit', $sem->id_semester) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('semester.hapus', $sem->id_semester) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus semester ini?')"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data semester</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection