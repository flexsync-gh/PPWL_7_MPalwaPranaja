@extends('layouts.app')

@section('title', 'Gallery Foto - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Header Halaman Galeri -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-images text-primary me-2"></i>Galeri Foto
                </h3>
                <p class="text-muted small mb-0">Menampilkan postingan gambar dan foto portofolio</p>
            </div>
            <a href="{{ route('gallery.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Tambah Foto Baru
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-semibold">Dashboard Galeri</h5>
            </div>
            <div class="card-body p-4">
                @if(count($galleries) > 0)
                    <div class="row g-3">
                        @foreach ($galleries as $gallery)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card h-100 border shadow-none overflow-hidden">
                                    <!-- Lightbox Image Link (sesuai slide praktikum) -->
                                    <a class="example-image-link text-decoration-none" 
                                       href="{{ $gallery->original_pict }}" 
                                       data-lightbox="roadtrip" 
                                       data-title="{{ $gallery->title }} - {{ $gallery->description }}">
                                        <div style="height: 140px; overflow: hidden; background-color: #f1f5f9;" class="d-flex align-items-center justify-content-center">
                                            <img class="example-image img-fluid w-100 h-100 object-fit-cover" 
                                                 src="{{ asset('storage/posts_image/' . $gallery->picture) }}" 
                                                 alt="{{ $gallery->title }}" />
                                        </div>
                                    </a>

                                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="card-title text-truncate mb-1 fw-bold small" title="{{ $gallery->title }}">
                                                {{ $gallery->title }}
                                            </h6>
                                            <p class="card-text text-muted text-truncate mb-2" style="font-size: 0.75rem;" title="{{ $gallery->description }}">
                                                {{ $gallery->description }}
                                            </p>
                                        </div>

                                        <!-- Aksi Edit & Hapus (Tugas Praktikum Slide 11) -->
                                        <div class="d-flex gap-1 pt-2 border-top">
                                            <a href="{{ route('gallery.edit', $gallery->id) }}" class="btn btn-outline-warning btn-sm p-1 flex-fill text-center" title="Edit Data">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('gallery.destroy', $gallery->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-1 w-100 text-center" title="Hapus Foto">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $galleries->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="display-6 text-muted mb-3">
                            <i class="bi bi-image"></i>
                        </div>
                        <h4 class="text-secondary fw-semibold">Tidak ada data.</h4>
                        <p class="text-muted small">Belum ada foto yang diupload ke dalam galeri.</p>
                        <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Upload Foto Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
