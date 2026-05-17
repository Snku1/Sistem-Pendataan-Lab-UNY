@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen User</h2>
            <p class="text-muted mb-0">Kelola akun administrator sistem.</p>
        </div>
        <div>
            <a href="{{ route('user.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Admin
            </a>
        </div>
    </div>

    <!-- Statistik (hanya admin) -->
    @php
        $totalUser = App\Models\User::count();
        $totalAdmin = App\Models\User::where('role', 'admin')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Administrator</p><h3 class="fw-bold text-primary mb-0">{{ $totalUser }}</h3></div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3"><i class="fas fa-users text-primary fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Admin Aktif</p><h3 class="fw-bold text-success mb-0">{{ $totalAdmin }}</h3></div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3"><i class="fas fa-user-shield text-success fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter (tanpa role petugas) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Cari Admin</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" 
                           value="{{ request('search') }}" placeholder="Nama atau email...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3"><i class="fas fa-undo me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel User -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-0"><i class="fas fa-table-list me-2 text-primary"></i>Daftar Administrator</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" style="font-size:0.85rem">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id_user }}</td>
                            <td>{{ $user->nama }}@if($user->id_user == auth()->id()) <span class="badge bg-primary bg-opacity-10 text-primary ms-1">Anda</span>@endif</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">Admin</span></td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</td>
                            <td>
                                <a href="{{ route('user.edit', $user->id_user) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id_user != auth()->id())
                                <form action="{{ route('user.destroy', $user->id_user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin hapus admin {{ $user->nama }}?')" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada administrator. <a href="{{ route('user.create') }}">Tambah admin</a> sekarang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection