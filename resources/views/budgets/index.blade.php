@extends('layouts.app')

@section('title', 'Budget Bulanan - FinTrack')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title mb-1"><i class="bi bi-pie-chart text-primary me-2"></i>Budget Bulanan</h3>
                <p class="text-muted small mb-0">Tetapkan batas pengeluaran per kategori dan pantau pemakaiannya setiap bulan</p>
            </div>
            <a href="{{ route('budgets.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Budget
            </a>
        </div>
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="col-12">
        <div class="card app-card">
            <div class="card-body p-3 p-md-4">
                <form method="GET" action="{{ route('budgets.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold" for="month">Bulan</label>
                        <select id="month" name="month" class="form-select">
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}" @selected($month === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold" for="year">Tahun</label>
                        <input id="year" type="number" name="year" min="2000" max="2100" class="form-control" value="{{ $year }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">Filter</button>
                        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card summary-budget p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Total Budget</p>
            <div class="h4 fw-bold mb-0">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card summary-expense p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Total Terpakai</p>
            <div class="h4 fw-bold mb-0">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card summary-balance p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Sisa Budget</p>
            <div class="h4 fw-bold mb-0">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card summary-warning p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Melebihi Budget</p>
            <div class="h4 fw-bold mb-0">{{ $overBudgetCount }}</div>
        </div>
    </div>

    <!-- Tabel Budget -->
    <div class="col-12">
        <div class="card app-card overflow-hidden">
            @if($budgetItems->isEmpty())
                <div class="empty-state">
                    <h4 class="h5">Belum ada budget untuk {{ $monthOptions[$month] ?? $month }} {{ $year }}</h4>
                    <p>Buat anggaran bulanan untuk kategori pengeluaran Anda agar belanja lebih terkontrol.</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">Tambah Budget</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Budget</th>
                                <th class="text-end">Terpakai</th>
                                <th class="text-end">Sisa</th>
                                <th>Pemakaian</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgetItems as $item)
                                @php($b = $item['budget'])
                                <tr>
                                    <td class="fw-semibold">{{ $b->category->name ?? '-' }}</td>
                                    <td class="text-end">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item['spent'], 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold {{ $item['remaining'] < 0 ? 'text-danger' : '' }}">
                                        Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                                    </td>
                                    <td style="min-width: 190px;">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>{{ number_format($item['usage'], 0) }}%</span>
                                            <span>{{ $monthOptions[$b->month] ?? $b->month }} {{ $b->year }}</span>
                                        </div>
                                        <div class="progress" role="progressbar" aria-valuenow="{{ (int) $item['barWidth'] }}" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar {{ $item['statusClass'] }}" style="width: {{ $item['barWidth'] }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $item['statusClass'] }}">{{ $item['status'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('budgets.edit', $b->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('budgets.destroy', $b->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus budget ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
