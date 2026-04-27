@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Edit Barang</h2>
            <p class="text-muted mb-0">Perbarui data inventaris barang</p>
        </div>
        <div>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Menampilkan error jika ada -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Kode Barang -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control form-control-sm rounded-pill @error('kode_barang') is-invalid @enderror" 
                               value="{{ old('kode_barang', $barang->kode_barang) }}" required>
                        @error('kode_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Barang -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control form-control-sm rounded-pill @error('nama_barang') is-invalid @enderror" 
                               value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Merk</label>
                        <input type="text" name="merk" class="form-control form-control-sm rounded-pill @error('merk') is-invalid @enderror" 
                               value="{{ old('merk', $barang->merk) }}">
                        @error('merk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Lokasi <span class="text-danger">*</span></label>
                        <select name="id_lokasi" class="form-select form-select-sm rounded-pill @error('id_lokasi') is-invalid @enderror" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($lokasi as $l)
                                <option value="{{ $l->id_lokasi }}" {{ old('id_lokasi', $barang->id_lokasi) == $l->id_lokasi ? 'selected' : '' }}>
                                    {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Jumlah Baik, Rusak, Hilang -->
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jumlah Baik <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_baik" id="jumlah_baik" class="form-control form-control-sm rounded-pill @error('jumlah_baik') is-invalid @enderror" 
                               value="{{ old('jumlah_baik', $barang->jumlah_baik) }}" min="0" required>
                        @error('jumlah_baik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jumlah Rusak</label>
                        <input type="number" name="jumlah_rusak" id="jumlah_rusak" class="form-control form-control-sm rounded-pill @error('jumlah_rusak') is-invalid @enderror" 
                               value="{{ old('jumlah_rusak', $barang->jumlah_rusak ?? 0) }}" min="0">
                        @error('jumlah_rusak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jumlah Hilang</label>
                        <input type="number" name="jumlah_hilang" id="jumlah_hilang" class="form-control form-control-sm rounded-pill @error('jumlah_hilang') is-invalid @enderror" 
                               value="{{ old('jumlah_hilang', $barang->jumlah_hilang ?? 0) }}" min="0">
                        @error('jumlah_hilang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Total Stok (readonly, otomatis) -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Total Stok (Otomatis)</label>
                        <input type="number" id="total_stok" class="form-control form-control-sm rounded-pill" readonly>
                        <small class="text-muted">Akan dihitung otomatis</small>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Semester</label>
                        <select name="semester" class="form-select form-select-sm rounded-pill @error('semester') is-invalid @enderror">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester', $barang->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester', $barang->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" class="form-control form-control-sm rounded-pill @error('tahun_ajaran') is-invalid @enderror" 
                               value="{{ old('tahun_ajaran', $barang->tahun_ajaran) }}" placeholder="Contoh: 2024/2025">
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kapasitas -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kapasitas Alat</label>
                        <input type="text" name="kapasitas" class="form-control form-control-sm rounded-pill @error('kapasitas') is-invalid @enderror" 
                               value="{{ old('kapasitas', $barang->kapasitas) }}" placeholder="Contoh: 2 pengguna, 100 sampel">
                        <small class="text-muted">Isi dengan kapasitas pemeriksaan atau penggunaan alat.</small>
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control form-control-sm rounded-pill @error('keterangan') is-invalid @enderror" 
                               value="{{ old('keterangan', $barang->keterangan) }}">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control form-control-sm @error('deskripsi') is-invalid @enderror" rows="3" 
                                  style="border-radius: 20px;">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Penanggung Jawab -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Penanggung Jawab</label>
                        <select name="penanggung_jawab[]" class="form-select form-select-sm @error('penanggung_jawab') is-invalid @enderror" multiple size="3" style="border-radius: 20px;">
                            @foreach($penanggungJawab as $pj)
                                <option value="{{ $pj->id_pj }}" 
                                    {{ in_array($pj->id_pj, old('penanggung_jawab', $barang->penanggungJawab->pluck('id_pj')->toArray())) ? 'selected' : '' }}>
                                    {{ $pj->nama_pj }} ({{ $pj->email ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tekan Ctrl untuk memilih lebih dari satu</small>
                        @error('penanggung_jawab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-info bg-opacity-10">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-info fs-3 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Informasi</h6>
                    <p class="mb-0 small text-muted">Total stok akan dihitung otomatis dari jumlah baik + rusak + hilang. Pastikan data sesuai.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center py-3 border-top">
        <small class="text-muted">© 2024 Sistem Informasi Laboratorium Terintegrasi (SILABT). Hak Cipta Dilindungi.</small>
    </div>
</div>

<script>
    // Fungsi untuk menghitung total stok
    function hitungTotal() {
        const baik = parseInt(document.getElementById('jumlah_baik').value) || 0;
        const rusak = parseInt(document.getElementById('jumlah_rusak').value) || 0;
        const hilang = parseInt(document.getElementById('jumlah_hilang').value) || 0;
        const total = baik + rusak + hilang;
        document.getElementById('total_stok').value = total;
    }

    // Pastikan elemen ada sebelum menambahkan event listener
    const jumlahBaik = document.getElementById('jumlah_baik');
    const jumlahRusak = document.getElementById('jumlah_rusak');
    const jumlahHilang = document.getElementById('jumlah_hilang');

    if (jumlahBaik) jumlahBaik.addEventListener('input', hitungTotal);
    if (jumlahRusak) jumlahRusak.addEventListener('input', hitungTotal);
    if (jumlahHilang) jumlahHilang.addEventListener('input', hitungTotal);

    // Hitung awal
    hitungTotal();
</script>
@endsection