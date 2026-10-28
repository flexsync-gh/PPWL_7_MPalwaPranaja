<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinTrack - Catatan Transaksi Keuangan')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .main-content {
            flex: 1;
            padding: 24px 0;
        }
        .card {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
        }
        footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 16px 0;
            margin-top: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="fs-4">📊</span>
                <span>FinTrack</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active fw-bold' : '' }}" href="{{ route('transactions.index') }}">
                                <i class="bi bi-wallet2 me-1"></i> Transaksi
                            </a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-warning {{ request()->routeIs('admin.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('admin.index') }}">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Admin Panel
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active fw-bold' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">
                                <i class="bi bi-person-plus-fill me-1"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item d-flex align-items-center me-2">
                            <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-2 py-1">
                                {{ Auth::user()->role }}
                            </span>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <h6 class="dropdown-header">Login sebagai: {{ Auth::user()->email }}</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="bi bi-person me-2"></i> Profil Saya
                                    </a>
                                </li>
                                @if(Auth::user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('admin.index') }}">
                                            <i class="bi bi-shield-shaded me-2"></i> Admin Panel
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Alert Notifikasi Flash -->
            @if(session('pesan'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('pesan') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-muted small">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} FinTrack - Praktikum Pemrograman Web (Auth, Middleware & RBAC)</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
