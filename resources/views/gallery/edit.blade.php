@extends('layouts.app')

@section('title', 'Edit Foto Galeri - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Foto Galeri
                </h5>
                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('gallery.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-3 row">
                        <label for="title" class="col-md-4 col-form-label text-md-end text-start fw-semibold">Title</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->title) }}" placeholder="Masukkan judul foto">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3 row">
                        <label for="description" class="col-md-4 col-form-label text-md-end text-start fw-semibold">Description</label>
                        <div class="col-md-7">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="5" name="description" placeholder="Masukkan deskripsi foto">{{ old('description', $post->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Foto Saat Ini -->
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label text-md-end text-start fw-semibold">Foto Saat Ini</label>
                        <div class="col-md-7">
                            <div class="border rounded p-2 d-inline-block bg-light">
                                <img src="{{ asset('storage/posts_image/' . $post->picture) }}" 
                                     alt="{{ $post->title }}" 
                                     class="img-thumbnail" 
                                     style="max-height: 160px; object-fit: cover;">
                            </div>
                            <div class="small text-muted mt-1">{{ $post->picture }}</div>
                        </div>
                    </div>

                    <!-- Ganti File Foto (Opsional) -->
                    <div class="mb-4 row">
                        <label for="input-file" class="col-md-4 col-form-label text-md-end text-start fw-semibold">Ganti Foto (Opsional)</label>
                        <div class="col-md-7">
                            <input type="file" class="form-control @error('picture') is-invalid @enderror" id="input-file" name="picture" accept="image/*">
                            <div class="form-text text-muted small">Biarkan kosong jika tidak ingin mengubah foto. Format: JPEG, PNG, JPG (Maks. 2MB)</div>
                            @error('picture')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Update -->
                    <div class="row">
                        <div class="col-md-7 offset-md-4">
                            <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Update Data
                            </button>
                            <a href="{{ route('gallery.index') }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
