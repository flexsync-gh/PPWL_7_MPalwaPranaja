<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinTrack - Catatan Transaksi & Manajemen Keuangan')</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'FinTrack adalah aplikasi modern untuk mencatat transaksi keuangan pribadi, memantau pengeluaran, anggaran bulanan, target tabungan, serta edukasi dan tips finansial.')">
    <meta name="keywords" content="@yield('meta_keywords', 'fintrack, catatan keuangan, manajemen anggaran, pencatatan transaksi, budget bulanan, tabungan, tips finansial, edukasi keuangan')">
    <meta name="author" content="FinTrack Team">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Open Graph / Facebook / WhatsApp SEO -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'FinTrack - Catatan Transaksi Keuangan')">
    <meta property="og:description" content="@yield('og_description', 'FinTrack adalah aplikasi modern untuk mencatat transaksi keuangan pribadi, budget bulanan, dan target tabungan.')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:site_name" content="FinTrack">
    <meta property="og:image" content="@yield('og_image', asset('storage/posts_image/noimage.png'))">

    <!-- Twitter Card SEO -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'FinTrack - Catatan Transaksi Keuangan')">
    <meta name="twitter:description" content="@yield('og_description', 'FinTrack adalah aplikasi modern untuk mencatat transaksi keuangan pribadi.')">
    <meta name="twitter:image" content="@yield('og_image', asset('storage/posts_image/noimage.png'))">

    <!-- Schema.org JSON-LD Structured Data for Financial Web Application -->
    <script type="application/ld+json">
    {!! json_encode([
        '@' . 'context' => 'https://schema.org',
        '@' . 'type' => 'WebApplication',
        'name' => 'FinTrack',
        'url' => url('/'),
        'applicationCategory' => 'FinanceApplication',
        'operatingSystem' => 'All',
        'description' => 'Aplikasi manajemen transaksi dan catatan keuangan pribadi modern dengan fitur pencatatan, akun, kategori, anggaran bulanan, tabungan, dan edukasi finansial.',
        'offers' => [
            '@' . 'type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'IDR',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @yield('schema_json')

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Unified FinTrack + Finote Styles -->
    <link href="{{ asset('css/fintrack.css') }}" rel="stylesheet">
    <!-- Lightbox2 CSS (Bab X Praktikum) -->
    <link href="{{ asset('dist/css/lightbox.min.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body class="app-body">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="fs-4">📊</span>
                <span>FinTrack</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                            <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active fw-bold' : '' }}" href="{{ route('accounts.index') }}">
                                <i class="bi bi-bank me-1"></i> Akun
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active fw-bold' : '' }}" href="{{ route('categories.index') }}">
                                <i class="bi bi-tags me-1"></i> Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active fw-bold' : '' }}" href="{{ route('transactions.index') }}">
                                <i class="bi bi-wallet2 me-1"></i> Transaksi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active fw-bold' : '' }}" href="{{ route('budgets.index') }}">
                                <i class="bi bi-pie-chart me-1"></i> Budget
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('savings.*') ? 'active fw-bold' : '' }}" href="{{ route('savings.index') }}">
                                <i class="bi bi-piggy-bank me-1"></i> Tabungan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('fintrack.edukasi*') ? 'active fw-bold text-warning' : '' }}" href="{{ route('fintrack.edukasi') }}">
                                <i class="bi bi-lightbulb me-1"></i> Edukasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active fw-bold' : '' }}" href="{{ route('gallery.index') }}">
                                <i class="bi bi-images me-1"></i> Gallery
                            </a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-warning {{ request()->routeIs('admin.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('admin.index') }}">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('fintrack.edukasi*') ? 'active fw-bold text-warning' : '' }}" href="{{ route('fintrack.edukasi') }}">
                                <i class="bi bi-lightbulb me-1"></i> Edukasi Finansial
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active fw-bold' : '' }}" href="{{ route('gallery.index') }}">
                                <i class="bi bi-images me-1"></i> Gallery
                            </a>
                        </li>
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    <li class="nav-item d-flex align-items-center me-1">
                        <button id="appDarkToggle" class="app-theme-toggle" type="button" aria-label="Toggle dark mode" data-theme-toggle>
                            <span class="toggle-icon">&#9728;</span>
                            <span class="toggle-icon">&#9790;</span>
                        </button>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active fw-bold' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning text-dark fw-semibold btn-sm px-3" href="{{ route('register') }}">
                                <i class="bi bi-person-plus-fill me-1"></i> Register
                            </a>
                        </li>
                    @else
                        @if(Auth::user()->isAdmin())
                            @php
                                $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
                                $recentAdminNotifications = Auth::user()->notifications()->latest()->take(5)->get();
                            @endphp
                            <li class="nav-item dropdown me-1">
                                <a class="nav-link position-relative text-light px-2" href="#" id="adminNotificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi Admin">
                                    <i class="bi bi-bell-fill fs-5"></i>
                                    @if($unreadNotificationsCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" data-testid="admin-unread-badge" style="font-size: 0.65rem;">
                                            {{ $unreadNotificationsCount }}
                                            <span class="visually-hidden">notifikasi belum dibaca</span>
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 320px; max-width: 380px;">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center gap-2">
                                        <span class="fw-bold text-dark">Notifikasi ({{ $unreadNotificationsCount }})</span>
                                        @if($unreadNotificationsCount > 0)
                                            <form action="{{ route('admin.notifications.markAllRead') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold">
                                                    Tandai semua dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    @forelse($recentAdminNotifications as $notif)
                                        <li>
                                            <div class="dropdown-item-text py-2 px-3 {{ is_null($notif->read_at) ? 'bg-light fw-semibold' : 'text-muted' }}" style="white-space: normal;">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="small">
                                                        {{ $notif->data['message'] ?? 'Notifikasi baru' }}
                                                    </div>
                                                    @if(is_null($notif->read_at))
                                                        <span class="badge bg-primary rounded-pill flex-shrink-0">Baru</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <small class="text-muted">{{ $notif->created_at?->diffForHumans() }}</small>
                                                    @if(is_null($notif->read_at))
                                                        <form action="{{ route('admin.notifications.read', $notif->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none small">Tandai dibaca</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                        @if(! $loop->last)
                                            <li><hr class="dropdown-divider my-1"></li>
                                        @endif
                                    @empty
                                        <li class="px-3 py-3 text-center text-muted small">
                                            Belum ada notifikasi.
                                        </li>
                                    @endforelse
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item d-flex align-items-center me-1">
                            <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-danger' : 'bg-light text-primary' }} text-uppercase px-2 py-1">
                                {{ Auth::user()->role }}
                            </span>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <x-user-avatar :user="Auth::user()" :size="28" :fontSize="12" />
                                <span>{{ Auth::user()->name }}</span>
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
                                <li>
                                    <a class="dropdown-item" href="{{ route('accounts.index') }}">
                                        <i class="bi bi-bank me-2"></i> Akun Keuangan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('budgets.index') }}">
                                        <i class="bi bi-pie-chart me-2"></i> Budget Bulanan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('savings.index') }}">
                                        <i class="bi bi-piggy-bank me-2"></i> Target Tabungan
                                    </a>
                                </li>
                                @if(Auth::user()->role === 'admin')
                                    <li><hr class="dropdown-divider"></li>
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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
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
    <footer class="app-footer text-center text-muted small">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0">&copy; {{ date('Y') }} FinTrack &times; Finote — Manajemen Keuangan Pribadi, Budgeting, &amp; Edukasi Finansial</p>
            <div class="d-flex gap-3">
                <a href="{{ route('fintrack.edukasi') }}" class="text-decoration-none text-muted">Edukasi Finansial</a>
                <a href="{{ route('gallery.index') }}" class="text-decoration-none text-muted">Gallery</a>
                <a href="{{ route('fintrack.sitemap') }}" class="text-decoration-none text-muted">Sitemap XML</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lightbox2 JS (includes jQuery - Sesuai Bab X Praktikum) -->
    <script src="{{ asset('dist/js/lightbox-plus-jquery.min.js') }}"></script>
    <!-- Dark Mode Theme Toggle (from Finote) -->
    <script src="{{ asset('js/theme.js') }}"></script>
    @yield('scripts')
</body>
</html>
