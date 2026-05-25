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
            overflow-x: hidden;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            white-space: nowrap;
        }

        .navbar-brand img {
            max-height: 35px;
            margin-right: 8px;
        }

        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 1px 0 0 rgba(0, 0, 0, 0.05);
            transition: width 0.3s ease;
            overflow-x: hidden;
            white-space: nowrap;
            flex-shrink: 0;
            height: calc(100vh - 60px);
            position: sticky;
            top: 60px;
        }

        .sidebar-expanded {
            width: 260px;
        }

        .sidebar-collapsed {
            width: 70px;
        }

        .sidebar-collapsed .nav-link span,
        .sidebar-collapsed .nav-section-title span,
        .sidebar-collapsed .nav-section-title i:not(.fa-chevron-right) {
            display: none;
        }

        .sidebar-collapsed .nav-link {
            text-align: center;
            padding: 0.625rem 0;
        }

        .sidebar-collapsed .nav-link i {
            margin-right: 0 !important;
            width: auto;
            font-size: 1.25rem;
        }

        .sidebar-collapsed .nav-section-title {
            text-align: center;
            padding: 0.75rem 0;
        }

        .sidebar-collapsed .nav-section-title i {
            margin-right: 0;
        }

        .sidebar .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 0.75rem 1rem 0.5rem 1rem;
            margin-top: 0.5rem;
            transition: all 0.3s;
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
            margin-right: 0.5rem;
        }

        .main-content {
            background-color: #f5f7fb;
            min-height: calc(100vh - 60px);
            flex-grow: 1;
            overflow-x: auto;
        }

        .app-container {
            display: flex;
            width: 100%;
        }

        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }

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

        .toggle-sidebar-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.25rem;
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .toggle-sidebar-btn:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .semester-dropdown-toggle {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.25rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .semester-dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .semester-dropdown-toggle::after {
            margin-left: 0.5rem;
            vertical-align: middle;
        }

        .semester-dropdown-menu {
            min-width: 220px;
        }

        .dropdown-item.active-semester {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar-btn d-none d-md-block" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                @php
                $labName = \App\Models\Setting::get('lab_name', 'Laboratorium UNY', auth()->user()->id_lab ?? null);
                @endphp
                <a class="navbar-brand ms-2" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo-uny.png') }}" style="max-height: 35px;">
                    <span class="fw-bold">{{ $labName }}</span>
                </a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Dropdown Ganti Semester -->
                    <li class="nav-item dropdown me-2" id="semesterDropdownContainer" style="display: none;">
                        <a class="nav-link semester-dropdown-toggle dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="semesterDropdownToggle">
                            <i class="fas fa-calendar-alt me-1"></i> <span id="currentSemesterLabel">Memuat...</span>
                        </a>
                        <ul class="dropdown-menu semester-dropdown-menu dropdown-menu-end" id="semesterDropdownMenu">
                            <!-- Akan diisi oleh JavaScript -->
                        </ul>
                    </li>

                    <!-- Tombol lonceng (bell) dihapus -->

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->nama ?? 'A', 0, 1) }}
                            </div>
                            <span class="d-none d-lg-inline">{{ Auth::user()->nama ?? 'Admin Lab' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user-circle me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
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

    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar sidebar-expanded" id="sidebar">
            <div class="position-sticky pt-3" style="top: 0;">
                <ul class="nav flex-column">
                    <li class="nav-section-title">
                        <i class="fas fa-compass me-1"></i> <span>Main Navigation</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">
                            <i class="fas fa-boxes me-2"></i> <span>Data Barang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">
                            <i class="fas fa-truck-loading me-2"></i> <span>Barang Masuk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('peminjaman.*') && !request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}" href="{{ route('peminjaman.index') }}">
                            <i class="fas fa-chalkboard me-2"></i><span>Peminjaman Barang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}" href="{{ route('peminjaman.riwayat') }}">
                            <i class="fas fa-history me-2"></i><span>Riwayat Peminjaman</span>
                        </a>
                    </li>
                    <li class="nav-section-title mt-3">
                        <i class="fas fa-chart-simple me-1"></i> <span>Management & Reporting</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stok.*') ? 'active' : '' }}" href="{{ route('stok.index') }}">
                            <i class="fas fa-chart-line me-2"></i> <span>Manajemen Stok</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stok-opname.*') ? 'active' : '' }}" href="{{ route('stok-opname.index') }}">
                            <i class="fas fa-clipboard-check me-2"></i> <span>Stok Opname</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                            <i class="fas fa-file-alt me-2"></i> <span>Laporan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}" href="{{ route('riwayat.aktivitas') }}">
                            <i class="fas fa-history me-2"></i> <span>Riwayat Aktivitas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('semester.*') ? 'active' : '' }}" href="{{ route('semester.daftar') }}">
                            <i class="fas fa-calendar-alt me-2"></i> <span>Manajemen Semester</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                            <i class="fas fa-users me-2"></i> <span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                            <i class="fas fa-sliders-h me-2"></i> <span>Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="main-content">
            <div class="px-4 py-4">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            if (sidebar && toggleBtn) {
                const sidebarState = localStorage.getItem('sidebarCollapsed');
                if (sidebarState === 'true') {
                    sidebar.classList.remove('sidebar-expanded');
                    sidebar.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.add('sidebar-expanded');
                }
                toggleBtn.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) {
                        sidebar.classList.remove('sidebar-expanded');
                        sidebar.classList.add('sidebar-collapsed');
                        localStorage.setItem('sidebarCollapsed', 'true');
                    } else {
                        sidebar.classList.remove('sidebar-collapsed');
                        sidebar.classList.add('sidebar-expanded');
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                    window.dispatchEvent(new Event('resize'));
                });
            }
        });

        async function loadSemesterDropdown() {
            try {
                const response = await fetch('{{ route("semester.list") }}');
                if (!response.ok) throw new Error('Gagal mengambil data semester');
                const semesters = await response.json();
                const currentId = {{ session('active_semester_id', 'null') }};
                const container = document.getElementById('semesterDropdownContainer');
                const toggleLabel = document.getElementById('currentSemesterLabel');
                const menu = document.getElementById('semesterDropdownMenu');

                if (!semesters.length) {
                    container.style.display = 'none';
                    return;
                }

                let activeLabel = '';
                if (currentId === 0) {
                    activeLabel = 'Semua Semester';
                } else {
                    const activeSemester = semesters.find(s => s.id_semester === currentId);
                    if (activeSemester) {
                        activeLabel = `${activeSemester.nama_semester} - ${activeSemester.tahun_ajaran}`;
                    } else {
                        container.style.display = 'none';
                        return;
                    }
                }

                toggleLabel.innerText = activeLabel;
                container.style.display = 'block';

                let html = `
                    <li>
                        <a class="dropdown-item ${currentId === 0 ? 'active active-semester' : ''}" href="#" data-id="0">
                            <i class="fas fa-globe me-2"></i> Semua Semester
                            ${currentId === 0 ? ' <i class="fas fa-check-circle float-end mt-1"></i>' : ''}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                `;
                semesters.forEach(sem => {
                    const isActive = (currentId === sem.id_semester);
                    html += `
                        <li>
                            <a class="dropdown-item ${isActive ? 'active active-semester' : ''}" href="#" data-id="${sem.id_semester}">
                                ${sem.nama_semester} - ${sem.tahun_ajaran}
                                ${isActive ? ' <i class="fas fa-check-circle float-end mt-1"></i>' : ''}
                            </a>
                        </li>
                    `;
                });
                menu.innerHTML = html;

                document.querySelectorAll('#semesterDropdownMenu .dropdown-item').forEach(item => {
                    item.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        if (!id) return;
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const formData = new FormData();
                        formData.append('id_semester', id);
                        const resp = await fetch('{{ route("set-semester") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        if (resp.ok) {
                            window.location.reload();
                        } else {
                            alert('Gagal mengubah semester');
                        }
                    });
                });
            } catch (err) {
                console.error(err);
                const container = document.getElementById('semesterDropdownContainer');
                if (container) container.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', loadSemesterDropdown);

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>

</html>