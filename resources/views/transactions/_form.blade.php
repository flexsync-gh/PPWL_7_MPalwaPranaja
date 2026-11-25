@php
    $selectedType = strtolower((string) old('type', $transaction->type ?? 'expense'));
    $selectedAccount = old('accountid', $transaction->accountid ?? '');
    $selectedCategory = old('categoryid', $transaction->categoryid ?? '');
    $selectedDate = old('transaction_date', isset($transaction) && $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : date('Y-m-d'));
    $selectedAmount = old('amount', $transaction->amount ?? '');
    $selectedDescription = old('description', $transaction->description ?? '');
@endphp

@if($accounts->isEmpty() || $categories->isEmpty())
    <div class="alert alert-warning small">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        Anda memerlukan minimal 1 akun keuangan dan 1 kategori sebelum mencatat transaksi.
        <div class="mt-2 d-flex gap-2">
            @if($accounts->isEmpty())
                <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-warning">Buat Akun</a>
            @endif
            @if($categories->isEmpty())
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-warning">Buat Kategori</a>
            @endif
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger py-2 small mb-4">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf
    @if(($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

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
                value="{{ $selectedDate }}"
                required
            >
        </div>
    </div>

    <div class="mb-3">
        <label for="type" class="form-label fw-semibold">Tipe Transaksi</label>
        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="expense" @selected($selectedType === 'expense')>Expense (Pengeluaran)</option>
            <option value="income" @selected($selectedType === 'income')>Income (Pemasukan)</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="accountid" class="form-label fw-semibold">Akun Keuangan</label>
        <select name="accountid" id="accountid" class="form-select @error('accountid') is-invalid @enderror" required>
            <option value="">-- Pilih Akun --</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" @selected((string) $selectedAccount === (string) $account->id)>
                    {{ $account->name }} (Saldo: Rp {{ number_format($account->balance, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="categoryid" class="form-label fw-semibold">Kategori</label>
        <select name="categoryid" id="categoryid" class="form-select @error('categoryid') is-invalid @enderror" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $category)
                <option
                    data-type="{{ strtolower($category->type) }}"
                    value="{{ $category->id }}"
                    @selected((string) $selectedCategory === (string) $category->id)
                >
                    {{ $category->name }} ({{ ucfirst(strtolower($category->type)) }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="amount" class="form-label fw-semibold">Nominal (Rp)</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input
                type="number"
                step="any"
                min="0.01"
                id="amount"
                name="amount"
                class="form-control @error('amount') is-invalid @enderror"
                placeholder="Contoh: 50000"
                value="{{ $selectedAmount }}"
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
            placeholder="Deskripsi transaksi..."
            required
        >{{ $selectedDescription }}</textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
        </a>
        <button type="submit" class="btn {{ ($method ?? 'POST') === 'PUT' ? 'btn-warning text-dark' : 'btn-primary' }} px-4 fw-semibold">
            <i class="bi {{ ($method ?? 'POST') === 'PUT' ? 'bi-check-circle' : 'bi-save' }} me-1"></i>
            {{ $submitLabel ?? 'Simpan Transaksi' }}
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('categoryid');
    if (!typeSelect || !categorySelect) {
        return;
    }

    function syncCategories() {
        const selectedType = typeSelect.value.toLowerCase();
        let hasVisibleSelected = false;

        Array.from(categorySelect.options).forEach(function (option) {
            if (!option.value) {
                return;
            }
            const visible = option.dataset.type === selectedType;
            option.hidden = !visible;
            option.disabled = !visible;
            if (visible && option.selected) {
                hasVisibleSelected = true;
            }
        });

        if (!hasVisibleSelected) {
            categorySelect.value = '';
        }
    }

    typeSelect.addEventListener('change', syncCategories);
    syncCategories();
});
</script>
