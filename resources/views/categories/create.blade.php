@extends('layouts.app')

@section('title', 'Tambah Kategori - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-tags text-primary me-2"></i>Tambah Kategori</h5>
                <span class="badge bg-primary-subtle text-primary">{{ $categoryCount }} / {{ $limit }}</span>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($categoryCount >= $limit)
                    <div class="alert alert-warning">Anda telah mencapai batas maksimal {{ $limit }} kategori.</div>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Kembali ke Kategori</a>
                @else
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" id="name" name="name" maxlength="100" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Makanan, Gaji, Investasi" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="type" class="form-label fw-semibold">Tipe Kategori</label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', 'expense') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">Simpan Kategori</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
