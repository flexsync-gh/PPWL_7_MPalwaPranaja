@extends('layouts.app')

@section('title', 'Daftar Transaksi - FinTrack')

@section('content')
<div class="row g-4">
    <!-- Header Title & Action Button -->
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>Daftar Transaksi Keuangan</h3>
                <p class="text-muted small mb-0">Kelola dan pantau seluruh catatan arus kas transaksi masuk dan keluar</p>
            </div>
            <div>
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                    </a>
                @else
                    <span class="badge bg-secondary-subtle text-secondary p-2 border">
                        <i class="bi bi-eye me-1"></i> Mode View (User Biasa - Tambah/Ubah khusus Admin)
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Form Kategori & Search -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('transactions.search') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="semua">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ ucfirst($category->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="kata" class="form-control" placeholder="Cari deskripsi transaksi..." value="{{ request('kata') ?? request('cari') }}">
                            <button type="submit" class="btn btn-primary px-3">Cari</button>
                        </div>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th class="text-end">Nominal</th>
                                @if(Auth::check() && Auth::user()->role === 'admin')
                                    <th class="text-center" style="width: 160px;">Aksi (Admin)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{ $trx->transaction_date ? $trx->transaction_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            {{ $trx->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">{{ $trx->description }}</td>
                                    <td>
                                        <span class="badge {{ $trx->type == 'income' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($trx->type) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $trx->type == 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ "Rp " . number_format($trx->amount, 2, ',', '.') }}
                                    </td>
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                        <td class="text-center">
                                            <a href="{{ route('transactions.edit', $trx->id) }}" class="btn btn-warning btn-sm me-1" title="Edit Transaksi">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Yakin ingin menghapus transaksi ini?')" type="submit" class="btn btn-danger btn-sm" title="Hapus Transaksi">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (Auth::check() && Auth::user()->role === 'admin') ? 7 : 6 }}" class="text-center py-4 text-danger fw-semibold">
                                        Data transaksi tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
                <div class="small text-muted mb-2 mb-md-0">
                    <div><strong>Jumlah Transaksi: {{ $total_transaksi }}</strong></div>
                    <span>Menampilkan {{ $transactions->count() }} dari {{ $total_transaksi }} total transaksi</span>
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Transaksi -->
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Statistik Transaksi</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Total Jumlah Transaksi:</span>
                        <span class="fw-bold">{{ $total_transaksi }} transaksi</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Total Nominal Semua Transaksi:</span>
                        <span class="fw-bold text-success">{{ "Rp " . number_format($total_nominal, 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Transaksi Nominal Tertinggi:</span>
                        <span class="fw-bold text-primary">{{ "Rp " . number_format($nominal_tertinggi, 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Transaksi Nominal Terendah:</span>
                        <span class="fw-bold text-secondary">{{ "Rp " . number_format($nominal_terendah, 2, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
