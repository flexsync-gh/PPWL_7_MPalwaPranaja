@extends('layouts.app')

@section('title', 'Dashboard - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="card summary-card summary-balance border-0">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <x-user-avatar :user="$user" :size="64" class="border border-2 border-white" />
                        <div>
                            <span class="badge bg-light text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                                Role: {{ $user->role }}
                            </span>
                            <h2 class="fw-bold mb-1">Selamat Datang, {{ $user->name }}! 👋</h2>
                            <p class="mb-0 opacity-75">
                                Ringkasan keuangan pribadi Anda: akun, transaksi, budget bulanan, dan target tabungan dalam satu tempat.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('transactions.create') }}" class="btn btn-light btn-sm px-3 fw-semibold text-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                        </a>
                        <a href="{{ route('budgets.create') }}" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                            <i class="bi bi-pie-chart me-1"></i> Atur Budget
                        </a>
                        <a href="{{ route('profile') }}" class="btn btn-warning btn-sm px-3 fw-semibold text-dark">
                            <i class="bi bi-person-fill me-1"></i> Profil Saya
                        </a>
                        @if($user->role === 'admin')
                            <a href="{{ route('admin.index') }}" class="btn btn-danger btn-sm px-3 fw-semibold border border-white">
                                <i class="bi bi-shield-fill-check me-1"></i> Admin Panel
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gradient Summary Cards (Finote Style) -->
    <div class="col-md-4">
        <div class="summary-card summary-income p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Total Pemasukan (Income)</p>
            <h3 class="fw-bold mb-0">Rp {{ number_format($total_income, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card summary-expense p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Total Pengeluaran (Expense)</p>
            <h3 class="fw-bold mb-0">Rp {{ number_format($total_expense, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card summary-balance p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Total Saldo Akun</p>
            <h3 class="fw-bold mb-0">Rp {{ number_format($total_balance, 0, ',', '.') }}</h3>
            <small class="opacity-75">Arus kas bersih: Rp {{ number_format($saldo_bersih, 0, ',', '.') }}</small>
        </div>
    </div>

    <!-- Mini Cards: Transaksi, Akun/Kategori, Tabungan, Budget Bulan Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="card app-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Transaksi Anda</div>
                    <h4 class="fw-bold mb-0">{{ $total_transaksi }}</h4>
                    <small class="text-muted">{{ $total_akun }} akun &bull; {{ $total_kategori }} kategori</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card app-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Tersimpan</div>
                    <h4 class="fw-bold mb-0">Rp {{ number_format($total_saved, 0, ',', '.') }}</h4>
                    <small class="text-muted">{{ $active_goals }} target tabungan aktif</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-6">
        <div class="card app-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Penggunaan Budget Bulan Ini</span>
                        <div class="small text-muted">
                            Rp {{ number_format($monthly_budget_spent, 0, ',', '.') }} / Rp {{ number_format($monthly_budget_total, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="fw-bold fs-5">{{ number_format($monthly_budget_percent, 0) }}%</span>
                </div>
                @php
                    $budgetBarClass = $monthly_budget_percent >= 100 ? 'status-over' : ($monthly_budget_percent >= 80 ? 'status-warning' : 'status-safe');
                @endphp
                <div class="progress" role="progressbar" aria-valuenow="{{ (int) $monthly_budget_percent }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar {{ $budgetBarClass }}" style="width: {{ $monthly_budget_percent }}%"></div>
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center small">
                    <a href="{{ route('budgets.index') }}" class="text-decoration-none fw-semibold">Lihat detail budget &rarr;</a>
                    <a href="{{ route('savings.index') }}" class="text-decoration-none fw-semibold">Lihat tabungan &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="col-lg-8">
        <div class="card app-card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>5 Transaksi Terkini</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </a>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Akun</th>
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
                                    <td>{{ $trx->account->name ?? '-' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $trx->category->name ?? '-' }}</span></td>
                                    <td>
                                        <a href="{{ route('transactions.show', $trx->id) }}" class="text-decoration-none fw-semibold">
                                            {{ $trx->description }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $trx->isIncome() ? 'badge-soft-income' : 'badge-soft-expense' }}">
                                            {{ ucfirst(strtolower($trx->type)) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold {{ $trx->isIncome() ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada data transaksi. <a href="{{ route('transactions.create') }}" class="fw-semibold">Tambah transaksi pertama Anda</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Tabungan & Hak Akses -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <div class="card app-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-piggy-bank me-2 text-primary"></i>Target Tabungan</h5>
                <a href="{{ route('savings.index') }}" class="btn btn-outline-primary btn-sm">Kelola</a>
            </div>
            <div class="card-body">
                @forelse($saving_goals as $goal)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <a href="{{ route('savings.show', $goal->id) }}" class="text-decoration-none">{{ $goal->name }}</a>
                            <span>{{ number_format($goal->progress_percent, 0) }}%</span>
                        </div>
                        <div class="progress mb-1">
                            <div class="progress-bar {{ $goal->status === 'completed' ? 'status-safe' : '' }}" style="width: {{ $goal->progress_percent }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                            <span>Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-2">Belum ada target tabungan.</p>
                    <a href="{{ route('savings.create') }}" class="btn btn-sm btn-primary">Buat Target Tabungan</a>
                @endforelse
            </div>
        </div>

        <div class="card app-card">
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
                        <span>Kelola Akun &amp; Kategori Pribadi</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Tambah, Edit &amp; Hapus Transaksi</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Atur Budget Bulanan &amp; Tabungan</span>
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
                </ul>

                <div class="alert {{ $user->role === 'admin' ? 'alert-success' : 'alert-info' }} p-2 small mb-0">
                    @if($user->role === 'admin')
                        <i class="bi bi-stars me-1"></i> Anda login sebagai <strong>Admin</strong> dengan akses penuh fitur keuangan pribadi + Admin Panel.
                    @else
                        <i class="bi bi-unlock me-1"></i> Semua fitur keuangan pribadi (<strong>Transaksi, Akun, Kategori, Budget, Tabungan</strong>) kini aktif penuh untuk Anda!
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
