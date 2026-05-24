<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Sistem Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        /* Navbar */
        .navbar {
            background: #191970 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 0.75rem 1.5rem;
            height: 64px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.3px;
            color: white !important;
        }

        .navbar-brand i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 64px;
            left: 0;
            width: 270px;
            height: calc(100vh - 64px);
            background: white;
            border-right: 1px solid #e9edf2;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.02);
        }

        .sidebar .nav {
            padding: 1.25rem 1rem;
        }

        .sidebar .nav-link {
            color: #334155;
            padding: 0.7rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link i {
            width: 22px;
            font-size: 1.1rem;
            color: #5b6e8c;
        }

        .sidebar .nav-link:hover {
            background-color: #f1f5f9;
            color: #191970;
        }

        .sidebar .nav-link:hover i {
            color: #191970;
        }

        .sidebar .nav-link.active {
            background-color: #eef2ff;
            color: #191970;
            font-weight: 600;
        }

        .sidebar .nav-link.active i {
            color: #191970;
        }

        /* Main Content */
        .main-content {
            margin-left: 270px;
            padding: 28px 32px;
            margin-top: 64px;
            min-height: calc(100vh - 64px);
            transition: margin-left 0.3s;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        /* Toggle Button */
        .sidebar-toggle {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.3rem;
            margin-right: 12px;
            padding: 6px 8px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Logout Button */
        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            border-radius: 40px;
            padding: 6px 18px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #dc2626;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle d-md-none" id="sidebarToggleBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-microscope"></i> Panel Admin
                </a>
            </div>
            <div>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.laboratorium.*') ? 'active' : '' }}" href="{{ route('admin.laboratorium.index') }}">
                <i class="fas fa-flask"></i> Manajemen Laboratorium
            </a>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> Manajemen User
            </a>
            <a class="nav-link {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}" href="{{ route('admin.lokasi.index') }}">
                <i class="fas fa-map-marker-alt"></i> Manajemen Lokasi
            </a>
            <a class="nav-link {{ request()->routeIs('admin.penanggung-jawab.*') ? 'active' : '' }}" href="{{ route('admin.penanggung-jawab.index') }}">
                <i class="fas fa-user-tie"></i> Manajemen Penanggung Jawab
            </a>
            <a class="nav-link {{ request()->routeIs('admin.settings.lab') ? 'active' : '' }}" href="{{ route('admin.settings.lab') }}">
                <i class="fas fa-building"></i> Pengaturan Nama Lab
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && e.target !== toggleBtn && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>

</html>