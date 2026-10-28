@extends('layouts.app')

@section('title', 'Edit Transaksi - FinTrack')

@section('styles')
<!-- datepicker css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Transaksi #{{ $transaction->id }}</h5>
                <span class="badge bg-danger text-uppercase">Role: Admin Only</span>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="transaction_date" class="form-label fw-semibold">Tanggal Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input 
                                type="text" 
                                id="transaction_date" 
                                name="transaction_date" 
                                class="date form-control @error('transaction_date') is-invalid @enderror" 
                                placeholder="yyyy-mm-dd" 
                                value="{{ old('transaction_date', $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : '') }}" 
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categoryid" class="form-label fw-semibold">Kategori</label>
                        <select name="categoryid" id="categoryid" class="form-select @error('categoryid') is-invalid @enderror" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('categoryid', $transaction->categoryid) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ ucfirst($category->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="accountid" class="form-label fw-semibold">Akun Keuangan</label>
                        <select name="accountid" id="accountid" class="form-select @error('accountid') is-invalid @enderror" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('accountid', $transaction->accountid) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Tipe Transaksi</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="Expense" {{ old('type', $transaction->type) == 'Expense' ? 'selected' : '' }}>Expense (Pengeluaran)</option>
                            <option value="Income" {{ old('type', $transaction->type) == 'Income' ? 'selected' : '' }}>Income (Pemasukan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Nominal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input 
                                type="number" 
                                step="any" 
                                id="amount" 
                                name="amount" 
                                class="form-control @error('amount') is-invalid @enderror" 
                                value="{{ old('amount', $transaction->amount) }}" 
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi / Catatan</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="3" 
                            required
                        >{{ old('description', $transaction->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
                        </a>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold text-dark">
                            <i class="bi bi-check-circle me-1"></i> Perbarui Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
@endsection
