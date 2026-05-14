@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Header dengan Tanggal dan Waktu -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Selamat datang di Sistem Informasi Inventaris Laboratorium AV & TV</p>
        </div>
        <div class="text-end">
            <div class="datetime-badge text-white" id="realTimeDateTime">
                <i class="fas fa-calendar-alt me-1"></i> {{ date('d F Y') }} | <i class="fas fa-clock me-1"></i> {{ date('H:i:s') }} WIB
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Barang</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalBarang) }}</h3>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-boxes text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Barang Baik</p>
                            <h3 class="fw-bold text-success mb-0">{{ number_format($barangBaik) }}</h3>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Barang Rusak</p>
                            <h3 class="fw-bold text-warning mb-0">{{ number_format($barangRusak) }}</h3>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Barang Hilang</p>
                            <h3 class="fw-bold text-danger mb-0">{{ number_format($barangHilang) }}</h3>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Grafik Inventaris per Semester
                    </h5>
                    <p class="text-muted small mb-0">Perbandingan kondisi barang per periode akademik</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <canvas id="semesterChart" style="min-height: 250px; max-height: 250px;"></canvas>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <span class="badge bg-primary">Semester 1</span>
                        <span class="badge bg-success">Semester 2</span>
                        <span class="badge bg-warning">Semester 3</span>
                        <span class="badge bg-info">Semester 4</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Grafik Kondisi Barang
                    </h5>
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

    <!-- Second Row Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Grafik Barang Masuk
                    </h5>
                    <p class="text-muted small mb-0">Total pengadaan bulanan semester ini</p>
                </div>
                <div class="card-body">
                    <canvas id="barangMasukChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-chart-simple text-primary me-2"></i>Penggunaan Praktikum
                    </h5>
                    <p class="text-muted small mb-0">Perbandingan alat dipakai vs kapasitas</p>
                </div>
                <div class="card-body">
                    <canvas id="praktikumChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications & Recent Activity -->
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-bell text-warning me-2"></i>Quick Notifications
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($stokMenipis as $stok)
                        <div class="list-group-item border-0 py-3 notification-stok-menipis">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-danger mt-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold text-danger">{{ $stok->nama_barang }}</p>
                                    <p class="mb-0 small text-danger">⚠️ Stok menipis: Sisa {{ $stok->stok }} unit</p>
                                    <small class="text-muted">Segera lakukan pengadaan!</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item border-0 text-center py-4">
                            <p class="text-muted mb-0">Tidak ada notifikasi stok menipis</p>
                        </div>
                        @endforelse

                        @forelse($barangRusakTerbaru as $rusak)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-tools text-warning mt-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold">{{ $rusak->nama_barang }}</p>
                                    <p class="mb-0 small text-muted">Barang rusak</p>
                                    <small class="text-muted">-</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        @endforelse

                        @forelse($barangMasukTerbaru as $masuk)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-box-open text-success mt-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fw-semibold">{{ $masuk->barang->nama_barang ?? 'Barang' }}</p>
                                    <p class="mb-0 small text-muted">Barang baru datang: {{ $masuk->jumlah_masuk }} unit</p>
                                    <small class="text-muted">{{ $masuk->tanggal_masuk ? $masuk->tanggal_masuk->diffForHumans() : '-' }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="#" class="text-primary text-decoration-none small fw-semibold">
                        Lihat Semua Notifikasi <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-history text-primary me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3">AKTIVITAS</th>
                                    <th class="py-3">NAMA BARANG</th>
                                    <th class="py-3">USER</th>
                                    <th class="py-3">TANGGAL</th>
                                    <th class="pe-3 py-3">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="ps-3">{{ $activity->aktivitas }}</td>
                                    <td>
                                        @php
                                            // Ekstrak nama barang dari deskripsi
                                            $namaBarang = '-';
                                            $deskripsi = $activity->deskripsi ?? '';
                                            if (preg_match('/(?:barang|Barang)\s+(.+?)(?:\s+dengan|\s+sebanyak|\s+dari|\s*$)/', $deskripsi, $matches)) {
                                                $namaBarang = trim($matches[1]);
                                            } elseif (preg_match('/Menambah barang (.+?) dengan kode/', $deskripsi, $matches)) {
                                                $namaBarang = trim($matches[1]);
                                            } elseif (preg_match('/Menghapus barang (.+)/', $deskripsi, $matches)) {
                                                $namaBarang = trim($matches[1]);
                                            }
                                            if ($namaBarang == '-' && !empty($deskripsi)) {
                                                $namaBarang = Str::limit($deskripsi, 40);
                                            }
                                        @endphp
                                        {{ $namaBarang }}
                                    </td>
                                    <td>{{ $activity->user->nama ?? 'System' }}</td>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="pe-3">
                                        @php
                                            $statusText = 'Success';
                                            $statusClass = 'success';
                                            if(str_contains($activity->aktivitas, 'Login')) {
                                                $statusText = 'Online';
                                                $statusClass = 'info';
                                            } elseif(str_contains($activity->aktivitas, 'Update')) {
                                                $statusText = 'Updated';
                                                $statusClass = 'warning';
                                            } elseif(str_contains($activity->aktivitas, 'Masuk')) {
                                                $statusText = 'Inbound';
                                                $statusClass = 'primary';
                                            } elseif(str_contains($activity->aktivitas, 'Rusak') || str_contains($activity->aktivitas, 'Hilang')) {
                                                $statusText = 'Alert';
                                                $statusClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-1 rounded-pill">
                                            {{ $statusText }}
                                        </span>
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

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Semester
    const semesterCtx = document.getElementById('semesterChart').getContext('2d');
    new Chart(semesterCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($semesterLabels) !!},
            datasets: [{
                label: 'Jumlah Barang',
                data: {!! json_encode($semesterData) !!},
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0'],
                borderRadius: 8,
                barPercentage: 0.65
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
    
    // Grafik Kondisi Barang - Lingkaran Penuh (PIE)
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
            plugins: {
                legend: { display: false }
            }
        }
    });
    
    // Grafik Barang Masuk
    const barangMasukCtx = document.getElementById('barangMasukChart').getContext('2d');
    new Chart(barangMasukCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($bulanLabels) !!},
            datasets: [{
                label: 'Total Pengadaan',
                data: {!! json_encode($bulanData) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
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
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    
    // Grafik Penggunaan Praktikum
    const praktikumCtx = document.getElementById('praktikumChart').getContext('2d');
    new Chart(praktikumCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($praktikumLabels) !!},
            datasets: [
                {
                    label: 'Digunakan',
                    data: {!! json_encode($praktikumDigunakan) !!},
                    backgroundColor: '#0d6efd',
                    borderRadius: 8,
                    barPercentage: 0.7
                },
                {
                    label: 'Kapasitas',
                    data: {!! json_encode($praktikumKapasitas) !!},
                    backgroundColor: '#e9ecef',
                    borderRadius: 8,
                    barPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
</script>
@endsection