@extends('layouts.app')

@section('title', 'Dashboard - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Card -->
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge bg-light text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                            Role: {{ $user->role }}
                        </span>
                        <h2 class="fw-bold mb-1">Selamat Datang, {{ $user->name }}! 👋</h2>
                        <p class="mb-0 opacity-75">
                            Anda berhasil masuk ke sistem catatan keuangan FinTrack. Halaman ini diproteksi oleh <code>middleware('auth')</code>.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('profile') }}" class="btn btn-light btn-sm px-3 fw-semibold text-primary">
                            <i class="bi bi-person-fill me-1"></i> Profil Saya
                        </a>
                        @if($user->role === 'admin')
                            <a href="{{ route('admin.index') }}" class="btn btn-warning btn-sm px-3 fw-semibold">
                                <i class="bi bi-shield-fill-check me-1"></i> Halaman Admin
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Total Transaksi</h6>
                    <h3 class="fw-bold mb-0">{{ $total_transaksi }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Total Nominal</h6>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($total_nominal, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Kategori Aktif</h6>
                    <h3 class="fw-bold mb-0">{{ $total_kategori }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>5 Transaksi Terkini</h5>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_transactions as $trx)
                                <tr>
                                    <td>{{ $trx->transaction_date ? $trx->transaction_date->format('d/m/Y') : '-' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $trx->category->name ?? '-' }}</span></td>
                                    <td>{{ $trx->description }}</td>
                                    <td>
                                        <span class="badge {{ $trx->type == 'income' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($trx->type) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Akses & Hak Peran (RBAC Info) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Hak Akses Anda (RBAC)</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} fs-6 px-3 py-2 me-2">
                        {{ strtoupper($user->role) }}
                    </span>
                    <span class="text-muted small">Status Peran Akun</span>
                </div>

                <ul class="list-group list-group-flush small mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Akses Halaman Dashboard</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Akses Profil Pribadi (/profile)</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Melihat & Mencari Transaksi</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Akses Admin Panel (/admin)</span>
                        @if($user->role === 'admin')
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Menambah & Mengubah Data Transaksi</span>
                        @if($user->role === 'admin')
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        @endif
                    </li>
                </ul>

                @if($user->role !== 'admin')
                    <div class="alert alert-warning p-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Akun Anda berstatus <strong>User</strong>. Untuk mencoba fitur Admin, login menggunakan akun Admin atau ubah role melalui Admin Panel.
                    </div>
                @else
                    <div class="alert alert-success p-2 small mb-0">
                        <i class="bi bi-stars me-1"></i> Akun Anda berstatus <strong>Admin</strong> dengan hak akses penuh ke seluruh rute khusus Admin.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
