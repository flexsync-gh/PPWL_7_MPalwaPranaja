@extends('layouts.app')

@section('title', 'Target Tabungan - FinTrack')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title mb-1"><i class="bi bi-piggy-bank text-primary me-2"></i>Target Tabungan</h3>
                <p class="text-muted small mb-0">Rencanakan target tabungan, catat setoran atau penarikan, dan pantau progresnya</p>
            </div>
            <a href="{{ route('savings.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Target
            </a>
        </div>
    </div>

    @if($goals->isEmpty())
        <div class="col-12">
            <div class="card app-card">
                <div class="empty-state">
                    <h4 class="h5">Belum ada target tabungan</h4>
                    <p>Buat target tabungan pertama Anda untuk melacak progres pencapaian finansial.</p>
                    <a href="{{ route('savings.create') }}" class="btn btn-primary">Tambah Target</a>
                </div>
            </div>
        </div>
    @else
        @foreach($goals as $goal)
            @php
                $statusClass = $goal->status === 'completed' ? 'status-safe' : 'status-warning';
                $statusLabel = $goal->status === 'completed' ? 'Tercapai' : 'Aktif';
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card app-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $goal->name }}</h5>
                            <p class="text-muted small mb-0">
                                Target: {{ $goal->target_date ? $goal->target_date->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="d-flex justify-content-between gap-2 mb-2 small">
                        <span class="fw-semibold">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                        <span class="text-muted">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress mb-3" role="progressbar" aria-valuenow="{{ (int) $goal->progress_percent }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar {{ $goal->status === 'completed' ? 'status-safe' : '' }}" style="width: {{ $goal->progress_percent }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between gap-2 mb-4 small">
                        <span class="text-muted">Kekurangan</span>
                        <strong>Rp {{ number_format($goal->remaining_amount, 0, ',', '.') }}</strong>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <a href="{{ route('savings.show', $goal->id) }}" class="btn btn-sm btn-primary">Detail</a>
                        <a href="{{ route('savings.edit', $goal->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('savings.destroy', $goal->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus target tabungan ini?')">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
