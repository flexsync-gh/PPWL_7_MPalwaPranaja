@extends('layouts.app')

@section('title', 'Edit Budget - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Budget Bulanan</h5>
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

                <form method="POST" action="{{ route('budgets.update', $budget->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="categoryid" class="form-label fw-semibold">Kategori Pengeluaran</label>
                            <select id="categoryid" name="categoryid" class="form-select @error('categoryid') is-invalid @enderror" required autofocus>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('categoryid', $budget->categoryid) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold">Batas Budget (Rp)</label>
                            <input id="amount" type="number" name="amount" min="1" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $budget->amount) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="month" class="form-label fw-semibold">Bulan</label>
                            <select id="month" name="month" class="form-select @error('month') is-invalid @enderror" required>
                                @foreach($monthOptions as $value => $label)
                                    <option value="{{ $value }}" @selected((int) old('month', $budget->month) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label fw-semibold">Tahun</label>
                            <input id="year" type="number" name="year" min="2000" max="2100" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $budget->year) }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
