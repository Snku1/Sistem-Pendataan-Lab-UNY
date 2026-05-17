@extends('layouts.app')
@section('title', 'Edit Semester')
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 w-50 mx-auto">
        <div class="card-header bg-primary text-white rounded-top-4 py-3">
            <h4 class="mb-0">Edit Semester</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('semester.update', $semester->id_semester) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Semester</label>
                    <select name="nama_semester" class="form-select rounded-pill" required>
                        <option value="Ganjil" {{ $semester->nama_semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester->nama_semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control rounded-pill" value="{{ $semester->tahun_ajaran }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control rounded-pill" value="{{ $semester->tanggal_mulai ? date('Y-m-d', strtotime($semester->tanggal_mulai)) : '' }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control rounded-pill" value="{{ $semester->tanggal_selesai ? date('Y-m-d', strtotime($semester->tanggal_selesai)) : '' }}">
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $semester->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Jadikan semester aktif</label>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('semester.daftar') }}" class="btn btn-secondary rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection