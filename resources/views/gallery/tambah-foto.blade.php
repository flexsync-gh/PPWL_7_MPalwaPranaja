@extends('layouts.app')

@section('title', 'Tambah Infografis Galeri - ' . $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-image-fill text-primary me-2"></i>Tambah Infografis Galeri untuk "{{ $post->title }}"
                </h5>
                <a href="{{ route('fintrack.edukasi.detail', $post->post_seo ?: $post->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Ke Topik Finansial
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('gallery.photo.store', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_galeri" class="form-label fw-medium">Nama Infografis / Gambar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_galeri') is-invalid @enderror" id="nama_galeri" name="nama_galeri" value="{{ old('nama_galeri') }}" placeholder="Contoh: Bagan Alokasi Anggaran 50-30-20" required>
                        <div class="form-text text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Nilai <code>galeri_seo</code> (slug) akan otomatis digenerate dari nama infografis untuk optimasi SEO.
                        </div>
                        @error('nama_galeri')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label fw-medium">Pilih File Gambar Infografis <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*" required>
                        <div class="form-text text-muted small">Format: JPG, JPEG, PNG, WEBP, GIF (Maks. 2MB)</div>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-medium">Keterangan / Deskripsi Infografis</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" placeholder="Jelaskan secara singkat isi infografis atau diagram keuangan ini...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('fintrack.edukasi.detail', $post->post_seo ?: $post->id) }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Infografis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
