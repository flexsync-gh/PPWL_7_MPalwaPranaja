<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi</title>
    <style>
        body { font-family: sans-serif; padding: 24px; color: #333; }
        .filter-box { display: flex; gap: 12px; margin-bottom: 16px; align-items: center; }
        select, input, button { padding: 8px 12px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .stat-card { border: 1px solid #d1d5db; background-color: #f9fafb; border-radius: 8px; padding: 16px; width: 350px; margin-top: 24px; }
        .stat-card p { margin: 8px 0; }
        .empty-row { text-align: center; color: #dc2626; font-weight: bold; }
        .btn { display: inline-block; padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-primary { background-color: #2563eb; color: #fff; }
        .btn-warning { background-color: #f59e0b; color: #fff; }
        .btn-danger { background-color: #dc2626; color: #fff; }
        .float-end { float: right; }
    </style>
</head>
<body>

    <div style="margin-bottom: 20px;">
        <!-- Tombol Create Transaksi -->
        <a href="{{ route('transactions.create') }}" class="btn btn-primary float-end">Tambah Transaksi</a>
        <h2 style="margin: 0;">Daftar Transaksi</h2>
    </div>

    <!-- Form Search & Dropdown Kategori -->
    <form action="{{ route('transactions.index') }}" method="GET" class="filter-box">
        <!-- Dropdown Kategori -->
        <select name="category_id" onchange="this.form.submit()">
            <option value="semua">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <!-- Input Pencarian Deskripsi & Tombol Cari -->
        <input type="text" name="cari" placeholder="Cari deskripsi..." value="{{ request('cari') }}">
        <button type="submit">Cari</button>
        <a href="{{ route('transactions.index') }}">Reset</a>
    </form>

    <!-- Tabel Data Transaksi -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Tipe</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trx->transaction_date ? $trx->transaction_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $trx->category->name ?? '-' }}</td>
                    <td>{{ $trx->description }}</td>
                    <td>{{ $trx->type }}</td>
                    <td>{{ "Rp " . number_format($trx->amount, 2, ',', '.') }}</td>
                    <td style="white-space: nowrap;">
                        <!-- Tombol Update (Edit) -->
                        <a href="{{ route('transactions.edit', $trx->id) }}" class="btn btn-warning">Edit</a>

                        <!-- Tombol Delete -->
                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin mau dihapus?')" type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <!-- Pesan data tidak ditemukan -->
                <tr>
                    <td colspan="7" class="empty-row">Data transaksi tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Kotak Statistik Transaksi -->
    <div class="stat-card">
        <h3>Statistik Transaksi</h3>
        <p><strong>Total Transaksi:</strong> {{ $total_transaksi }}</p>
        <p><strong>Total Nominal Semua Transaksi:</strong> {{ "Rp " . number_format($total_nominal, 2, ',', '.') }}</p>
        <p><strong>Transaksi Nominal Tertinggi:</strong> {{ "Rp " . number_format($nominal_tertinggi, 2, ',', '.') }}</p>
        <p><strong>Transaksi Nominal Terendah:</strong> {{ "Rp " . number_format($nominal_terendah, 2, ',', '.') }}</p>
    </div>

</body>
</html>