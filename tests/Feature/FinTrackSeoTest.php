<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostGallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinTrackSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_table_has_post_seo_and_category_columns(): void
    {
        $post = Post::create([
            'title' => 'Strategi Investasi FinTrack 2026',
            'post_seo' => 'strategi-investasi-fintrack-2026',
            'category' => 'Investasi',
            'description' => 'Panduan investasi reksadana dan saham.',
            'picture' => 'invest.jpg',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Strategi Investasi FinTrack 2026',
            'post_seo' => 'strategi-investasi-fintrack-2026',
            'category' => 'Investasi',
        ]);
    }

    public function test_post_automatically_generates_seo_slug(): void
    {
        $post = Post::create([
            'title' => 'Cara Mudah Mencatat Uang Keluar!',
            'description' => 'Tips pencatatan harian.',
        ]);

        $this->assertEquals('cara-mudah-mencatat-uang-keluar', $post->post_seo);
    }

    public function test_post_photos_relationship_returns_galleries(): void
    {
        $post = Post::create([
            'title' => 'Tips Menabung Gaji',
            'description' => 'Alokasi 20% gaji.',
        ]);

        $gallery = PostGallery::create([
            'post_id' => $post->id,
            'nama_galeri' => 'Bagan Alur Menabung',
            'keterangan' => 'Diagram menabung otomatis di awal bulan.',
            'foto' => 'bagan.png',
        ]);

        $this->assertCount(1, $post->photos);
        $this->assertEquals('Bagan Alur Menabung', $post->photos->first()->nama_galeri);
        $this->assertEquals('bagan-alur-menabung', $gallery->galeri_seo);
    }

    public function test_edukasi_keuangan_list_screen_can_be_rendered(): void
    {
        $post = Post::create([
            'title' => 'Tips Arus Kas FinTrack',
            'description' => 'Deskripsi panduan arus kas.',
            'category' => 'Arus Kas',
            'picture' => 'cashflow.png',
        ]);

        $response = $this->get('/edukasi-keuangan');

        $response->assertStatus(200);
        $response->assertSee('Edukasi & Tips Manajemen Keuangan', false);
        $response->assertSee('Tips Arus Kas FinTrack');
        $response->assertSee('/edukasi-keuangan/'.$post->post_seo);
    }

    public function test_edukasi_keuangan_detail_screen_can_be_accessed_via_seo_slug(): void
    {
        $post = Post::create([
            'title' => 'Audit Keuangan Bulanan',
            'description' => 'Langkah memeriksa kebocoran pengeluaran bulanan.',
            'category' => 'Budgeting',
            'picture' => 'audit.jpg',
        ]);

        PostGallery::create([
            'post_id' => $post->id,
            'nama_galeri' => 'Checklist Evaluasi Pengeluaran',
            'keterangan' => 'Template daftar periksa pos belanja.',
            'foto' => 'checklist.jpg',
        ]);

        // Akses melalui clean SEO friendly slug URL
        $response = $this->get('/edukasi-keuangan/'.$post->post_seo);

        $response->assertStatus(200);
        $response->assertSee('Audit Keuangan Bulanan');
        $response->assertSee('Checklist Evaluasi Pengeluaran');
        $response->assertSee('data-lightbox="album-keuangan"', false);
        $response->assertSee('dist/css/lightbox.min.css');
        $response->assertSee('dist/js/lightbox-plus-jquery.min.js');
        // Schema.org structured data check
        $response->assertSee('"@type": "Article"', false);
    }

    public function test_sitemap_xml_can_be_generated(): void
    {
        $post = Post::create([
            'title' => 'Dana Darurat FinTrack',
            'description' => 'Cara menyiapkan dana darurat.',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/edukasi-keuangan/'.$post->post_seo);
    }

    public function test_can_upload_new_gallery_photo_for_post(): void
    {
        Storage::fake('public');

        $post = Post::create([
            'title' => 'Topik Baru',
            'description' => 'Deskripsi topik baru.',
        ]);

        $file = UploadedFile::fake()->image('diagram.png');

        $response = $this->post("/gallery/{$post->id}/upload-foto", [
            'nama_galeri' => 'Diagram Alur Transaksi',
            'keterangan' => 'Penjelasan diagram alur transaksi keuangan',
            'foto' => $file,
        ]);

        $response->assertRedirect('/edukasi-keuangan/'.$post->post_seo);
        $this->assertDatabaseHas('post_galleries', [
            'post_id' => $post->id,
            'nama_galeri' => 'Diagram Alur Transaksi',
            'galeri_seo' => 'diagram-alur-transaksi',
        ]);
    }
}
