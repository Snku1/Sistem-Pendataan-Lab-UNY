@extends('layouts.app')

@section('title', 'Tambah Penerimaan Barang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tambah Penerimaan Barang</h2>
            <p class="text-muted mb-0">Catat barang yang baru datang ke laboratorium (bisa lebih dari satu)</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('barang-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Bagian informasi umum -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tanggal Datang <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_masuk" class="form-control form-control-sm rounded-pill @error('tanggal_masuk') is-invalid @enderror" 
                               value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                        @error('tanggal_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Supplier / Sumber</label>
                        <input type="text" name="sumber" class="form-control form-control-sm rounded-pill @error('sumber') is-invalid @enderror" 
                               value="{{ old('sumber') }}" placeholder="Contoh: PT. Science Medika">
                        @error('sumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Pemeriksa Barang (Teknisi)</label>
                        <select name="id_penanggung_jawab" class="form-select form-select-sm rounded-pill @error('id_penanggung_jawab') is-invalid @enderror">
                            <option value="">Pilih Pemeriksa</option>
                            @foreach($penanggungJawabList as $pj)
                                <option value="{{ $pj->id_pj }}" {{ old('id_penanggung_jawab') == $pj->id_pj ? 'selected' : '' }}>
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
                            <option value="menunggu" {{ old('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="diterima" {{ old('status') == 'diterima' ? 'selected' : '' }}>Langsung Diterima</option>
                        </select>
                        <small class="text-muted">Jika dipilih Diterima, stok barang akan langsung bertambah.</small>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Daftar Barang yang Diterima</h5>
                <div id="items-container">
                    <!-- Baris pertama -->
                    <div class="item-row row g-2 mb-3 align-items-start">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <select name="items[0][id_barang]" class="form-select form-select-sm barang-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barang as $b)
                                <option value="{{ $b->id_barang }}">
                                    {{ $b->nama_barang }} ({{ $b->merk ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Bukti Foto</label>
                            <input type="file" name="items[0][foto]" class="form-control form-control-sm foto-input" accept="image/*">
                            <small class="text-muted">JPG, PNG (max 2MB)</small>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display: none;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary mt-2 rounded-pill"><i class="fas fa-plus"></i> Tambah Barang</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Simpan Semua Penerimaan
                    </button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIndex = 0;
    function cloneNewRow() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        if (!firstRow) return null;
        const newRow = firstRow.cloneNode(true);
        itemIndex++;
        const select = newRow.querySelector('.barang-select');
        const jumlahInput = newRow.querySelector('.jumlah-input');
        const fotoInput = newRow.querySelector('.foto-input');
        if (select) {
            select.name = `items[${itemIndex}][id_barang]`;
            select.selectedIndex = 0;
        }
        if (jumlahInput) {
            jumlahInput.name = `items[${itemIndex}][jumlah]`;
            jumlahInput.value = 1;
        }
        if (fotoInput) {
            fotoInput.name = `items[${itemIndex}][foto]`;
            fotoInput.value = '';
        }
        const labels = newRow.querySelectorAll('.col-md-5 > label, .col-md-2 > label, .col-md-4 > label');
        labels.forEach(label => label.remove());
        const removeBtn = newRow.querySelector('.remove-item');
        if (removeBtn) removeBtn.style.display = 'inline-block';
        return newRow;
    }
    document.getElementById('add-item').addEventListener('click', function() {
        const newRow = cloneNewRow();
        if (newRow) {
            document.getElementById('items-container').appendChild(newRow);
            const removeBtn = newRow.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() { newRow.remove(); });
            }
        }
    });
    const firstRemoveBtn = document.querySelector('.item-row .remove-item');
    if (firstRemoveBtn) firstRemoveBtn.style.display = 'none';
</script>
@endsection