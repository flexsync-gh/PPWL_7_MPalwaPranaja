<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostGallery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'id' => 'posts',
            'menu' => 'Gallery',
            'galleries' => Post::where('picture', '!=', '')
                ->whereNotNull('picture')
                ->orderBy('created_at', 'desc')
                ->paginate(30),
        ];

        return view('gallery.index')->with($data);
    }

    /**
     * Halaman Publik Edukasi Keuangan & Infografis Finansial (SEO Optimized)
     */
    public function financialIndex()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(9);

        return view('gallery.edukasi-index', compact('posts'));
    }

    /**
     * Halaman Detail Topik Finansial berdasarkan SEO Slug (Sesuai Bab X)
     */
    public function financialDetail(string $title)
    {
        // Cari berdasarkan post_seo (slug ramah mesin pencari) atau id fallback
        $post = Post::where('post_seo', $title)
            ->orWhere(function ($query) use ($title) {
                if (is_numeric($title)) {
                    $query->where('id', (int) $title);
                }
            })
            ->first();

        if (! $post) {
            abort(404, 'Topik edukasi finansial tidak ditemukan.');
        }

        // Relasi galeri foto / infografis terkait dengan paginasi (sesuai Bab X)
        $galeris = $post->photos()->orderBy('id', 'desc')->paginate(6);

        return view('gallery.detail-seo', compact('post', 'galeris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage with auto SEO Slug.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category' => 'nullable|string|max:100',
            'picture' => 'image|nullable|max:1999',
        ]);

        if ($request->hasFile('picture')) {
            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid().time();
            $filenameSimpan = "{$basename}.{$extension}";

            // Simpan ke disk public agar dapat diakses via asset('storage/posts_image/...')
            $request->file('picture')->storeAs('posts_image', $filenameSimpan, 'public');
        } else {
            $filenameSimpan = 'noimage.png';
        }

        $post = new Post;
        $post->picture = $filenameSimpan;
        $post->title = $request->input('title');
        $post->post_seo = Str::slug($request->input('title'));
        $post->category = $request->input('category', 'Tips Finansial');
        $post->description = $request->input('description');
        $post->save();

        return redirect('gallery')
            ->with('pesan', 'Berhasil menambahkan data baru')
            ->with('success', 'Berhasil menambahkan konten finansial baru dengan SEO slug: '.$post->post_seo);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Jika parameter berupa slug atau id, cari data
        $post = Post::where('post_seo', $id)
            ->orWhere(function ($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('id', (int) $id);
                }
            })
            ->firstOrFail();

        // Alihkan ke URL SEO friendly jika memiliki galeri infografis
        if ($post->photos()->exists()) {
            return redirect()->route('fintrack.edukasi.detail', $post->post_seo ?: $post->id);
        }

        return view('gallery.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);

        return view('gallery.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category' => 'nullable|string|max:100',
            'picture' => 'image|nullable|max:1999',
        ]);

        $post->title = $request->input('title');
        $post->post_seo = Str::slug($request->input('title'));
        if ($request->has('category')) {
            $post->category = $request->input('category');
        }
        $post->description = $request->input('description');

        if ($request->hasFile('picture')) {
            // Hapus gambar lama jika ada dan bukan noimage.png
            if ($post->picture && $post->picture !== 'noimage.png' && Storage::disk('public')->exists('posts_image/'.$post->picture)) {
                Storage::disk('public')->delete('posts_image/'.$post->picture);
            }

            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid().time();
            $filenameSimpan = "{$basename}.{$extension}";
            $request->file('picture')->storeAs('posts_image', $filenameSimpan, 'public');

            $post->picture = $filenameSimpan;
        }

        $post->save();

        return redirect('gallery')
            ->with('pesan', 'Berhasil memperbarui data galeri')
            ->with('success', 'Berhasil memperbarui data galeri');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        // Hapus file fisik gambar jika ada
        if ($post->picture && $post->picture !== 'noimage.png' && Storage::disk('public')->exists('posts_image/'.$post->picture)) {
            Storage::disk('public')->delete('posts_image/'.$post->picture);
        }

        $post->delete();

        return redirect('gallery')
            ->with('pesan', 'Berhasil menghapus foto dari galeri')
            ->with('success', 'Berhasil menghapus foto dari galeri');
    }

    /**
     * Form untuk menambahkan foto/infografis ke dalam topik finansial tertentu
     */
    public function createGalleryPhoto(string $id)
    {
        $post = Post::findOrFail($id);

        return view('gallery.tambah-foto', compact('post'));
    }

    /**
     * Simpan foto galeri baru yang berelasi dengan post
     */
    public function storeGalleryPhoto(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'nama_galeri' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $extension = $request->file('foto')->getClientOriginalExtension();
        $basename = 'gallery_'.uniqid().time();
        $filenameSimpan = "{$basename}.{$extension}";
        $request->file('foto')->storeAs('posts_image', $filenameSimpan, 'public');

        PostGallery::create([
            'post_id' => $post->id,
            'nama_galeri' => $request->nama_galeri,
            'galeri_seo' => Str::slug($request->nama_galeri),
            'keterangan' => $request->keterangan,
            'foto' => $filenameSimpan,
        ]);

        return redirect()->route('fintrack.edukasi.detail', $post->post_seo ?: $post->id)
            ->with('success', 'Infografis baru berhasil ditambahkan ke galeri topik '.$post->title);
    }

    /**
     * Menghasilkan XML Sitemap dinamis untuk Search Engine Crawlers (Googlebot, Bingbot)
     */
    public function sitemap(): Response
    {
        $baseUrl = url('/');
        $posts = Post::orderBy('updated_at', 'desc')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

        // 1. Landing Page FinTrack
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars($baseUrl).'</loc>';
        $xml .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // 2. Edukasi Keuangan List Page
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars(route('fintrack.edukasi')).'</loc>';
        $xml .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // 3. Gallery FinTrack
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars(route('gallery.index')).'</loc>';
        $xml .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';

        // 4. Tiap Konten Finansial dengan Clean SEO Slug
        foreach ($posts as $post) {
            $slug = $post->post_seo ?: $post->id;
            $postUrl = route('fintrack.edukasi.detail', $slug);
            $lastmod = $post->updated_at ? $post->updated_at->format('Y-m-d') : date('Y-m-d');

            $xml .= '<url>';
            $xml .= '<loc>'.htmlspecialchars($postUrl).'</loc>';
            $xml .= '<lastmod>'.$lastmod.'</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.85</priority>';
            $xml .= '</url>';
        }

        // 5. Autentikasi Publik (Login & Register)
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars(route('login')).'</loc>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.5</priority>';
        $xml .= '</url>';

        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars(route('register')).'</loc>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.5</priority>';
        $xml .= '</url>';

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
