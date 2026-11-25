@extends('layouts.app')

@section('title', 'Tambah Target Tabungan - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-piggy-bank text-primary me-2"></i>Tambah Target Tabungan</h5>
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

                <form method="POST" action="{{ route('savings.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Target</label>
                            <input id="name" type="text" name="name" maxlength="100" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Dana Darurat, Laptop Baru" required autofocus>
                        </div>
                        <div class="col-md-6">
                            <label for="target_date" class="form-label fw-semibold">Tanggal Target</label>
                            <input id="target_date" type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror" value="{{ old('target_date', date('Y-m-d', strtotime('+6 months'))) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="target_amount" class="form-label fw-semibold">Target Nominal (Rp)</label>
                            <input id="target_amount" type="number" name="target_amount" min="1" step="0.01" class="form-control @error('target_amount') is-invalid @enderror" value="{{ old('target_amount') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="current_amount" class="form-label fw-semibold">Saldo Awal Terkumpul (Rp)</label>
                            <input id="current_amount" type="number" name="current_amount" min="0" step="0.01" class="form-control @error('current_amount') is-invalid @enderror" value="{{ old('current_amount', 0) }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Simpan Target</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
