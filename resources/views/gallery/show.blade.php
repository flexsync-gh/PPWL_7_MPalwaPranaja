@extends('layouts.app')

@section('title', $post->title . ' - Detail Galeri')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">{{ $post->title }}</h5>
                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Galeri
                </a>
            </div>
            <div class="card-body p-4 text-center">
                <a href="{{ $post->original_pict }}" data-lightbox="roadtrip" data-title="{{ $post->title }} - {{ $post->description }}">
                    <img src="{{ asset('storage/posts_image/' . $post->picture) }}" 
                         alt="{{ $post->title }}" 
                         class="img-fluid rounded shadow-sm mb-3" 
                         style="max-height: 400px;">
                </a>
                <p class="text-muted text-start mt-3">{{ $post->description }}</p>
                <div class="text-start text-secondary small">
                    <i class="bi bi-clock me-1"></i> Diupload pada: {{ $post->created_at->format('d M Y H:i') }}
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-end gap-2">
                <a href="{{ route('gallery.edit', $post->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <form action="{{ route('gallery.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
