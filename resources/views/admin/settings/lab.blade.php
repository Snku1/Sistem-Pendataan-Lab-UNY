@extends('layouts.admin')

@section('title', 'Pengaturan Nama Laboratorium')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Pengaturan Nama Laboratorium (Default)</h2>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill">Dashboard</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('admin.settings.lab.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Laboratorium Default (central)</label>
                <input type="text" name="lab_name" class="form-control @error('lab_name') is-invalid @enderror" value="{{ old('lab_name', $labName) }}" required>
                @error('lab_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Nama ini digunakan sebagai default jika belum ada pengaturan per lab. Untuk pengaturan per lab, silakan login sebagai koorlap/teknisi masing-masing laboratorium.</small>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection