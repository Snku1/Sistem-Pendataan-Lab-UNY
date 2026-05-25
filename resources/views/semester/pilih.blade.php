@extends('layouts.app')

@section('title', 'Pilih Semester Aktif')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white border-0 rounded-top-4 py-3">
                    <h4 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2"></i>Pilih Semester Aktif</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Silakan pilih semester dan tahun ajaran yang sedang berjalan.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    @if ($semesterList->isEmpty())
                        <div class="alert alert-warning">Belum ada data semester. Silahkan tambahkan data semester terlebih dahulu di Manajemen Semester.</div>
                    @else
                        <form method="POST" action="{{ route('pilih-semester.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pilih Semester</label>
                                <select name="id_semester" class="form-select form-select-lg rounded-pill" required>
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach ($semesterList as $semester)
                                        <option value="{{ $semester->id_semester }}" {{ $activeSemester && $activeSemester->id_semester == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }} - {{ $semester->tahun_ajaran }}
                                            @if ($semester->is_active) (Aktif) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2">Pilih Semester</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection