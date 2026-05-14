@extends('layouts.app')

@section('title', 'Tambah Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tambah Peminjaman</h2>
            <p class="text-muted">Input data peminjaman barang (bisa lebih dari satu barang)</p>
        </div>
        <div>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" name="nama_peminjam" class="form-control form-control-sm rounded-pill @error('nama_peminjam') is-invalid @enderror" value="{{ old('nama_peminjam') }}" required>
                        @error('nama_peminjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">NIM</label>
                        <input type="text" name="nim" class="form-control form-control-sm rounded-pill @error('nim') is-invalid @enderror" value="{{ old('nim') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tanggal Penggunaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_penggunaan" class="form-control form-control-sm rounded-pill @error('tanggal_penggunaan') is-invalid @enderror" value="{{ old('tanggal_penggunaan', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control form-control-sm rounded-pill @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Upload Surat Peminjaman</label>
                        <input type="file" name="surat_peminjaman" class="form-control form-control-sm @error('surat_peminjaman') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Maks. 5MB)</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Awal</label>
                        <textarea name="catatan_awal" class="form-control" rows="2" placeholder="Catatan awal peminjaman jika ada...">{{ old('catatan_awal') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Daftar Barang yang Dipinjam</h5>
                <div id="items-container">
                    <div class="item-row row g-2 mb-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Barang <span class="text-danger">*</span></label>
                            <select name="items[0][id_barang]" class="form-select form-select-sm" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barang as $b)
                                    @php $stokBaik = $b->jumlah_baik; @endphp
                                    <option value="{{ $b->id_barang }}" data-stok="{{ $stokBaik }}" {{ $stokBaik == 0 ? 'disabled' : '' }} style="{{ $stokBaik == 0 ? 'color: #999; background-color: #f5f5f5;' : '' }}">
                                        {{ $b->nama_barang }} (Stok Baik: {{ $stokBaik }})
                                        @if($stokBaik == 0) - HABIS @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <!-- kosong -->
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display:none;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary mt-2 rounded-pill"><i class="fas fa-plus"></i> Tambah Barang</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSubmit">Simpan Peminjaman</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk memvalidasi stok pada setiap baris
    function validateStok(row) {
        const select = row.querySelector('select');
        const jumlahInput = row.querySelector('.jumlah-input');
        const warningSpan = row.querySelector('.stok-warning');
        if (!select || !jumlahInput) return true;
        
        const selectedOption = select.options[select.selectedIndex];
        const stokTersedia = parseInt(selectedOption.getAttribute('data-stok') || 0);
        const jumlah = parseInt(jumlahInput.value) || 0;
        
        if (stokTersedia < jumlah) {
            warningSpan.style.display = 'block';
            return false;
        } else {
            warningSpan.style.display = 'none';
            return true;
        }
    }

    // Validasi semua baris sebelum submit
    function validateAllItems() {
        const rows = document.querySelectorAll('.item-row');
        let allValid = true;
        rows.forEach(row => {
            if (!validateStok(row)) allValid = false;
        });
        return allValid;
    }

    // Event listener untuk setiap perubahan di select atau jumlah
    function attachValidationEvents(row) {
        const select = row.querySelector('select');
        const jumlahInput = row.querySelector('.jumlah-input');
        if (select) {
            select.addEventListener('change', () => validateStok(row));
        }
        if (jumlahInput) {
            jumlahInput.addEventListener('input', () => validateStok(row));
        }
    }

    // Inisialisasi semua baris yang ada
    document.querySelectorAll('.item-row').forEach(row => {
        attachValidationEvents(row);
        validateStok(row);
    });

    // Tombol submit: lakukan validasi sebelum submit
    const form = document.querySelector('form');
    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', (e) => {
            if (!validateAllItems()) {
                e.preventDefault();
                alert('Periksa kembali stok barang yang dipilih. Ada barang dengan stok tidak mencukupi.');
            }
        });
    }

    // Tambah item baru
    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row g-2 mb-2 align-items-end';
        newRow.innerHTML = `
            <div class="col-md-6">
                <select name="items[${itemIndex}][id_barang]" class="form-select form-select-sm" required>
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $b)
                        @php $stokBaik = $b->jumlah_baik; @endphp
                        <option value="{{ $b->id_barang }}" data-stok="{{ $stokBaik }}" {{ $stokBaik == 0 ? 'disabled' : '' }} style="{{ $stokBaik == 0 ? 'color: #999; background-color: #f5f5f5;' : '' }}">
                            {{ $b->nama_barang }} (Stok Baik: {{ $stokBaik }})
                            @if($stokBaik == 0) - HABIS @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${itemIndex}][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1" required>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button>
            </div>
        `;
        container.appendChild(newRow);
        attachValidationEvents(newRow);
        validateStok(newRow);
        newRow.querySelector('.remove-item').addEventListener('click', function() {
            newRow.remove();
        });
        itemIndex++;
    });

    // Sembunyikan tombol remove pada baris pertama
    document.querySelectorAll('.remove-item').forEach(btn => btn.style.display = 'none');
</script>
@endsection