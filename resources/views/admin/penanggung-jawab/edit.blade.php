@extends('layouts.admin')

@section('title', 'Edit Penanggung Jawab')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Penanggung Jawab</h2>
    <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-secondary rounded-pill">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.penanggung-jawab.update', $pj->id_pj) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_pj" class="form-control @error('nama_pj') is-invalid @enderror" value="{{ old('nama_pj', $pj->nama_pj) }}" required>
                @error('nama_pj') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">No Kontak</label>
                <input type="text" name="no_kontak" class="form-control @error('no_kontak') is-invalid @enderror" value="{{ old('no_kontak', $pj->no_kontak) }}">
                @error('no_kontak') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pj->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Laboratorium</label>
                <select name="id_lab" class="form-select @error('id_lab') is-invalid @enderror">
                    <option value="">-- Semua Lab --</option>
                    @foreach($laboratorium as $lab)
                        <option value="{{ $lab->id_lab }}" {{ old('id_lab', $pj->id_lab) == $lab->id_lab ? 'selected' : '' }}>
                            {{ $lab->nama_lab }}
                        </option>
                    @endforeach
                </select>
                @error('id_lab') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection