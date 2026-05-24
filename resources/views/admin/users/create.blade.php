@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Tambah User</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary rounded-pill">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="koorlap" {{ old('role') == 'koorlap' ? 'selected' : '' }}>Koordinator Lab</option>
                        <option value="teknisi" {{ old('role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3" id="lab-field">
                    <label class="form-label">Laboratorium <span class="text-danger">*</span></label>
                    <select name="id_lab" class="form-select @error('id_lab') is-invalid @enderror">
                        <option value="">-- Pilih Laboratorium --</option>
                        @foreach($laboratorium as $lab)
                            <option value="{{ $lab->id_lab }}" {{ old('id_lab') == $lab->id_lab ? 'selected' : '' }}>{{ $lab->nama_lab }}</option>
                        @endforeach
                    </select>
                    @error('id_lab') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const labField = document.getElementById('lab-field');
    function toggleLabField() {
        if (roleSelect.value === 'admin') {
            labField.style.display = 'none';
            // hilangkan required
            labField.querySelector('select').removeAttribute('required');
        } else {
            labField.style.display = 'block';
            labField.querySelector('select').setAttribute('required', 'required');
        }
    }
    roleSelect.addEventListener('change', toggleLabField);
    toggleLabField();
</script>
@endsection