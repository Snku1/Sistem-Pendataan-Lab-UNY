@extends('layouts.admin')

@section('title', 'Edit Laboratorium')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Laboratorium</h2>
    <a href="{{ route('admin.laboratorium.index') }}" class="btn btn-secondary rounded-pill">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.laboratorium.update', $laboratorium->id_lab) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Laboratorium <span class="text-danger">*</span></label>
                <input type="text" name="nama_lab" class="form-control @error('nama_lab') is-invalid @enderror" value="{{ old('nama_lab', $laboratorium->nama_lab) }}" required>
                @error('nama_lab')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                <a href="{{ route('admin.laboratorium.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection