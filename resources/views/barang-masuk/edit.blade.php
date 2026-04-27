@extends('layouts.app')

@section('title', 'Edit Penerimaan Barang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Edit Penerimaan Barang</h2>
            <p class="text-muted mb-0">Perbarui data penerimaan barang</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('barang-masuk.update', $barangMasuk->id_masuk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <select name="id_barang" class="form-select form-select-sm rounded-pill @error('id_barang') is-invalid @enderror" required>
                            <option value="">Pilih Barang</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->id_barang }}" {{ old('id_barang', $barangMasuk->id_barang) == $b->id_barang ? 'selected' : '' }}>
                                    {{ $b->nama_barang }} ({{ $b->merk ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_masuk" class="form-control form-control-sm rounded-pill @error('jumlah_masuk') is-invalid @enderror" 
                               value="{{ old('jumlah_masuk', $barangMasuk->jumlah_masuk) }}" min="1" required>
                        @error('jumlah_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Tanggal Datang <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_masuk" class="form-control form-control-sm rounded-pill @error('tanggal_masuk') is-invalid @enderror" 
                               value="{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}" required>
                        @error('tanggal_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Supplier / Sumber</label>
                        <input type="text" name="sumber" class="form-control form-control-sm rounded-pill @error('sumber') is-invalid @enderror" 
                               value="{{ old('sumber', $barangMasuk->sumber) }}" placeholder="Contoh: PT. Science Medika">
                        @error('sumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Semester</label>
                        <select name="semester" class="form-select form-select-sm rounded-pill @error('semester') is-invalid @enderror">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester', $barangMasuk->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester', $barangMasuk->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Pemeriksa Barang (Teknisi)</label>
                        <select name="id_penanggung_jawab" class="form-select form-select-sm rounded-pill @error('id_penanggung_jawab') is-invalid @enderror">
                            <option value="">Pilih Pemeriksa</option>
                            @foreach($penanggungJawabList as $pj)
                                <option value="{{ $pj->id_pj }}" {{ old('id_penanggung_jawab', $barangMasuk->id_penanggung_jawab) == $pj->id_pj ? 'selected' : '' }}>
                                    {{ $pj->nama_pj }} ({{ $pj->email ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Biarkan kosong jika tidak ada.</small>
                        @error('id_penanggung_jawab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status Penerimaan <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm rounded-pill @error('status') is-invalid @enderror" required>
                            <option value="menunggu" {{ old('status', $barangMasuk->status) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="diterima" {{ old('status', $barangMasuk->status) == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        </select>
                        <small class="text-muted">Perubahan status akan mempengaruhi stok barang (jika diubah ke diterima, stok akan ditambah; jika ke menunggu, stok akan dikurangi).</small>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Bukti Foto Saat Ini</label>
                        @if($barangMasuk->bukti_foto)
                            <div class="mb-2">
                                <a href="{{ Storage::url($barangMasuk->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                    <i class="fas fa-image"></i> Lihat Foto Saat Ini
                                </a>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada foto</p>
                        @endif
                        <label class="form-label small fw-semibold mt-2">Ganti Bukti Foto (opsional)</label>
                        <input type="file" name="bukti_foto" class="form-control form-control-sm @error('bukti_foto') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">JPG, PNG, WebP maks. 2MB. Upload ulang jika ingin mengganti.</small>
                        @error('bukti_foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-2"></i>Update Penerimaan
                        </button>
                        <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection