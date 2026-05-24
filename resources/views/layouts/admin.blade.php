<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Sistem Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Inter', sans-serif;
        }

        .navbar.bg-primary {
            background-color: #191970 !important;
        }

        .sidebar {
            background: white;
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 56px;
            left: 0;
            box-shadow: 1px 0 0 rgba(0, 0, 0, 0.05);
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #4a5568;
            padding: 0.625rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background-color: #eef2ff;
            color: #191970;
        }

        .sidebar .nav-link.active {
            background-color: #191970;
            color: white !important;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            margin-top: 56px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-md-none" id="sidebarToggleBtn"><i class="fas fa-bars"></i></button>
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Panel Admin</a>
            <div class="ms-auto">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light me-2">Aplikasi Utama</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.laboratorium.*') ? 'active' : '' }}" href="{{ route('admin.laboratorium.index') }}">
                        <i class="fas fa-flask me-2"></i> Manajemen Laboratorium
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users me-2"></i> Manajemen User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}" href="{{ route('admin.lokasi.index') }}">
                        <i class="fas fa-map-marker-alt me-2"></i> Manajemen Lokasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.penanggung-jawab.*') ? 'active' : '' }}" href="{{ route('admin.penanggung-jawab.index') }}">
                        <i class="fas fa-user-tie me-2"></i> Manajemen Penanggung Jawab
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.lab') ? 'active' : '' }}" href="{{ route('admin.settings.lab') }}">
                        <i class="fas fa-building me-2"></i> Pengaturan Nama Lab
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggleBtn')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
</body>

</html>