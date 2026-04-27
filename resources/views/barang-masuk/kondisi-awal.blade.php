@extends('layouts.app')

@section('title', 'Kondisi Awal Barang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Kondisi Awal & Konfirmasi Penerimaan</h2>
            <p class="text-muted">ID Transaksi: TR-IN-{{ str_pad($barangMasuk->id_masuk, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('barang-masuk.update-kondisi-awal', $barangMasuk->id_masuk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="form-label fw-semibold">Pilih Kondisi Awal Barang <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-4 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kondisi_penerimaan" value="baik" id="kondisi_baik" {{ old('kondisi_penerimaan', $barangMasuk->kondisi_penerimaan) == 'baik' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="kondisi_baik">Baik</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kondisi_penerimaan" value="rusak" id="kondisi_rusak" {{ old('kondisi_penerimaan', $barangMasuk->kondisi_penerimaan) == 'rusak' ? 'checked' : '' }}>
                            <label class="form-check-label" for="kondisi_rusak">Rusak</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kondisi_penerimaan" value="tidak_sesuai" id="kondisi_tidak_sesuai" {{ old('kondisi_penerimaan', $barangMasuk->kondisi_penerimaan) == 'tidak_sesuai' ? 'checked' : '' }}>
                            <label class="form-check-label" for="kondisi_tidak_sesuai">Tidak Sesuai</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Upload Bukti Foto Barang</label>
                    @if($barangMasuk->bukti_foto)
                        <div class="mb-2">
                            <a href="{{ Storage::url($barangMasuk->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                <i class="fas fa-image"></i> Lihat Foto Saat Ini
                            </a>
                        </div>
                    @endif
                    <input type="file" name="bukti_foto" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted">JPG, PNG, WebP (Maks. 2MB). Minimal 3 sudut pandang berbeda.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Catatan Pemeriksaan</label>
                    <textarea name="catatan_pemeriksaan" class="form-control" rows="3" placeholder="Tambahkan catatan jika ada kerusakan atau ketidaksesuaian jumlah...">{{ old('catatan_pemeriksaan', $barangMasuk->catatan_pemeriksaan) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-check-circle me-2"></i>Simpan & Konfirmasi Penerimaan
                    </button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary rounded-pill px-4">
                        Batal
                    </a>
                </div>
                @if($barangMasuk->status == 'diterima')
                    <div class="mt-3 alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Barang ini sudah diterima. Perubahan kondisi, foto, atau catatan akan disimpan tetapi stok tidak berubah.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection