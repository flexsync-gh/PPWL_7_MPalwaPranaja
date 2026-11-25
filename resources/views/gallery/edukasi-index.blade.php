@extends('layouts.app')

@section('title', 'Edukasi & Wawasan Finansial - FinTrack SEO')
@section('meta_description', 'Kumpulan panduan literasi keuangan, tips manajemen arus kas, budgeting gaji, dan infografis finansial terbaik dari FinTrack.')
@section('meta_keywords', 'edukasi keuangan, tips finansial, manajemen arus kas, budgeting 50 30 20, dana darurat, pembukuan pribadi, fintrack')
@section('canonical_url', route('fintrack.edukasi'))
@section('og_title', 'Edukasi & Wawasan Finansial - FinTrack')
@section('og_description', 'Pelajari tips mengatur keuangan pribadi, alokasi gaji, dan pantau infografis finansial lengkap di FinTrack.')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <!-- Hero Header SEO -->
        <div class="bg-primary text-white rounded-4 p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden">
            <div class="position-relative" style="z-index: 2;">
                <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-graph-up-arrow me-1"></i> Literasi Keuangan FinTrack
                </span>
                <h1 class="display-5 fw-bold mb-3">Edukasi & Tips Manajemen Keuangan</h1>
                <p class="lead mb-0 text-white-50 col-lg-9">
                    Kumpulan panduan praktis, strategi alokasi anggaran, dan infografis finansial untuk membantu Anda mengelola arus kas dan mencapai kebebasan finansial.
                </p>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-journal-richtext text-primary me-2"></i>Daftar Topik Finansial
                </h4>
                <p class="text-muted small mb-0">Klik pada topik untuk melihat detail penjelasan lengkap dan galeri infografis</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Topik Finansial
                </a>
                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-grid-fill me-1"></i> Galeri Foto
                </a>
            </div>
        </div>

        @if(count($posts) > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($posts as $post)
                    <div class="col">
                        <div class="card h-100 border shadow-sm rounded-3 overflow-hidden d-flex flex-column justify-content-between">
                            <!-- Gambar Sampul -->
                            <div class="position-relative bg-light" style="height: 200px; overflow: hidden;">
                                <img src="{{ $post->original_pict }}" 
                                     alt="{{ $post->title }}" 
                                     class="w-100 h-100 object-fit-cover">
                                <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $post->category ?? 'Tips Finansial' }}
                                </span>
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-2">
                                    <a href="{{ route('fintrack.edukasi.detail', $post->post_seo ?: $post->id) }}" class="text-decoration-none text-dark hover-primary">
                                        {{ $post->title }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted small flex-grow-1 mb-3">
                                    {{ Str::limit($post->description, 110) }}
                                </p>

                                <!-- SEO Friendly Slug Badge -->
                                <div class="mb-3">
                                    <span class="badge bg-light text-secondary border small text-truncate d-inline-block mw-100" title="SEO URL: /edukasi-keuangan/{{ $post->post_seo ?: $post->id }}">
                                        <i class="bi bi-link-45deg text-success me-1"></i>/edukasi-keuangan/{{ $post->post_seo ?: $post->id }}
                                    </span>
                                </div>

                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $post->created_at->format('d M Y') }}
                                    </span>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('fintrack.edukasi.detail', $post->post_seo ?: $post->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i> Baca & Infografis
                                        </a>
                                        <a href="{{ route('gallery.photo.create', $post->id) }}" class="btn btn-light btn-sm border" title="Upload Infografis">
                                            <i class="bi bi-upload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="card shadow-sm border-0 p-5 text-center">
                <div class="display-5 text-muted mb-3"><i class="bi bi-journal-x"></i></div>
                <h4 class="fw-semibold text-secondary">Belum ada topik edukasi finansial</h4>
                <p class="text-muted small">Mulai tambahkan tips dan wawasan keuangan pertama Anda dengan gambar pendukung.</p>
                <div class="mt-2">
                    <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Buat Konten Finansial
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
