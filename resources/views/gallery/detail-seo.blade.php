@extends('layouts.app')

@section('title', $post->title . ' - FinTrack SEO')
@section('meta_description', Str::limit(strip_tags($post->description), 155))
@section('meta_keywords', 'fintrack, ' . strtolower($post->category) . ', ' . str_replace('-', ' ', $post->post_seo) . ', manajemen keuangan, tips finansial')
@section('canonical_url', route('fintrack.edukasi.detail', $post->post_seo ?: $post->id))
@section('og_type', 'article')
@section('og_title', $post->title . ' - Edukasi Keuangan FinTrack')
@section('og_description', Str::limit(strip_tags($post->description), 155))
@section('og_image', $post->original_pict)

@section('schema_json')
<!-- Schema.org Article Structured Data for Search Engine Rich Snippets -->
<script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@' . 'type' => 'Article',
    'headline' => $post->title,
    'description' => Str::limit(strip_tags($post->description), 155),
    'image' => $post->original_pict,
    'datePublished' => $post->created_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@' . 'type' => 'Organization',
        'name' => 'FinTrack Financial Team',
    ],
    'publisher' => [
        '@' . 'type' => 'Organization',
        'name' => 'FinTrack',
        'logo' => [
            '@' . 'type' => 'ImageObject',
            'url' => asset('storage/posts_image/noimage.png'),
        ],
    ],
    'mainEntityOfPage' => [
        '@' . 'type' => 'WebPage',
        '@' . 'id' => route('fintrack.edukasi.detail', $post->post_seo ?: $post->id),
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Breadcrumbs & Navigasi -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('fintrack.edukasi') }}">Edukasi Keuangan</a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 300px;">
                    {{ $post->title }}
                </li>
            </ol>
        </nav>

        <!-- Informasi SEO URL Badge & Tombol Aksi -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Clean SEO URL: <code>/edukasi-keuangan/{{ $post->post_seo ?: $post->id }}</code>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gallery.photo.create', $post->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Infografis Galeri
                </a>
                <a href="{{ route('gallery.edit', $post->id) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit Topik
                </a>
                <a href="{{ route('fintrack.edukasi') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Banner & Konten Utama Topik Finansial -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-3">
                    <a href="{{ $post->original_pict }}" data-lightbox="sampul-utama" data-title="{{ $post->title }}">
                        <img src="{{ $post->original_pict }}" 
                             alt="{{ $post->title }}" 
                             class="img-fluid rounded shadow-sm object-fit-cover" 
                             style="max-height: 280px; width: 100%;">
                    </a>
                </div>
                <div class="col-md-7 d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary">{{ $post->category ?? 'Tips Finansial' }}</span>
                            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ $post->created_at->format('d M Y') }}</span>
                        </div>
                        <h2 class="fw-bold text-dark mb-3">{{ $post->title }}</h2>
                        <p class="text-secondary leading-relaxed mb-0" style="white-space: pre-line;">{{ $post->description }}</p>
                    </div>
                    <div class="mt-3 pt-3 border-top text-muted small">
                        <i class="bi bi-share me-1"></i> Bagikan link topik ini yang sudah terindeks SEO dan ramah mesin pencari.
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Album Galeri Infografis (Sesuai Struktur Praktikum Bab X) -->
        <section id="album" class="py-4 text-center bg-light rounded-4 shadow-sm p-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="fw-bold text-dark mb-0">Galeri Infografis & Bukti Visual</h3>
                    <span class="badge bg-secondary">{{ $galeris->total() }} Foto/Diagram</span>
                </div>
                <p class="text-muted small text-start mb-3">
                    Klik gambar untuk memperbesar dengan fitur Lightbox2. Galeri ini mendukung visualisasi topik <strong>{{ $post->title }}</strong>.
                </p>
                <hr class="mb-4">

                <div class="row">
                    @forelse ($galeris as $data)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border p-2 bg-white">
                            <!-- Link Lightbox (Sesuai Bab X Panduan Praktikum) -->
                            <a href="{{ asset('storage/posts_image/' . $data->foto) }}" 
                               data-lightbox="album-keuangan" 
                               data-title="{{ $data->keterangan ?? $data->nama_galeri }}">
                                <img src="{{ asset('storage/posts_image/' . $data->foto) }}" 
                                     class="rounded object-fit-cover shadow-sm" 
                                     style="width:200px; height:150px" 
                                     alt="{{ $data->nama_galeri }}">
                                <p class="mt-2 mb-0"><h5>{{ $data->nama_galeri }}</h5></p>
                            </a>
                            @if($data->keterangan)
                                <p class="text-muted small mb-1 mt-1">{{ Str::limit($data->keterangan, 60) }}</p>
                            @endif
                            @if($data->galeri_seo)
                                <small class="text-secondary">
                                    <i class="bi bi-tag-fill me-1"></i><code>{{ $data->galeri_seo }}</code>
                                </small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-12 py-5">
                        <div class="text-muted mb-3"><i class="bi bi-images fs-1"></i></div>
                        <h5 class="text-secondary fw-semibold">Belum Ada Infografis di Galeri Ini</h5>
                        <p class="text-muted small">Tambahkan diagram, bagan anggaran, atau bukti visual untuk melengkapi edukasi finansial ini.</p>
                        <a href="{{ route('gallery.photo.create', $post->id) }}" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="bi bi-upload me-1"></i> Upload Infografis Pertama
                        </a>
                    </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $galeris->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
