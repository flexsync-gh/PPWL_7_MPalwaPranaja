@extends('layouts.app')

@section('title', 'Akun Keuangan - FinTrack')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title mb-1"><i class="bi bi-bank text-primary me-2"></i>Akun Keuangan</h3>
                <p class="text-muted small mb-0">{{ $accountCount }} / {{ $limit }} akun digunakan</p>
            </div>
            @if($accountCount >= $limit)
                <button type="button" class="btn btn-primary" disabled>Batas Akun Tercapai</button>
            @else
                <a href="{{ route('accounts.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Akun
                </a>
            @endif
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="summary-card summary-balance p-4">
            <div class="small text-uppercase fw-semibold opacity-75">Total Saldo Semua Akun</div>
            <div class="h3 fw-bold mb-0 mt-2">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-12">
        <div class="card app-card overflow-hidden">
            @if($accounts->isEmpty())
                <div class="empty-state">
                    <h4 class="h5">Belum ada akun keuangan</h4>
                    <p>Tambahkan akun (Dompet, Bank, atau E-Wallet) untuk mulai mencatat transaksi.</p>
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary">Tambah Akun</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Akun</th>
                                <th>Tipe</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-end">Jumlah Transaksi</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td class="fw-semibold">{{ $account->name }}</td>
                                    <td>
                                        <span class="badge account-type-badge account-type-{{ $account->normalized_type }}">
                                            {{ $account->type_label }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $account->transactions_count }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus akun {{ $account->name }}?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
