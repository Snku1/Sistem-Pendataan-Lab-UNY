@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
            <p class="text-muted">Selamat datang di Sistem Informasi Inventaris Laboratorium AV & TV</p>
        </div>
        <div class="text-end">
            <div class="datetime-badge text-white" id="realTimeDateTime">
                <i class="fas fa-calendar-alt me-1"></i> {{ date('d F Y') }} | <i class="fas fa-clock me-1"></i> {{ date('H:i:s') }} WIB
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Total Barang</p>
                    <h3 class="fw-bold mb-0">{{ number_format($totalBarang) }}</h3>
                    <i class="fas fa-boxes text-primary mt-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Barang Baik</p>
                    <h3 class="fw-bold text-success mb-0">{{ number_format($barangBaik) }}</h3>
                    <i class="fas fa-check-circle text-success mt-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Barang Rusak</p>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($barangRusak) }}</h3>
                    <i class="fas fa-exclamation-triangle text-warning mt-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Barang Hilang</p>
                    <h3 class="fw-bold text-danger mb-0">{{ number_format($barangHilang) }}</h3>
                    <i class="fas fa-times-circle text-danger mt-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Peminjaman Selesai</p>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($totalPeminjamanSelesai) }}</h3>
                    <i class="fas fa-check-double text-info mt-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3 text-center">
                    <p class="text-muted mb-1 small">Sedang Dipinjam</p>
                    <h3 class="fw-bold text-primary mb-0">{{ number_format($totalBarangSedangDipinjam) }}</h3>
                    <i class="fas fa-clock text-primary mt-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Baris 1 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Grafik Inventaris per Semester</h5>
                    <p class="text-muted small mb-0">Perbandingan kondisi barang per periode akademik (Baik / Rusak / Hilang)</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <canvas id="semesterChart" style="min-height: 250px; max-height: 250px;"></canvas>
                    <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                        @foreach($semesterLabels as $index => $label)
                            <span class="badge" style="background-color: {{ ['#0d6efd', '#198754', '#ffc107', '#0dcaf0'][$index % 4] }};">{{ $label }}</span>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center gap-4 mt-2">
                        <span class="badge bg-success">Baik</span>
                        <span class="badge bg-warning">Rusak</span>
                        <span class="badge bg-danger">Hilang</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Distribusi Kondisi Barang</h5>
                    <p class="text-muted small mb-0">Persentase status kelayakan alat</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <canvas id="kondisiChart" style="min-height: 250px; max-height: 250px;"></canvas>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <span class="badge bg-success">Baik</span>
                        <span class="badge bg-warning">Rusak</span>
                        <span class="badge bg-danger">Hilang</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Baris 2 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Grafik Barang Masuk</h5>
                    <p class="text-muted small mb-0">Total pengadaan 6 bulan terakhir</p>
                </div>
                <div class="card-body">
                    <canvas id="barangMasukChart" style="min-height: 250px; max-height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Aktivitas Peminjaman</h5>
                    <p class="text-muted small mb-0">Jumlah transaksi peminjaman per bulan</p>
                </div>
                <div class="card-body">
                    <canvas id="peminjamanChart" style="min-height: 250px; max-height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pemberitahuan Sistem & Recent Activity -->
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-bell text-warning me-2"></i>Pemberitahuan Sistem</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($stokMenipis as $stok)
                        <div class="list-group-item border-0 py-3 notification-stok-menipis">
                            <div class="d-flex">
                                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-danger mt-1"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold text-danger">{{ $stok->nama_barang }}</p>
                                    <p class="mb-0 small text-danger">⚠️ Stok menipis: Sisa {{ $stok->stok }} unit</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item border-0 text-center py-4"><p class="text-muted mb-0">Tidak ada notifikasi stok menipis</p></div>
                        @endforelse

                        @forelse($peminjamanJatuhTempo as $p)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0"><i class="fas fa-calendar-week text-danger mt-1"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold">{{ $p->kode_transaksi }}</p>
                                    <p class="mb-0 small">Peminjam: {{ $p->nama_peminjam }}</p>
                                    <p class="mb-0 small text-muted">Jatuh tempo: {{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        @endforelse

                        @if($barangMasukMenunggu > 0)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0"><i class="fas fa-truck-loading text-warning mt-1"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold">Barang masuk menunggu konfirmasi</p>
                                    <p class="mb-0 small text-muted">{{ $barangMasukMenunggu }} penerimaan perlu diverifikasi</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <div class="d-flex flex-wrap gap-2 justify-content-between mt-2">
                        <a href="{{ route('barang-masuk.index') }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-truck-loading me-1"></i> Barang Datang</a>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-success rounded-pill"><i class="fas fa-clipboard-list me-1"></i> Peminjaman Aktif</a>
                        <a href="{{ route('barang.index') }}" class="btn btn-sm btn-outline-warning rounded-pill"><i class="fas fa-boxes me-1"></i> Data Barang</a>
                        <a href="{{ route('stok.index') }}" class="btn btn-sm btn-outline-info rounded-pill"><i class="fas fa-chart-line me-1"></i> Manajemen Stok</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-history text-primary me-2"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">AKTIVITAS</th>
                                    <th>NAMA BARANG</th>
                                    <th>USER</th>
                                    <th>TANGGAL</th>
                                    <th class="pe-3">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="ps-3">{{ $activity->aktivitas }}</td>
                                    <td>
                                        @php
                                            $namaBarang = '-';
                                            $deskripsi = $activity->deskripsi ?? '';
                                            if (preg_match('/(?:barang|Barang)\s+(.+?)(?:\s+dengan|\s+sebanyak|\s+dari|\s*$)/', $deskripsi, $matches)) $namaBarang = trim($matches[1]);
                                            elseif (preg_match('/Menambah barang (.+?) dengan kode/', $deskripsi, $matches)) $namaBarang = trim($matches[1]);
                                            elseif (preg_match('/Menghapus barang (.+)/', $deskripsi, $matches)) $namaBarang = trim($matches[1]);
                                            if ($namaBarang == '-' && !empty($deskripsi)) $namaBarang = Str::limit($deskripsi, 40);
                                        @endphp
                                        {{ $namaBarang }}
                                    </td>
                                    <td>{{ $activity->user->nama ?? 'System' }}</td>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="pe-3">
                                        @php
                                            $statusText = 'Success';
                                            $statusClass = 'success';
                                            if(str_contains($activity->aktivitas, 'Login')) { $statusText = 'Online'; $statusClass = 'info'; }
                                            elseif(str_contains($activity->aktivitas, 'Update')) { $statusText = 'Updated'; $statusClass = 'warning'; }
                                            elseif(str_contains($activity->aktivitas, 'Masuk')) { $statusText = 'Inbound'; $statusClass = 'primary'; }
                                            elseif(str_contains($activity->aktivitas, 'Rusak') || str_contains($activity->aktivitas, 'Hilang')) { $statusText = 'Alert'; $statusClass = 'danger'; }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-1 rounded-pill">{{ $statusText }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada aktivitas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Inventaris per Semester (stacked bar chart untuk Baik, Rusak, Hilang)
    const semesterCtx = document.getElementById('semesterChart').getContext('2d');
    new Chart(semesterCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($semesterLabels) !!},
            datasets: [
                {
                    label: 'Baik',
                    data: {!! json_encode($semesterBaik) !!},
                    backgroundColor: '#198754',
                    borderRadius: 4,
                    stack: 'stack0'
                },
                {
                    label: 'Rusak',
                    data: {!! json_encode($semesterRusak) !!},
                    backgroundColor: '#ffc107',
                    borderRadius: 4,
                    stack: 'stack0'
                },
                {
                    label: 'Hilang',
                    data: {!! json_encode($semesterHilang) !!},
                    backgroundColor: '#dc3545',
                    borderRadius: 4,
                    stack: 'stack0'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Jumlah Unit' } }
            }
        }
    });

    // Grafik Distribusi Kondisi Barang
    const kondisiCtx = document.getElementById('kondisiChart').getContext('2d');
    new Chart(kondisiCtx, {
        type: 'pie',
        data: {
            labels: ['Baik', 'Rusak', 'Hilang'],
            datasets: [{
                data: [{{ $barangBaik }}, {{ $barangRusak }}, {{ $barangHilang }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } }
        }
    });

    // Grafik Barang Masuk
    const barangMasukCtx = document.getElementById('barangMasukChart').getContext('2d');
    new Chart(barangMasukCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($bulanLabels) !!},
            datasets: [{
                label: 'Jumlah Barang Masuk',
                data: {!! json_encode($bulanData) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.05)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Grafik Aktivitas Peminjaman
    const peminjamanCtx = document.getElementById('peminjamanChart').getContext('2d');
    new Chart(peminjamanCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($peminjamanLabels) !!},
            datasets: [{
                label: 'Jumlah Transaksi Peminjaman',
                data: {!! json_encode($peminjamanData) !!},
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.05)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#198754',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top' } }
        }
    });
</script>

<style>
    .notification-stok-menipis { background-color: #fee2e2; border-left: 4px solid #dc3545; }
    .card-hover { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
    .card-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important; }
    .datetime-badge { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 0.5rem 1rem; border-radius: 50px; }
</style>
@endsection