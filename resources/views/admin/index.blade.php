@extends('layouts.app')

@section('title', 'Admin Panel - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Admin Banner -->
    <div class="col-12">
        <div class="card bg-danger text-white border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-white text-danger fw-bold text-uppercase px-3 py-1">
                                <i class="bi bi-shield-lock-fill me-1"></i> Admin Area
                            </span>
                            <span class="badge bg-black bg-opacity-25 text-white">
                                Middleware: ['auth', 'isAdmin']
                            </span>
                        </div>
                        <h2 class="fw-bold mb-1">Halo Admin, {{ Auth::user()->name }}! 🛡️</h2>
                        <p class="mb-0 opacity-75">
                            Ini adalah halaman khusus Administrator sesuai instruksi <strong>MIDDLEWARE_RBAC.pdf (Langkah 6 & Latihan 2)</strong>. Pengguna dengan role biasa (user) akan ditolak jika mencoba mengakses URL ini.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('transactions.create') }}" class="btn btn-light text-danger fw-bold btn-sm px-3 shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Pengguna & Transaksi -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Total User</h6>
                    <h3 class="fw-bold mb-0">{{ $total_users }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-4">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Jumlah Admin</h6>
                    <h3 class="fw-bold mb-0">{{ $total_admins }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">User Biasa</h6>
                    <h3 class="fw-bold mb-0">{{ $total_reg_users }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Total Dana Tercatat</h6>
                    <h4 class="fw-bold mb-0 text-truncate">Rp {{ number_format($total_nominal, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Manajemen User & Role (RBAC Simulator) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-gear me-2 text-primary"></i>Daftar Pengguna & Manajemen Role (RBAC)
                    </h5>
                    <small class="text-muted">Admin dapat menguji pergantian role pengguna secara langsung melalui tombol aksi.</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Role Saat Ini</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-center">Aksi Ubah Role (Uji RBAC)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $u)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">
                                        {{ $u->name }}
                                        @if($u->id === Auth::id())
                                            <span class="badge bg-secondary-subtle text-secondary ms-1">Akun Anda</span>
                                        @endif
                                    </td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->phonenumber ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-2 py-1">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td>{{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        @if($u->id === Auth::id())
                                            <span class="text-muted small">Sedang aktif</span>
                                        @else
                                            <form action="{{ route('admin.users.updateRole', $u->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @if($u->role === 'admin')
                                                    <input type="hidden" name="role" value="user">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Ubah role {{ $u->name }} menjadi User?')">
                                                        <i class="bi bi-arrow-down-circle me-1"></i> Turunkan ke User
                                                    </button>
                                                @else
                                                    <input type="hidden" name="role" value="admin">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Jadikan {{ $u->name }} sebagai Admin?')">
                                                        <i class="bi bi-arrow-up-circle me-1"></i> Jadikan Admin
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada user di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
