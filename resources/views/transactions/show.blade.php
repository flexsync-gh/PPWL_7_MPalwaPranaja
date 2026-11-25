@extends('layouts.app')

@section('title', 'Detail Transaksi - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card app-card">
            <div class="card-header bg-white py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Detail Transaksi #{{ $transaction->id }}</h5>
                    <small class="text-muted">Rincian lengkap catatan arus kas Anda.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Tanggal Transaksi</dt>
                    <dd class="col-sm-8">{{ $transaction->transaction_date ? $transaction->transaction_date->format('d M Y') : '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Tipe &amp; Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $transaction->isIncome() ? 'badge-soft-income' : 'badge-soft-expense' }}">
                            {{ ucfirst(strtolower($transaction->type)) }}
                        </span>
                        <span class="ms-2 small text-muted">{{ $transaction->status_label }}</span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Nominal</dt>
                    <dd class="col-sm-8 fw-bold fs-5 {{ $transaction->isIncome() ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($transaction->amount, 2, ',', '.') }}
                    </dd>

                    <dt class="col-sm-4 text-muted">Akun Keuangan</dt>
                    <dd class="col-sm-8">{{ $transaction->account->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Kategori</dt>
                    <dd class="col-sm-8">{{ $transaction->category->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Deskripsi</dt>
                    <dd class="col-sm-8 mb-0">{{ $transaction->description ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
