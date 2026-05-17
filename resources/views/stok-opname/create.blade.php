@extends('layouts.app')

@section('title', 'Tambah Stok Opname')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tambah Stok Opname</h2>
            <p class="text-muted">Input hasil stock opname untuk semua barang</p>
        </div>
        <div>
            <a href="{{ route('stok-opname.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Semester Aktif -->
    <div class="alert alert-info bg-opacity-10 border-0 rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                Stok opname ini akan dicatat untuk semester:
                <strong>
                    @php
                        $activeSemesterId = session('active_semester_id');
                        $semesterLabel = 'Semua Semester';
                        if ($activeSemesterId && $activeSemesterId != 0) {
                            $semester = App\Models\Semester::find($activeSemesterId);
                            if ($semester) {
                                $semesterLabel = $semester->nama_semester . ' - ' . $semester->tahun_ajaran;
                            }
                        }
                    @endphp
                    {{ $semesterLabel }}
                </strong>
                <br><small class="text-muted">Barang yang ditampilkan hanya dari semester tersebut.</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('stok-opname.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Opname <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_opname" class="form-control rounded-pill" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan Pengecekan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan keseluruhan opname..."></textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Daftar Barang (Semester Aktif)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Stok Sistem</th>
                                <th>Stok Fisik <span class="text-danger">*</span></th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang as $index => $b)
                            <tr>
                                <td>{{ $b->kode_barang }}</td>
                                <td>{{ $b->nama_barang }} @if($b->merk) ({{ $b->merk }}) @endif</td>
                                <td class="text-center fw-bold">{{ $b->stok }}</td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][stok_fisik]" class="form-control form-control-sm" 
                                           min="0" value="{{ $b->stok }}" required>
                                    <input type="hidden" name="items[{{ $index }}][id_barang]" value="{{ $b->id_barang }}">
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $index }}][catatan]" class="form-control form-control-sm" 
                                           placeholder="Selisih, kondisi, dll">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada barang pada semester aktif ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4" {{ count($barang) == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-save me-2"></i>Simpan Opname
                    </button>
                    <a href="{{ route('stok-opname.index') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection