@extends('layouts.app')

@section('title', 'Tambah Peminjaman Barang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tambah Peminjaman Barang</h2>
            <p class="text-muted mb-0">Catat peminjaman barang oleh pengguna</p>
        </div>
        <div>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data" id="formPeminjaman">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" name="nama_peminjam" class="form-control form-control-sm rounded-pill @error('nama_peminjam') is-invalid @enderror" value="{{ old('nama_peminjam') }}" required>
                        @error('nama_peminjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">NIM <span class="text-danger">*</span></label>
                        <input type="text" name="nim" class="form-control form-control-sm rounded-pill @error('nim') is-invalid @enderror" value="{{ old('nim') }}" required>
                        @error('nim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Tanggal Penggunaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_penggunaan" class="form-control form-control-sm rounded-pill @error('tanggal_penggunaan') is-invalid @enderror" value="{{ old('tanggal_penggunaan', date('Y-m-d')) }}" required>
                        @error('tanggal_penggunaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control form-control-sm rounded-pill @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        @error('tanggal_jatuh_tempo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Surat Peminjaman (PDF/Gambar, opsional)</label>
                        <input type="file" name="surat_peminjaman" class="form-control form-control-sm @error('surat_peminjaman') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Maks. 5MB, format PDF, JPG, PNG</small>
                        @error('surat_peminjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alasan Peminjaman (opsional)</label>
                        <textarea name="catatan_awal" class="form-control form-control-sm rounded-3" rows="2">{{ old('catatan_awal') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Daftar Barang yang Dipinjam</h5>
                <div id="items-container">
                    <div class="item-row row g-2 mb-3 align-items-start">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <select name="items[0][id_barang]" class="form-select form-select-sm barang-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barang as $b)
                                    @php $stok = $b->jumlah_baik; @endphp
                                    <option value="{{ $b->id_barang }}" data-stok="{{ $stok }}" {{ $stok == 0 ? 'disabled' : '' }}>
                                        {{ $b->nama_barang }} ({{ $b->merk ?? '-' }}) - Stok: {{ $stok }}
                                        @if($stok == 0) (Stok habis) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Stok Tersedia</label>
                            <input type="text" class="form-control form-control-sm stok-info" readonly disabled>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display: none;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary mt-2 rounded-pill"><i class="fas fa-plus"></i> Tambah Barang</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Peminjaman
                    </button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIndex = 0;
    let isSubmitting = false; // Flag untuk mencegah double submit

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
            select.addEventListener('change', updateStokInfo);
            select.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.disabled) {
                    this.value = "";
                    updateStokInfo({target: this});
                    alert("Barang dengan stok habis tidak dapat dipilih.");
                }
            });
        }
        if (jumlahInput) {
            jumlahInput.name = `items[${itemIndex}][jumlah]`;
            jumlahInput.value = 1;
        }
        const stokInfo = newRow.querySelector('.stok-info');
        if (stokInfo) stokInfo.value = '';
        const labels = newRow.querySelectorAll('.col-md-6 > label, .col-md-3 > label, .col-md-2 > label');
        labels.forEach(label => label.remove());
        const removeBtn = newRow.querySelector('.remove-item');
        if (removeBtn) removeBtn.style.display = 'inline-block';
        return newRow;
    }

    function updateStokInfo(event) {
        const row = event.target.closest('.item-row');
        const select = row.querySelector('.barang-select');
        const stokInput = row.querySelector('.stok-info');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && !selectedOption.disabled && selectedOption.value !== "") {
            const stok = selectedOption.getAttribute('data-stok') || 0;
            stokInput.value = stok;
        } else {
            stokInput.value = '';
        }
    }

    // Validasi sebelum submit: jumlah tidak boleh melebihi stok
    document.getElementById('formPeminjaman').addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        let isValid = true;
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const select = row.querySelector('.barang-select');
            const jumlahInput = row.querySelector('.jumlah-input');
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                const jumlah = parseInt(jumlahInput.value) || 0;
                if (jumlah > stok) {
                    isValid = false;
                    alert(`Jumlah pinjam untuk barang "${selectedOption.text}" melebihi stok yang tersedia (${stok}).`);
                    jumlahInput.focus();
                    e.preventDefault();
                }
            }
        });

        if (isValid) {
            isSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        } else {
            e.preventDefault();
        }
    });

    document.getElementById('add-item').addEventListener('click', function() {
        const newRow = cloneNewRow();
        if (newRow) {
            document.getElementById('items-container').appendChild(newRow);
            const removeBtn = newRow.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() { newRow.remove(); });
            }
            const select = newRow.querySelector('.barang-select');
            if (select) select.addEventListener('change', updateStokInfo);
        }
    });

    const firstRowSelect = document.querySelector('.item-row .barang-select');
    if (firstRowSelect) {
        firstRowSelect.addEventListener('change', updateStokInfo);
        firstRowSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.disabled) {
                this.value = "";
                updateStokInfo({target: this});
                alert("Barang dengan stok habis tidak dapat dipilih.");
            }
        });
    }
    const firstRemoveBtn = document.querySelector('.item-row .remove-item');
    if (firstRemoveBtn) firstRemoveBtn.style.display = 'none';
</script>
@endsection