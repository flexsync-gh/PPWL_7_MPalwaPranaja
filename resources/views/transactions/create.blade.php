<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi</title>
    <style>
        body { font-family: sans-serif; padding: 24px; color: #333; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h4 { margin-top: 0; font-size: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 8px 16px; font-size: 14px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-primary { background-color: #2563eb; color: #fff; }
        .btn-secondary { background-color: #6b7280; color: #fff; margin-left: 8px; }
    </style>
</head>
<body>

<div class="container">
    <h4>Tambah Transaksi</h4>
    <form method="post" action="{{ route('transactions.store') }}">
        @csrf
        <div class="form-group">
            <label>Tanggal Transaksi</label>
            <input type="date" name="transaction_date" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="categoryid" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Akun</label>
            <select name="accountid" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Tipe</label>
            <select name="type" required>
                <option value="Expense">Expense (Pengeluaran)</option>
                <option value="Income">Income (Pemasukan)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nominal (Rp)</label>
            <input type="number" step="any" name="amount" placeholder="Contoh: 50000" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <input type="text" name="description" placeholder="Deskripsi transaksi..." required>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ '/transactions' }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
