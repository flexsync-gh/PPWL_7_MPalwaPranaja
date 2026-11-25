@extends('layouts.app')

@section('title', 'Kategori Transaksi - FinTrack')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title mb-1"><i class="bi bi-tags text-primary me-2"></i>Kategori Transaksi</h3>
                <p class="text-muted small mb-0">{{ $categoryCount }} / {{ $limit }} kategori digunakan</p>
            </div>
            @if($categoryCount >= $limit)
                <button type="button" class="btn btn-primary" disabled>Batas Kategori Tercapai</button>
            @else
                <a href="{{ route('categories.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
                </a>
            @endif
        </div>
    </div>

    <div class="col-12">
        <div class="card app-card overflow-hidden">
            @if($categories->isEmpty())
                <div class="empty-state">
                    <h4 class="h5">Belum ada kategori</h4>
                    <p>Tambahkan kategori pemasukan dan pengeluaran agar pencatatan transaksi lebih rapi.</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Tipe</th>
                                <th class="text-end">Jumlah Transaksi</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td class="fw-semibold">{{ $category->name }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($category->type) === 'income' ? 'badge-soft-income' : 'badge-soft-expense' }}">
                                            {{ ucfirst(strtolower($category->type)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $category->transactions_count }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')">
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
