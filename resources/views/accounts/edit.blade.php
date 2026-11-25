@extends('layouts.app')

@section('title', 'Edit Akun Keuangan - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Akun Keuangan</h5>
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

                <form method="POST" action="{{ route('accounts.update', $account->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Akun</label>
                        <input type="text" id="name" name="name" maxlength="100" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $account->name) }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Tipe Akun</label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $account->normalized_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="balance" class="form-label fw-semibold">Saldo (Rp)</label>
                        <input type="number" id="balance" name="balance" min="0" step="0.01" class="form-control @error('balance') is-invalid @enderror" value="{{ old('balance', $account->balance) }}" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
