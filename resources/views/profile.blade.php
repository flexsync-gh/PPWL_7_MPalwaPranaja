@extends('layouts.app')

@section('title', 'Profil Pengguna - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person-badge me-2 text-primary"></i>Profil Pengguna
                </h5>
                <span class="badge bg-secondary-subtle text-secondary small">
                    Rute: <code>/profile</code> (Protected by <code>auth</code>)
                </span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 72px; height: 72px; font-size: 28px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-3 py-1">
                            Role: {{ $user->role }}
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small mb-1">ID Pengguna</label>
                        <div class="p-2 bg-light rounded fw-semibold">#{{ $user->id }}</div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label text-muted small mb-1">Nomor Telepon</label>
                        <div class="p-2 bg-light rounded fw-semibold">
                            {{ $user->phonenumber ?? 'Belum ditambahkan' }}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label text-muted small mb-1">Hak Akses Sistem</label>
                        <div class="p-2 bg-light rounded fw-semibold">
                            @if($user->role === 'admin')
                                <span class="text-danger"><i class="bi bi-shield-lock-fill me-1"></i> Administrator (Full Access)</span>
                            @else
                                <span class="text-primary"><i class="bi bi-person-check me-1"></i> User Biasa (Read Only Transaksi)</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label text-muted small mb-1">Terdaftar Sejak</label>
                        <div class="p-2 bg-light rounded fw-semibold">
                            {{ $user->created_at ? $user->created_at->format('d F Y, H:i') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4 mb-0 small">
                    <i class="bi bi-info-circle-fill me-1"></i> 
                    <strong>Catatan Praktikum (Latihan 1):</strong> Halaman ini merupakan implementasi Latihan 1 pada modul <code>MIDDLEWARE_RBAC.pdf</code>, di mana rute <code>/profile</code> hanya dapat dibuka oleh pengguna yang telah login. Jika pengguna belum login (guest), sistem akan secara otomatis mengarahkan ke halaman login.
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between py-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
                <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-wallet2 me-1"></i> Buka Transaksi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
