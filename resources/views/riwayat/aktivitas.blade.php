@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Riwayat Aktivitas</h2>
            <p class="text-muted mb-0">Pantau semua perubahan data dan aktivitas sistem laboratorium dalam satu panel.</p>
        </div>
    </div>

    <!-- Statistik Cepat (Card) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Hari Ini</p><h3 class="fw-bold text-primary mb-0">{{ number_format($totalHariIni) }}</h3></div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3"><i class="fas fa-calendar-day text-primary fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Minggu Ini</p><h3 class="fw-bold text-success mb-0">{{ number_format($totalMingguIni) }}</h3></div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3"><i class="fas fa-calendar-week text-success fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Bulan Ini</p><h3 class="fw-bold text-warning mb-0">{{ number_format($totalBulanIni) }}</h3></div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3"><i class="fas fa-calendar-alt text-warning fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">User Aktif</p><h3 class="fw-bold text-info mb-0">{{ number_format($totalUserAktif) }}</h3></div>
                    <div class="rounded-circle bg-info bg-opacity-10 p-3"><i class="fas fa-users text-info fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="riwayatTab" role="tablist">
        <li class="nav-item"><a class="nav-link {{ $tab == 'barang-masuk' ? 'active' : '' }}" href="{{ route('riwayat.aktivitas', array_merge(request()->except('tab'), ['tab' => 'barang-masuk'])) }}"><i class="fas fa-truck me-1"></i> Riwayat Barang Masuk</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab == 'stok' ? 'active' : '' }}" href="{{ route('riwayat.aktivitas', array_merge(request()->except('tab'), ['tab' => 'stok'])) }}"><i class="fas fa-exchange-alt me-1"></i> Riwayat Stok</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab == 'log' ? 'active' : '' }}" href="{{ route('riwayat.aktivitas', array_merge(request()->except('tab'), ['tab' => 'log'])) }}"><i class="fas fa-history me-1"></i> Log Aktivitas</a></li>
    </ul>

    <div class="tab-content">
        <!-- TAB BARANG MASUK -->
        <div class="tab-pane fade {{ $tab == 'barang-masuk' ? 'show active' : '' }}" id="barang-masuk">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="barang-masuk">
                        <div class="col">
                            <label class="form-label small fw-semibold">Cari Barang</label>
                            <input type="text" name="search_bm" class="form-control form-control-sm rounded-pill" value="{{ request('search_bm') }}" placeholder="Nama / kode">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Awal</label>
                            <input type="date" name="tanggal_awal_bm" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_awal_bm') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Akhir</label>
                            <input type="date" name="tanggal_akhir_bm" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_akhir_bm') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Semester</label>
                            <select name="id_semester_bm" class="form-select form-select-sm rounded-pill">
                                <option value="">Semua</option>
                                @foreach($semesterList as $sem)
                                    <option value="{{ $sem->id_semester }}" {{ request('id_semester_bm') == $sem->id_semester ? 'selected' : '' }}>{{ $sem->nama_semester }} {{ $sem->tahun_ajaran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-filter"></i> Filter</button>
                                <a href="{{ route('riwayat.aktivitas', ['tab' => 'barang-masuk']) }}" class="btn btn-secondary btn-sm rounded-pill"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover mb-0 align-middle" style="font-size:0.85rem">
                            <thead class="table-light"><tr><th>Tanggal</th><th>Nama Barang</th><th>Jumlah</th><th>Semester</th><th>User Penerima</th></tr></thead>
                            <tbody>
                                @forelse($barangMasuk as $bm)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->translatedFormat('d M Y H:i') }}</td>
                                    <td>{{ $bm->barang->nama_barang ?? '-' }}<br><small>{{ $bm->barang->kode_barang ?? '' }}</small></td>
                                    <td>{{ $bm->jumlah_masuk }} unit</td>
                                    <td>{{ $bm->semester ? $bm->semester->nama_semester.' '.$bm->semester->tahun_ajaran : '-' }}</td>
                                    <td>{{ $bm->user->nama ?? '-' }}@if($bm->user)<br><small>{{ $bm->user->email }}</small>@endif</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada barang masuk</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $barangMasuk->withQueryString()->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>

        <!-- TAB STOK -->
        <div class="tab-pane fade {{ $tab == 'stok' ? 'show active' : '' }}" id="stok">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="stok">
                        <div class="col">
                            <label class="form-label small fw-semibold">Cari Barang</label>
                            <input type="text" name="search_stok" class="form-control form-control-sm rounded-pill" value="{{ request('search_stok') }}" placeholder="Nama / kode">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Jenis Perubahan</label>
                            <select name="jenis_perubahan" class="form-select form-select-sm rounded-pill">
                                <option value="">Semua</option>
                                @foreach($jenisList as $j)
                                    <option value="{{ $j }}" {{ request('jenis_perubahan') == $j ? 'selected' : '' }}>{{ ucfirst($j) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Awal</label>
                            <input type="date" name="tanggal_awal_stok" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_awal_stok') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Akhir</label>
                            <input type="date" name="tanggal_akhir_stok" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_akhir_stok') }}">
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-filter"></i> Filter</button>
                                <a href="{{ route('riwayat.aktivitas', ['tab' => 'stok']) }}" class="btn btn-secondary btn-sm rounded-pill"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover mb-0 align-middle" style="font-size:0.85rem">
                            <thead class="table-light"><tr><th>Waktu</th><th>Barang</th><th>Stok Lama</th><th>Stok Baru</th><th>Perubahan</th><th>Jenis</th><th>Alasan</th><th>User</th></tr></thead>
                            <tbody>
                                @forelse($riwayatStok as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y H:i:s') }}</td>
                                    <td>{{ $item->barang->nama_barang ?? '-' }}<br><small>{{ $item->barang->kode_barang ?? '' }}</small></td>
                                    <td class="text-end">{{ number_format($item->stok_lama) }}</td>
                                    <td class="text-end">{{ number_format($item->stok_baru) }}</td>
                                    <td class="text-end {{ $item->stok_baru > $item->stok_lama ? 'text-success' : 'text-danger' }}">{{ $item->stok_baru > $item->stok_lama ? '+' : '' }}{{ number_format($item->stok_baru - $item->stok_lama) }}</td>
                                    <td><span class="badge {{ $item->jenis_perubahan == 'tambah' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">{{ ucfirst($item->jenis_perubahan) }}</span></td>
                                    <td>{{ $item->alasan ?? '-' }}</td>
                                    <td>{{ $item->user->nama ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat stok</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $riwayatStok->withQueryString()->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>

        <!-- TAB LOG AKTIVITAS -->
        <div class="tab-pane fade {{ $tab == 'log' ? 'show active' : '' }}" id="log">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="log">
                        <div class="col">
                            <label class="form-label small fw-semibold">Cari Aktivitas</label>
                            <input type="text" name="search_log" class="form-control form-control-sm rounded-pill" value="{{ request('search_log') }}" placeholder="Aktivitas / deskripsi">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Awal</label>
                            <input type="date" name="tanggal_awal_log" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_awal_log') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Tgl Akhir</label>
                            <input type="date" name="tanggal_akhir_log" class="form-control form-control-sm rounded-pill" value="{{ request('tanggal_akhir_log') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">User</label>
                            <select name="id_user" class="form-select form-select-sm rounded-pill">
                                <option value="">Semua</option>
                                @foreach($userList as $user)
                                    <option value="{{ $user->id_user }}" {{ request('id_user') == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Jenis Aktivitas</label>
                            <select name="aktivitas" class="form-select form-select-sm rounded-pill">
                                <option value="">Semua</option>
                                @foreach($jenisAktivitasList as $jns)
                                    <option value="{{ $jns }}" {{ request('aktivitas') == $jns ? 'selected' : '' }}>{{ $jns }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-filter"></i> Filter</button>
                                <a href="{{ route('riwayat.aktivitas', ['tab' => 'log']) }}" class="btn btn-secondary btn-sm rounded-pill"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover mb-0 align-middle" style="font-size:0.85rem">
                            <thead class="table-light"><tr><th>Waktu</th><th>User</th><th>Aktivitas</th><th>Deskripsi</th></tr></thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y H:i:s') }}</td>
                                    <td>{{ $log->user->nama ?? 'Sistem' }}@if($log->user)<br><small>{{ $log->user->email }}</small>@endif</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $log->aktivitas }}</span></td>
                                    <td>{{ $log->deskripsi ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada aktivitas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $logs->withQueryString()->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi tambahan -->
    <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-primary fs-5 me-2"></i>
                <small class="text-muted">Semua aktivitas seperti tambah, edit, hapus, peminjaman, pengembalian, stok opname, dan lain-lain dicatat secara otomatis.</small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const tabPane = document.getElementById(tab);
            if (tabPane) {
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                tabPane.classList.add('show', 'active');
                document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.nav-link[href*="tab=${tab}"]`);
                if (activeLink) activeLink.classList.add('active');
            }
        }
    });
</script>
@endsection