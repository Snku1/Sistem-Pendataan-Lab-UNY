<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Lab') - Inventaris Laboratorium</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
        }
        
        /* Navbar Styling */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            white-space: nowrap;
        }
        
        /* Search Bar Styling */
        .search-bar {
            border-radius: 50px;
            border: none;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-bar:focus {
            background-color: rgba(255, 255, 255, 0.25);
            outline: none;
            box-shadow: none;
        }
        
        /* Sidebar Styling */
        .sidebar {
            min-height: calc(100vh - 60px);
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 1px 0 0 rgba(0, 0, 0, 0.05);
        }
        
        .sidebar .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 0.75rem 1rem 0.5rem 1rem;
            margin-top: 0.5rem;
        }
        
        .sidebar .nav-link {
            color: #4a5568;
            padding: 0.625rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: #eef2ff;
            color: #1e40af;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white !important;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25);
        }
        
        .sidebar .nav-link i {
            width: 1.75rem;
            font-size: 1rem;
        }
        
        /* Main Content */
        .main-content {
            background-color: #f5f7fb;
            min-height: calc(100vh - 60px);
        }
        
        /* Card Hover Effect */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* User Avatar */
        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Notification Styling */
        .notification-stok-menipis {
            background-color: #fee2e2;
            border-left: 4px solid #dc3545;
        }
        
        .notification-stok-menipis:hover {
            background-color: #fecaca;
        }
        
        .datetime-badge {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <!-- Logo Kiri -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-microscope me-2"></i>
                <span class="fw-bold">Sistem Lab AV & TV</span>
            </a>
            
            <!-- Search Bar - Posisi Tengah -->
            <div class="flex-grow-1 d-flex justify-content-center mx-3">
                <form action="{{ route('barang.index') }}" method="GET" class="d-flex w-100" style="max-width: 400px;">
                    <div class="position-relative w-100">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-white-50"></i>
                        <input type="text" name="search" class="search-bar ps-5 w-100" placeholder="Cari barang, merk, atau kode..." 
                               value="{{ request('search') }}">
                    </div>
                </form>
            </div>
            
            <!-- Menu Kanan -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-2 d-lg-none">
                        <a class="nav-link text-white-50" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link text-white-50" href="#">
                            <i class="fas fa-bell"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->nama ?? 'A', 0, 1) }}
                            </div>
                            <span class="d-none d-lg-inline">{{ Auth::user()->nama ?? 'Admin Lab' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Modal untuk mobile -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cari Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('barang.index') }}" method="GET">
                        <input type="text" name="search" class="form-control" placeholder="Cari barang, merk, atau kode...">
                        <button type="submit" class="btn btn-primary mt-3 w-100">Cari</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-2 col-lg-2 d-md-block sidebar p-0">
                <div class="position-sticky pt-3" style="top: 60px;">
                    <ul class="nav flex-column">
                        <!-- Main Navigation -->
                        <li class="nav-section-title">
                            <i class="fas fa-compass me-1"></i> Main Navigation
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">
                                <i class="fas fa-boxes me-2"></i>Data Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-import me-2"></i>Pengajuan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">
                                <i class="fas fa-truck-loading me-2"></i>Barang Datang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-people-arrows me-2"></i>Distribusi ke Lab
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chalkboard me-2"></i>Barang Digunakan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-clipboard-list me-2"></i>Monitoring Kondisi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-line me-2"></i>Manajemen Stok
                            </a>
                        </li>
                        
                        <!-- Management & Reporting -->
                        <li class="nav-section-title mt-3">
                            <i class="fas fa-chart-simple me-1"></i> Management & Reporting
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}" href="{{ route('riwayat.aktivitas') }}">
                                <i class="fas fa-history me-2"></i>Riwayat Aktivitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                <i class="fas fa-file-alt me-2"></i>Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users me-2"></i>Manajemen User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-sliders-h me-2"></i>Pengaturan
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 col-lg-10 ms-sm-auto main-content">
                <div class="px-4 py-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (opsional untuk beberapa fitur) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Aktifkan tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Update waktu real-time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            };
            const timeString = now.toLocaleTimeString('id-ID', options);
            const dateString = now.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            const dateTimeElement = document.getElementById('realTimeDateTime');
            if (dateTimeElement) {
                dateTimeElement.innerHTML = `<i class="fas fa-calendar-alt me-1"></i> ${dateString} | <i class="fas fa-clock me-1"></i> ${timeString} WIB`;
            }
        }
        
        // Update setiap detik
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>