@extends('layouts.app')

@section('title', 'Tambah Stok Opname')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tambah Stok Opname</h2>
            <p class="text-muted">Input hasil stock opname barang</p>
        </div>
        <div>
            <a href="{{ route('stok-opname.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('stok-opname.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Tanggal Opname <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_opname" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Data Barang</h5>
                <div id="items-container">
                    <div class="item-row row g-2 mb-2">
                        <div class="col-md-5">
                            <label>Barang</label>
                            <select name="items[0][id_barang]" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barang as $b)
                                    <option value="{{ $b->id_barang }}">{{ $b->nama_barang }} (Stok sistem: {{ $b->stok }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Stok Fisik</label>
                            <input type="number" name="items[0][stok_fisik]" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label>Catatan</label>
                            <input type="text" name="items[0][catatan]" class="form-control" placeholder="Selisih, kondisi, dll">
                        </div>
                        <div class="col-md-1 align-self-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display:none;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-plus"></i> Tambah Barang</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Opname</button>
                    <a href="{{ route('stok-opname.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row g-2 mb-2';
        newRow.innerHTML = `
            <div class="col-md-5">
                <select name="items[${itemIndex}][id_barang]" class="form-select" required>
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->id_barang }}">{{ $b->nama_barang }} (Stok sistem: {{ $b->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${itemIndex}][stok_fisik]" class="form-control" min="0" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="items[${itemIndex}][catatan]" class="form-control" placeholder="Selisih, kondisi, dll">
            </div>
            <div class="col-md-1 align-self-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button>
            </div>
        `;
        container.appendChild(newRow);
        newRow.querySelector('.remove-item').addEventListener('click', function() { newRow.remove(); });
        itemIndex++;
    });
</script>
@endsection