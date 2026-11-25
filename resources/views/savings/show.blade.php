@extends('layouts.app')

@section('title', $goal->name . ' - Target Tabungan')

@section('content')
@php
    $statusClass = $goal->status === 'completed' ? 'status-safe' : 'status-warning';
    $statusLabel = $goal->status === 'completed' ? 'Tercapai' : 'Aktif';
@endphp
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title mb-1">{{ $goal->name }}</h3>
                <p class="text-muted small mb-0">
                    Tanggal target: {{ $goal->target_date ? $goal->target_date->format('d M Y') : '-' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <a href="{{ route('savings.transactions.create', $goal->id) }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Catat Setoran / Penarikan
                </a>
                <a href="{{ route('savings.edit', $goal->id) }}" class="btn btn-outline-warning text-dark">Edit</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <p class="text-muted small mb-1">Progres Tabungan</p>
                    <h4 class="fw-bold mb-0">
                        Rp {{ number_format($goal->current_amount, 0, ',', '.') }} / Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                    </h4>
                </div>
                <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            <div class="progress mb-3" role="progressbar" aria-valuenow="{{ (int) $goal->progress_percent }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar {{ $goal->status === 'completed' ? 'status-safe' : '' }}" style="width: {{ $goal->progress_percent }}%"></div>
            </div>

            <div class="d-flex justify-content-between small">
                <div>
                    <span class="text-muted">Kekurangan:</span>
                    <strong>Rp {{ number_format($goal->remaining_amount, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <span class="text-muted">Persentase:</span>
                    <strong>{{ number_format($goal->progress_percent, 0) }}%</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="summary-card summary-savings p-4 h-100">
            <p class="mb-1 opacity-75 small text-uppercase fw-semibold">Jumlah Riwayat Transaksi</p>
            <div class="h2 fw-bold mb-0">{{ $transactions->count() }}</div>
        </div>
    </div>

    <div class="col-12">
        <div class="card app-card overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Riwayat Setoran &amp; Penarikan</h5>
            </div>
            @if($transactions->isEmpty())
                <div class="empty-state">
                    <h4 class="h5">Belum ada transaksi tabungan</h4>
                    <p>Catat setoran atau penarikan pertama untuk target ini.</p>
                    <a href="{{ route('savings.transactions.create', $goal->id) }}" class="btn btn-primary">Catat Transaksi Tabungan</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Catatan</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trx)
                                <tr>
                                    <td>{{ $trx->transaction_date ? $trx->transaction_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="status-badge {{ $trx->type === 'deposit' ? 'status-safe' : 'status-over' }}">
                                            {{ $trx->type === 'deposit' ? 'Setoran (Deposit)' : 'Penarikan (Withdraw)' }}
                                        </span>
                                    </td>
                                    <td>{{ $trx->note ?: '-' }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('savings.transactions.destroy', $trx->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus transaksi tabungan ini? Saldo tabungan akan disesuaikan kembali.')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
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
