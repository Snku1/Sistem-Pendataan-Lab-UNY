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
                        <label class="form-label small fw-semibold">Alasan Peminjaman</label>
                        <textarea name="catatan_awal" class="form-control" rows="2" placeholder="Catat alasan peminjaman jika ada...">{{ old('catatan_awal') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Daftar Barang yang Dipinjam</h5>
                <div id="items-container">
                    <!-- Baris pertama (dengan label) -->
                    <div class="item-row row g-2 mb-2 align-items-start"> <!-- align-items-start agar peringatan tidak merusak alignment -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Barang <span class="text-danger">*</span></label>
                            <select name="items[0][id_barang]" class="form-select form-select-sm barang-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barang as $b)
                                <option value="{{ $b->id_barang }}" data-stok="{{ $b->jumlah_baik }}" {{ $b->jumlah_baik == 0 ? 'disabled' : '' }} style="{{ $b->jumlah_baik == 0 ? 'color: #999; background-color: #f5f5f5;' : '' }}">
                                    {{ $b->nama_barang }} (Stok Baik: {{ $b->jumlah_baik }})@if($b->jumlah_baik == 0) - HABIS @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" name="items[0][jumlah]" class="form-control form-control-sm jumlah-input" min="0" value="0" required>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display: none;"><i class="fas fa-trash"></i></button>
                            </div>
                            <!-- Peringatan stok akan muncul di bawah input, membuat tinggi baris bertambah dan baris berikutnya ikut ke bawah -->
                            <span class="stok-warning text-danger small" style="display: none;">Stok tidak mencukupi!</span>
                        </div>
                        <div class="col-md-2"></div>
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
    function validateStok(row) {
        const select = row.querySelector('.barang-select');
        const jumlahInput = row.querySelector('.jumlah-input');
        const warningSpan = row.querySelector('.stok-warning');
        if (!select || !jumlahInput || !warningSpan) return true;

        const selectedOption = select.options[select.selectedIndex];
        const stokTersedia = selectedOption ? parseInt(selectedOption.getAttribute('data-stok') || 0) : 0;
        let jumlah = parseInt(jumlahInput.value) || 0;
        if (jumlah < 0) jumlah = 0;

        if (jumlah > 0 && stokTersedia < jumlah) {
            warningSpan.style.display = 'block';  // peringatan muncul, baris akan bertambah tinggi
            return false;
        } else {
            warningSpan.style.display = 'none';
            return true;
        }
    }

    function attachValidationEvents(row) {
        const select = row.querySelector('.barang-select');
        const jumlahInput = row.querySelector('.jumlah-input');
        if (select) select.addEventListener('change', () => validateStok(row));
        if (jumlahInput) jumlahInput.addEventListener('input', () => validateStok(row));
    }

    function validateAllItems() {
        let allValid = true;
        const rows = document.querySelectorAll('.item-row');
        for (let row of rows) {
            const select = row.querySelector('.barang-select');
            const jumlahInput = row.querySelector('.jumlah-input');
            const barangDipilih = select && select.value !== '';
            const jumlah = parseInt(jumlahInput?.value) || 0;

            if (barangDipilih && jumlah === 0) {
                alert('Jumlah barang harus minimal 1 untuk barang yang dipilih.');
                allValid = false;
                return false;
            }
            if (barangDipilih && jumlah > 0 && !validateStok(row)) {
                allValid = false;
                return false;
            }
        }
        return allValid;
    }

    document.querySelectorAll('.item-row').forEach(row => {
        attachValidationEvents(row);
        validateStok(row);
    });

    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', (e) => {
            if (!validateAllItems()) {
                e.preventDefault();
                alert('Periksa kembali stok atau jumlah barang yang dipilih.');
            }
        });
    }

    let itemIndex = 0;
    function cloneNewRow() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        if (!firstRow) return null;

        const newRow = firstRow.cloneNode(true);
        itemIndex++;

        const select = newRow.querySelector('.barang-select');
        const jumlahInput = newRow.querySelector('.jumlah-input');
        if (select) {
            select.name = `items[${itemIndex}][id_barang]`;
            select.selectedIndex = 0;
        }
        if (jumlahInput) {
            jumlahInput.name = `items[${itemIndex}][jumlah]`;
            jumlahInput.value = 0;
        }

        // Hapus label pada baris baru (karena hanya baris pertama yang perlu label)
        const labelBarang = newRow.querySelector('.col-md-6 > label');
        if (labelBarang) labelBarang.remove();
        const labelJumlah = newRow.querySelector('.col-md-4 > label');
        if (labelJumlah) labelJumlah.remove();

        const warningSpan = newRow.querySelector('.stok-warning');
        if (warningSpan) warningSpan.style.display = 'none';

        const removeBtn = newRow.querySelector('.remove-item');
        if (removeBtn) removeBtn.style.display = 'inline-block';

        attachValidationEvents(newRow);
        validateStok(newRow);

        return newRow;
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const newRow = cloneNewRow();
        if (newRow) {
            document.getElementById('items-container').appendChild(newRow);
            const removeBtn = newRow.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });
            }
        }
    });

    const firstRowRemoveBtn = document.querySelector('.item-row .remove-item');
    if (firstRowRemoveBtn) firstRowRemoveBtn.style.display = 'none';
</script>
@endsection