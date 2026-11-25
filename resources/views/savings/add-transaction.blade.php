@extends('layouts.app')

@section('title', 'Transaksi Tabungan - ' . $goal->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin text-primary me-2"></i>Catat Transaksi Tabungan</h5>
                <small class="text-muted">{{ $goal->name }} — Saldo saat ini: Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</small>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('savings.transactions.store', $goal->id) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-semibold">Tipe</label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required autofocus>
                                <option value="deposit" @selected(old('type', 'deposit') === 'deposit')>Setoran (Deposit)</option>
                                <option value="withdraw" @selected(old('type') === 'withdraw')>Penarikan (Withdraw)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold">Nominal (Rp)</label>
                            <input id="amount" type="number" name="amount" min="1" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label fw-semibold">Tanggal</label>
                            <input id="transaction_date" type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label for="note" class="form-label fw-semibold">Catatan (Opsional)</label>
                            <textarea id="note" name="note" rows="3" maxlength="1000" class="form-control @error('note') is-invalid @enderror" placeholder="Catatan tambahan...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('savings.show', $goal->id) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
