<!-- Filter Form Pencarian Lanjutan (FinTrack + Finote) -->
<div class="col-12">
    <div class="card app-card">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('transactions.search') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold" for="kata">Cari Deskripsi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input
                            type="text"
                            id="kata"
                            name="kata"
                            class="form-control"
                            placeholder="Contoh: Makan, Gaji, Transport..."
                            value="{{ $cari }}"
                        >
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold" for="account_id">Akun Keuangan</label>
                    <select id="account_id" name="account_id" class="form-select">
                        <option value="0">Semua Akun</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @selected((int) request('account_id') === $account->id)>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold" for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="semua">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }} ({{ ucfirst(strtolower($category->type)) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold" for="type">Tipe</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="income" @selected(strtolower((string) request('type')) === 'income')>Income (Pemasukan)</option>
                        <option value="expense" @selected(strtolower((string) request('type')) === 'expense')>Expense (Pengeluaran)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold" for="date_from">Dari Tanggal</label>
                    <input id="date_from" type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold" for="date_to">Sampai Tanggal</label>
                    <input id="date_to" type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="col-12">
    <div class="card app-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Akun</th>
                            <th>Kategori</th>
                            <th>Tipe &amp; Status</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center" style="width: 210px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            @php
                                $desc = $trx->description ?: '-';
                                $highlightedDesc = e($desc);
                                if ($cari !== '') {
                                    $highlightedDesc = preg_replace('/(' . preg_quote(e($cari), '/') . ')/i', '<mark>$1</mark>', $highlightedDesc);
                                }
                            @endphp
                            <tr>
                                <td>{{ ++$no }}</td>
                                <td>{{ $trx->transaction_date ? $trx->transaction_date->format('d/m/Y') : '-' }}</td>
                                <td class="fw-semibold transaction-description">{!! $highlightedDesc !!}</td>
                                <td>{{ $trx->account->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ $trx->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $trx->isIncome() ? 'badge-soft-income' : 'badge-soft-expense' }}">
                                        {{ ucfirst(strtolower($trx->type)) }}
                                    </span>
                                    <div class="small text-muted">{{ $trx->status_label }}</div>
                                </td>
                                <td class="text-end fw-bold {{ $trx->isIncome() ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($trx->amount, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('transactions.show', $trx->id) }}" class="btn btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.edit', $trx->id) }}" class="btn btn-outline-warning text-dark" title="Edit Transaksi">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Yakin ingin menghapus transaksi ini? Saldo akun akan disesuaikan otomatis.')" type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Transaksi">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-danger fw-semibold">
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
                @if($isSearch ?? false)
                    <div><strong>Ditemukan {{ $transactions->total() }} data transaksi</strong></div>
                    <span>Menampilkan {{ $transactions->count() }} hasil pencarian</span>
                @else
                    <div><strong>Jumlah Transaksi: {{ $total_transaksi }}</strong></div>
                    <span>Menampilkan {{ $transactions->count() }} dari {{ $total_transaksi }} total transaksi</span>
                @endif
            </div>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Statistik Transaksi -->
<div class="col-md-7 col-lg-6">
    <div class="card app-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Statistik Transaksi Anda</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Jumlah Transaksi:</span>
                    <span class="fw-bold">{{ $total_transaksi }} transaksi</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Pemasukan (Income):</span>
                    <span class="fw-bold text-success">Rp {{ number_format($total_income, 2, ',', '.') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Pengeluaran (Expense):</span>
                    <span class="fw-bold text-danger">Rp {{ number_format($total_expense, 2, ',', '.') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Saldo Bersih Arus Kas:</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($saldo_bersih, 2, ',', '.') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Nominal Semua Transaksi:</span>
                    <span class="fw-bold">Rp {{ number_format($total_nominal, 2, ',', '.') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Transaksi Nominal Tertinggi:</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($nominal_tertinggi, 2, ',', '.') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Transaksi Nominal Terendah:</span>
                    <span class="fw-bold text-secondary">Rp {{ number_format($nominal_terendah, 2, ',', '.') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
