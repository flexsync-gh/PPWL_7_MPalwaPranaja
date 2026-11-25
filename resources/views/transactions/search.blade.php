@extends('layouts.app')

@section('title', 'Hasil Pencarian Transaksi - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Title & Action Button -->
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-search text-primary me-2"></i>Hasil Pencarian Transaksi</h3>
                <p class="text-muted small mb-0">
                    @if($cari !== '')
                        Kata kunci pencarian: <span class="badge bg-primary-subtle text-primary fw-semibold fs-6">"{{ $cari }}"</span>
                    @else
                        Menampilkan transaksi sesuai filter yang dipilih.
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                </a>
            </div>
        </div>
    </div>

    @include('transactions._listing', ['isSearch' => true])
</div>
@endsection
