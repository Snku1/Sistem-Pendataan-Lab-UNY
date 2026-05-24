@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Laporan Inventaris</h2>
            <p class="text-muted mb-0">Kelola, tinjau, dan ekspor data aset laboratorium secara komprehensif.</p>
        </div>
    </div>

    <!-- 4 Statistik Cards (Universal) -->
    @php
    if ($jenis == 'barang') {
        $totalData = $stats['total'] ?? 0;
        $totalBarang = $stats['totalUnit'] ?? 0;
        $totalRusak = $stats['rusak'] ?? 0;
        $totalHilang = $stats['hilang'] ?? 0;
    } elseif ($jenis == 'barang-masuk') {
        $totalData = $stats['totalTransaksi'] ?? 0;
        $totalBarang = $stats['totalJumlah'] ?? 0;
        $totalRusak = 0;
        $totalHilang = 0;
    } elseif ($jenis == 'riwayat-peminjaman') {
        $totalData = $stats['totalSelesai'] ?? 0;
        $totalBarang = $stats['totalUnit'] ?? 0;
        $totalRusak = $stats['rusak'] ?? 0;
        $totalHilang = $stats['hilang'] ?? 0;
    } else {
        $totalData = $stats['totalBarang'] ?? 0;
        $totalBarang = $stats['totalStok'] ?? 0;
        $totalRusak = 0;
        $totalHilang = 0;
    }
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Data</p><h3 class="fw-bold text-primary mb-0">{{ number_format($totalData) }}</h3></div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3"><i class="fas fa-database text-primary fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Barang</p><h3 class="fw-bold text-success mb-0">{{ number_format($totalBarang) }} Unit</h3></div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3"><i class="fas fa-boxes text-success fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Rusak</p><h3 class="fw-bold text-warning mb-0">{{ number_format($totalRusak) }} Item</h3></div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3"><i class="fas fa-exclamation-triangle text-warning fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Hilang</p><h3 class="fw-bold text-danger mb-0">{{ number_format($totalHilang) }} Item</h3></div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3"><i class="fas fa-times-circle text-danger fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Parameter Laporan -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Filter Parameter Laporan</h5>
            <form method="GET" action="{{ route('laporan.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <div class="col">
                    <label class="form-label small fw-semibold">RENTANG TANGGAL</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="tanggal_awal" class="form-control rounded-start-pill" value="{{ $tanggalAwal }}">
                        <span class="input-group-text">–</span>
                        <input type="date" name="tanggal_akhir" class="form-control rounded-end-pill" value="{{ $tanggalAkhir }}">
                    </div>
                </div>
                <div class="col">
                    <label class="form-label small fw-semibold">SEMESTER</label>
                    <select name="id_semester" class="form-select form-select-sm rounded-pill">
                        <option value="">Semua Semester</option>
                        @foreach($semesterList as $sem)
                        <option value="{{ $sem->id_semester }}" {{ $semesterId == $sem->id_semester ? 'selected' : '' }}>
                            {{ $sem->nama_semester }} {{ $sem->tahun_ajaran }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @if($jenis == 'barang-masuk')
                <div class="col">
                    <label class="form-label small fw-semibold">STATUS</label>
                    <select name="status" class="form-select form-select-sm rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ $status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    </select>
                </div>
                @endif
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('laporan.index', ['jenis' => $jenis]) }}" class="btn btn-secondary btn-sm rounded-pill px-3"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jenis Laporan Tersedia -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Jenis Laporan Tersedia</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('laporan.index', array_merge(request()->except('jenis'), ['jenis' => 'barang'])) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 rounded-4 border {{ $jenis == 'barang' ? 'bg-primary bg-opacity-10 border-primary' : 'border-secondary' }}">
                            <div class="flex-shrink-0"><i class="fas fa-boxes fs-2 text-primary"></i></div>
                            <div class="flex-grow-1 ms-3"><h6 class="fw-bold mb-1 text-dark">Data Barang</h6><small class="text-muted">Status ketersediaan barang saat ini</small></div>
                            @if($jenis == 'barang')<i class="fas fa-check-circle text-primary"></i>@endif
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('laporan.index', array_merge(request()->except('jenis'), ['jenis' => 'barang-masuk'])) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 rounded-4 border {{ $jenis == 'barang-masuk' ? 'bg-primary bg-opacity-10 border-primary' : 'border-secondary' }}">
                            <div class="flex-shrink-0"><i class="fas fa-truck fs-2 text-success"></i></div>
                            <div class="flex-grow-1 ms-3"><h6 class="fw-bold mb-1 text-dark">Barang Datang</h6><small class="text-muted">Riwayat penerimaan barang</small></div>
                            @if($jenis == 'barang-masuk')<i class="fas fa-check-circle text-primary"></i>@endif
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('laporan.index', array_merge(request()->except('jenis'), ['jenis' => 'riwayat-peminjaman'])) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 rounded-4 border {{ $jenis == 'riwayat-peminjaman' ? 'bg-primary bg-opacity-10 border-primary' : 'border-secondary' }}">
                            <div class="flex-shrink-0"><i class="fas fa-history fs-2 text-info"></i></div>
                            <div class="flex-grow-1 ms-3"><h6 class="fw-bold mb-1 text-dark">Riwayat Peminjaman</h6><small class="text-muted">Peminjaman selesai</small></div>
                            @if($jenis == 'riwayat-peminjaman')<i class="fas fa-check-circle text-primary"></i>@endif
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('laporan.index', array_merge(request()->except('jenis'), ['jenis' => 'manajemen-stok'])) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 rounded-4 border {{ $jenis == 'manajemen-stok' ? 'bg-primary bg-opacity-10 border-primary' : 'border-secondary' }}">
                            <div class="flex-shrink-0"><i class="fas fa-chart-line fs-2 text-secondary"></i></div>
                            <div class="flex-grow-1 ms-3"><h6 class="fw-bold mb-1 text-dark">Manajemen Stok</h6><small class="text-muted">Rekap pergerakan stok per periode</small></div>
                            @if($jenis == 'manajemen-stok')<i class="fas fa-check-circle text-primary"></i>@endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Laporan -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-semibold mb-0"><i class="fas fa-eye me-2 text-primary"></i> Preview Laporan:
                @if($jenis == 'barang') Data Barang
                @elseif($jenis == 'barang-masuk') Barang Datang
                @elseif($jenis == 'riwayat-peminjaman') Riwayat Peminjaman
                @else Manajemen Stok
                @endif
            </h5>
            <div>
                <a href="{{ route('laporan.export-pdf', $jenis) . '?' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-danger rounded-pill me-2"><i class="fas fa-file-pdf"></i> PDF</a>
                <a href="{{ route('laporan.export-csv', $jenis) . '?' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-success rounded-pill me-2"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="{{ route('laporan.export-excel', $jenis) . '?' . http_build_query(request()->except('page')) }}" class="btn btn-sm btn-primary rounded-pill"><i class="fas fa-file-excel"></i> Excel</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" style="font-size:0.85rem">
                    <thead class="table-light">
                        <tr>
                            @if($jenis == 'barang')
                                <th class="ps-2">Merk Alat</th><th>Nama Alat</th><th>Deskripsi</th>
                                <th class="text-success text-end">Baik</th><th class="text-warning text-end">Rusak</th>
                                <th class="text-danger text-end">Hilang</th><th class="text-end">Total</th>
                                <th>Kapasitas Alat</th><th>Keterangan</th>
                            @elseif($jenis == 'barang-masuk')
                                <th>Tanggal</th><th>Nama Barang</th><th>Jumlah</th><th>Sumber</th><th>Pemeriksa</th><th>Status</th><th>Catatan</th>
                            @elseif($jenis == 'riwayat-peminjaman')
                                <th>Kode</th><th>Peminjam</th><th>Tanggal Pinjam</th><th>Tanggal Kembali</th><th>Total</th><th>Rusak</th><th>Hilang</th>
                                <th>Aksi</th>
                            @elseif($jenis == 'manajemen-stok')
                                <th>Nama Barang</th><th>Merk</th>
                                <th class="text-end">Stok Awal</th><th class="text-end">Stok Masuk</th>
                                <th class="text-end">Stok Keluar</th><th class="text-end">Stok Akhir</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($previewData as $row)
                        <tr>
                            @if($jenis == 'barang')
                                <td class="ps-2">{{ $row->merk ?? '-' }}</td>
                                <td>{{ $row->nama_barang }}</td>
                                <td>{{ Str::limit($row->deskripsi, 80) ?? '-' }}</td>
                                <td class="text-end">{{ number_format($row->jumlah_baik) }}</td>
                                <td class="text-end">{{ number_format($row->jumlah_rusak) }}</td>
                                <td class="text-end">{{ number_format($row->jumlah_hilang) }}</td>
                                <td class="text-end">{{ number_format($row->stok) }}</td>
                                @php $kapasitasAngka = preg_replace('/[^0-9]/', '', $row->kapasitas); $kapasitasTampil = $kapasitasAngka ?: '-'; @endphp
                                <td>{{ $kapasitasTampil }}</td>
                                <td>{{ $row->keterangan ?? '-' }}</td>
                            @elseif($jenis == 'barang-masuk')
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                                <td>{{ $row->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $row->jumlah_masuk }}</td>
                                <td>{{ $row->sumber ?? '-' }}</td>
                                <td>{{ $row->penanggungJawab->nama_pj ?? '-' }}</td>
                                <td>{{ $row->status == 'menunggu' ? 'Menunggu' : 'Diterima' }}</td>
                                <td>{{ $row->catatan_pemeriksaan ?? '-' }}</td>
                            @elseif($jenis == 'riwayat-peminjaman')
                                @php 
                                    $tgl = $row->details->where('status_item','kembali')->max('tanggal_kembali_aktual'); 
                                    $rusak = $row->details->where('kondisi_setelah','rusak')->sum('jumlah'); 
                                    $hilang = $row->details->where('kondisi_setelah','hilang')->sum('jumlah'); 
                                @endphp
                                <td>{{ $row->kode_transaksi }}</td>
                                <td>{{ $row->nama_peminjam }}@if($row->nim)<br><small>{{ $row->nim }}</small>@endif</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_penggunaan)->format('d/m/Y') }}</td>
                                <td>{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $row->details->sum('jumlah') }}</td>
                                <td>{{ $rusak }}</td>
                                <td>{{ $hilang }}</td>
                                <td class="pe-2">
                                    <!-- Tombol Detail (Modal) -->
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $row->id_peminjaman }}" title="Detail Barang">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <!-- Tombol PDF -->
                                    <a href="{{ route('peminjaman.export-detail-pdf', $row->id_peminjaman) }}" class="btn btn-sm btn-danger rounded-pill me-1" target="_blank" title="Export PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <!-- Tombol CSV -->
                                    <a href="{{ route('peminjaman.export-detail-csv', $row->id_peminjaman) }}" class="btn btn-sm btn-success rounded-pill" title="Export CSV">
                                        <i class="fas fa-file-csv"></i>
                                    </a>
                                </td>
                            @elseif($jenis == 'manajemen-stok')
                                <td>{{ $row->nama_barang }}</td>
                                <td>{{ $row->merk }}</td>
                                <td class="text-end">{{ number_format($row->stok_awal) }}</td>
                                <td class="text-end">{{ number_format($row->stok_masuk) }}</td>
                                <td class="text-end">{{ number_format($row->stok_keluar) }}</td>
                                <td class="text-end fw-bold {{ $row->stok_akhir <= 2 ? 'text-danger' : '' }}">{{ number_format($row->stok_akhir) }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            @if($jenis == 'riwayat-peminjaman')
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data yang sesuai dengan filter</td>
                            @else
                                <td colspan="10" class="text-center text-muted py-4">Tidak ada data yang sesuai dengan filter</td>
                            @endif
                        </tr>
                        @endforelse
                    </tbody>
                    @if($jenis == 'barang')
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end">{{ number_format($previewData->sum('jumlah_baik')) }}</td>
                            <td class="text-end">{{ number_format($previewData->sum('jumlah_rusak')) }}</td>
                            <td class="text-end">{{ number_format($previewData->sum('jumlah_hilang')) }}</td>
                            <td class="text-end">{{ number_format($previewData->sum('stok')) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            <small class="text-muted">Menampilkan {{ $previewData->count() }} data terbaru dari total {{ $totalData }} data (berdasarkan filter).</small>
        </div>
    </div>
</div>

<!-- Modal untuk detail barang peminjaman -->
@if($jenis == 'riwayat-peminjaman')
    @foreach($previewData as $row)
    <div class="modal fade" id="modalDetail{{ $row->id_peminjaman }}" tabindex="-1" aria-labelledby="modalLabel{{ $row->id_peminjaman }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $row->id_peminjaman }}">Detail Peminjaman - {{ $row->kode_transaksi }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Informasi peminjaman -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Peminjam:</strong> {{ $row->nama_peminjam }} @if($row->nim) ({{ $row->nim }}) @endif</p>
                            <p><strong>Email:</strong> {{ $row->email }}</p>
                            <p><strong>Tanggal Penggunaan:</strong> {{ \Carbon\Carbon::parse($row->tanggal_penggunaan)->translatedFormat('d F Y') }}</p>
                            <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($row->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status Transaksi:</strong> <span class="badge {{ $row->status_transaksi == 'aktif' ? 'bg-warning' : 'bg-success' }}">{{ ucfirst($row->status_transaksi) }}</span></p>
                            <p><strong>Catatan Awal:</strong> {{ $row->catatan_awal ?? '-' }}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Daftar Barang yang Dipinjam</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Merk</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Kondisi Awal</th>
                                    <th>Kondisi Setelah</th>
                                    <th>Status Item</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Catatan Kembali</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($row->details as $detail)
                                <tr>
                                    <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $detail->barang->merk ?? '-' }}</td>
                                    <td class="text-end">{{ $detail->jumlah }}</td>
                                    <td>{{ ucfirst($detail->kondisi_awal) }}</td>
                                    <td>{{ ucfirst($detail->kondisi_setelah ?? '-') }}</td>
                                    <td>
                                        @if($detail->status_item == 'dipinjam')
                                            <span class="badge bg-warning">Dipinjam</span>
                                        @else
                                            <span class="badge bg-success">Kembali</span>
                                        @endif
                                    </td>
                                    <td>{{ $detail->tanggal_kembali_aktual ? \Carbon\Carbon::parse($detail->tanggal_kembali_aktual)->translatedFormat('d M Y') : '-' }}</td>
                                    <td>{{ $detail->catatan_kembali ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif

@endsection