@extends('layouts.app')

@section('title', 'FinTrack - Catatan Transaksi & Manajemen Keuangan Pribadi')
@section('meta_description', 'FinTrack adalah platform manajemen keuangan pribadi: catat transaksi, kelola multi-akun, atur budget bulanan, capai target tabungan, dan baca panduan edukasi finansial.')
@section('meta_keywords', 'fintrack, finote, catatan keuangan, manajemen transaksi, pembukuan, arus kas, budgeting, tabungan, tips keuangan')
@section('canonical_url', url('/'))

@section('content')
<div class="row align-items-center py-4 py-lg-5 g-4">
    <div class="col-lg-6">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
            🚀 FinTrack &times; Finote — Personal Finance Workspace
        </span>
        <h1 class="display-4 fw-bold mb-3 lh-sm">
            Kelola <span class="text-primary">Transaksi, Budget &amp; Tabungan</span> Anda Secara Mandiri
        </h1>
        <p class="lead text-muted mb-4">
            Gabungan lengkap fitur manajemen keuangan pribadi (multi-akun, kategori kustom, pencatatan transaksi, budget bulanan, target tabungan, dark mode) dengan keamanan Auth &amp; RBAC serta Edukasi Finansial SEO.
        </p>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4 fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> Buat Akun Baru
                </a>
                <a href="{{ route('fintrack.edukasi') }}" class="btn btn-light btn-lg px-3 fw-semibold border">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Edukasi Finansial (SEO)
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                    <i class="bi bi-speedometer2 me-1"></i> Buka Dashboard
                </a>
                <a href="{{ route('transactions.create') }}" class="btn btn-success btn-lg px-4 fw-semibold shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
                </a>
                <a href="{{ route('budgets.index') }}" class="btn btn-outline-primary btn-lg px-3 fw-semibold">
                    <i class="bi bi-pie-chart me-1"></i> Budget
                </a>
                <a href="{{ route('fintrack.edukasi') }}" class="btn btn-light btn-lg px-3 fw-semibold border">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Edukasi Finansial
                </a>
            @endguest
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
            <div><i class="bi bi-check-circle-fill text-success me-1"></i> Akses Penuh Keuangan untuk User</div>
            <div><i class="bi bi-shield-lock-fill text-primary me-1"></i> Auth &amp; RBAC Admin Panel</div>
            <div><i class="bi bi-moon-stars-fill text-warning me-1"></i> Dark Mode</div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-lg p-2">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-stars text-primary me-2"></i>Fitur Unggulan Terintegrasi</h5>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="fw-bold text-primary mb-1"><i class="bi bi-wallet2 me-1"></i> Transaksi &amp; Multi-Akun</div>
                            <p class="small text-muted mb-0">User bebas mencatat pemasukan/pengeluaran, mengelola dompet/bank/e-wallet dengan sinkronisasi saldo otomatis.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="fw-bold text-success mb-1"><i class="bi bi-pie-chart-fill me-1"></i> Budget &amp; Tabungan</div>
                            <p class="small text-muted mb-0">Tetapkan batas budget bulanan per kategori dan pantau progres target tabungan Anda secara real-time.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="fw-bold text-warning mb-1"><i class="bi bi-lightbulb-fill me-1"></i> Edukasi &amp; Galeri SEO</div>
                            <p class="small text-muted mb-0">Artikel literasi finansial ramah mesin pencari lengkap dengan slug SEO, Lightbox2, dan Sitemap XML.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="fw-bold text-danger mb-1"><i class="bi bi-shield-lock-fill me-1"></i> Admin RBAC &amp; Profil</div>
                            <p class="small text-muted mb-0">Kustomisasi foto/tema profil pengguna serta Admin Panel khusus untuk manajemen role pengguna.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-primary border-start border-4 shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-unlock-fill me-1"></i> Hak Akses User vs Admin</h6>
                            <span class="badge bg-primary">Unlocked for Users</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Semua pengguna terdaftar dapat mengelola <strong>Akun, Kategori, Transaksi, Budget, Target Tabungan, &amp; Profil</strong> secara mandiri. Role <strong>Admin</strong> memiliki akses tambahan ke <code>/admin</code> untuk pengelolaan pengguna &amp; role.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
