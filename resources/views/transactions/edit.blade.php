<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
    <style>
        body { font-family: sans-serif; padding: 24px; color: #333; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h4 { margin-top: 0; font-size: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 8px 16px; font-size: 14px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-warning { background-color: #f59e0b; color: #fff; }
        .btn-secondary { background-color: #6b7280; color: #fff; margin-left: 8px; }
    </style>
</head>
<body>

<div class="container">
    <h4>Edit Transaksi</h4>
    <form method="post" action="{{ route('transactions.update', $transaction->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Tanggal Transaksi</label>
            <input type="date" name="transaction_date" value="{{ $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : '' }}" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="categoryid" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $transaction->categoryid == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Akun</label>
            <select name="accountid" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ $transaction->accountid == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Tipe</label>
            <select name="type" required>
                <option value="Expense" {{ $transaction->type == 'Expense' ? 'selected' : '' }}>Expense (Pengeluaran)</option>
                <option value="Income" {{ $transaction->type == 'Income' ? 'selected' : '' }}>Income (Pemasukan)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nominal (Rp)</label>
            <input type="number" step="any" name="amount" value="{{ $transaction->amount }}" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <input type="text" name="description" value="{{ $transaction->description }}" required>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ '/transactions' }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
