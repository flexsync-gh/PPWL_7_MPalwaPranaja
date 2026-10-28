@extends('layouts.app')

@section('title', 'Selamat Datang - FinTrack')

@section('content')
<div class="row align-items-center py-5">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
            🚀 Praktikum Pemrograman Web - Auth & RBAC
        </span>
        <h1 class="display-4 fw-bold mb-3 lh-sm">
            Sistem Catatan & Manajemen <span class="text-primary">Transaksi Keuangan</span>
        </h1>
        <p class="lead text-muted mb-4">
            Aplikasi web dengan fitur otentikasi (Register-Login), proteksi rute dengan Middleware, dan Role-Based Access Control (RBAC) antara Administrator dan User biasa.
        </p>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4 fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> Buat Akun Baru
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                    <i class="bi bi-speedometer2 me-1"></i> Buka Dashboard
                </a>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-lg px-4 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i> Lihat Transaksi
                </a>
            @endguest
        </div>

        <div class="d-flex align-items-center gap-3 text-muted small">
            <div><i class="bi bi-shield-check text-success fs-5 me-1"></i> Laravel 11/12+ Ready</div>
            <div><i class="bi bi-person-lock text-primary fs-5 me-1"></i> Simple RBAC</div>
            <div><i class="bi bi-bootstrap text-purple fs-5 me-1"></i> Bootstrap 5</div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-lg p-2 bg-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Struktur Akses Peran (RBAC)</h5>
                
                <div class="card mb-3 border-danger border-start border-4 shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-danger mb-0"><i class="bi bi-shield-lock-fill me-1"></i> Peran: Admin</h6>
                            <span class="badge bg-danger">Full Control</span>
                        </div>
                        <p class="small text-muted mb-2">
                            Memiliki akses penuh untuk melihat, menambah (create), mengedit (update), dan menghapus (delete) catatan transaksi keuangan, serta mengakses Admin Panel (<code>/admin</code>).
                        </p>
                        <span class="badge bg-white text-dark border small">Middleware: ['auth', 'isAdmin']</span>
                    </div>
                </div>

                <div class="card mb-3 border-primary border-start border-4 shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-fill me-1"></i> Peran: User Biasa</h6>
                            <span class="badge bg-primary">Read-Only</span>
                        </div>
                        <p class="small text-muted mb-2">
                            Dapat melihat daftar transaksi (<code>/transactions</code>), melakukan pencarian & filter, serta mengakses profil diri (<code>/profile</code>) dan dashboard personal.
                        </p>
                        <span class="badge bg-white text-dark border small">Middleware: ['auth']</span>
                    </div>
                </div>

                <div class="card border-secondary border-start border-4 shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-incognito me-1"></i> Pengunjung (Guest)</h6>
                            <span class="badge bg-secondary">Tamu</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Hanya dapat mengakses halaman publik (Landing Page, Form Login, Form Registrasi). Akses ke rute internal akan otomatis dialihkan ke halaman login.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
