@extends('layouts.app')

@section('title', 'Daftar Transaksi - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Title & Action Button -->
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>Daftar Transaksi Keuangan</h3>
                <p class="text-muted small mb-0">Kelola pemasukan dan pengeluaran pribadi Anda dengan sinkronisasi saldo akun otomatis</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-bank me-1"></i> Kelola Akun
                </a>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                </a>
            </div>
        </div>
    </div>

    @include('transactions._listing', ['isSearch' => false])
</div>
@endsection
