@extends('layouts.app')

@section('title', 'Pengembalian Barang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pengembalian Barang</h2>
            <p class="text-muted">Kode Transaksi: {{ $peminjaman->kode_transaksi }}</p>
        </div>
        <div>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="text-muted small">Peminjam</label>
                    <p class="fw-semibold">{{ $peminjaman->nama_peminjam }} ({{ $peminjaman->email }})</p>
                </div>
                <div class="col-md-4">
                    <label class="text-muted small">Tanggal Peminjaman</label>
                    <p class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_penggunaan)->translatedFormat('d M Y') }}</p>
                </div>
                <div class="col-md-4">
                    <label class="text-muted small">Jatuh Tempo</label>
                    <p class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            <form action="{{ route('peminjaman.proses-pengembalian', $peminjaman->id_peminjaman) }}" method="POST" id="formPengembalian">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                </div>

                <h5 class="fw-semibold mt-4 mb-3">Daftar Barang yang Dipinjam</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px; text-align: center;"><input type="checkbox" id="checkAll"></th>
                                <th>Nama Barang</th>
                                <th style="width: 80px; text-align: center;">Jumlah</th>
                                <th style="width: 200px;">Kondisi Setelah <span class="text-danger">*</span></th>
                                <th>Catatan Kembali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman->details as $detail)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" class="item-checkbox" data-id="{{ $detail->id_detail }}">
                                </td>
                                <td>{{ $detail->barang->nama_barang }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $detail->jumlah }}</td>
                                <td>
                                    <select class="form-select form-select-sm kondisi-select" data-id="{{ $detail->id_detail }}" disabled>
                                        <option value="baik">Baik</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                    <input type="hidden" name="items[{{ $loop->index }}][id_detail]" class="hidden-id" data-id="{{ $detail->id_detail }}" value="">
                                    <input type="hidden" name="items[{{ $loop->index }}][kondisi_setelah]" class="hidden-kondisi" data-id="{{ $detail->id_detail }}" value="">
                                    <input type="hidden" name="items[{{ $loop->index }}][catatan_kembali]" class="hidden-catatan" data-id="{{ $detail->id_detail }}" value="">
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm catatan-text" data-id="{{ $detail->id_detail }}" rows="1" placeholder="Catatan jika rusak/hilang..." disabled></textarea>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="button" id="btnKonfirmasi" class="btn btn-success rounded-pill px-4" disabled>
                        <i class="fas fa-check-circle me-2"></i>Konfirmasi Pengembalian
                    </button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary rounded-pill px-4 ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const kondisiSelects = document.querySelectorAll('.kondisi-select');
        const catatanTexts = document.querySelectorAll('.catatan-text');
        const checkAll = document.getElementById('checkAll');
        const btnKonfirmasi = document.getElementById('btnKonfirmasi');
        const form = document.getElementById('formPengembalian');

        function updateButtonState() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            btnKonfirmasi.disabled = !anyChecked;
        }

        function syncHiddenValues() {
            checkboxes.forEach((cb, idx) => {
                const kondisiSelect = kondisiSelects[idx];
                const catatanText = catatanTexts[idx];
                const hiddenId = document.querySelector(`.hidden-id[data-id="${cb.dataset.id}"]`);
                const hiddenKondisi = document.querySelector(`.hidden-kondisi[data-id="${cb.dataset.id}"]`);
                const hiddenCatatan = document.querySelector(`.hidden-catatan[data-id="${cb.dataset.id}"]`);
                if (cb.checked) {
                    hiddenId.value = cb.dataset.id;
                    hiddenKondisi.value = kondisiSelect.value;
                    hiddenCatatan.value = catatanText.value;
                } else {
                    hiddenId.value = '';
                    hiddenKondisi.value = '';
                    hiddenCatatan.value = '';
                }
            });
        }

        function updateInputsState() {
            checkboxes.forEach((cb, idx) => {
                const kondisiSelect = kondisiSelects[idx];
                const catatanText = catatanTexts[idx];
                if (cb.checked) {
                    kondisiSelect.disabled = false;
                    catatanText.disabled = false;
                } else {
                    kondisiSelect.disabled = true;
                    catatanText.disabled = true;
                }
            });
            syncHiddenValues();
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateInputsState();
                updateButtonState();
            });
        });

        kondisiSelects.forEach(select => {
            select.addEventListener('change', syncHiddenValues);
        });
        catatanTexts.forEach(text => {
            text.addEventListener('input', syncHiddenValues);
        });

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateInputsState();
            updateButtonState();
        });

        updateInputsState();
        updateButtonState();

        btnKonfirmasi.addEventListener('click', function(e) {
            e.preventDefault();
            // Hapus hidden inputs yang tidak terisi
            document.querySelectorAll('input.hidden-id').forEach(input => {
                if (input.value === '') input.remove();
            });
            document.querySelectorAll('input.hidden-kondisi').forEach(input => {
                if (input.value === '') input.remove();
            });
            document.querySelectorAll('input.hidden-catatan').forEach(input => {
                if (input.value === '') input.remove();
            });
            form.submit();
        });
    });
</script>
@endsection