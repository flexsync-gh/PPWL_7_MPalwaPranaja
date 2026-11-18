<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;
    public function test_gallery_index_screen_can_be_rendered(): void
    {
        $response = $this->get('/gallery');

        $response->assertStatus(200);
        $response->assertSee('Galeri Foto');
    }

    public function test_gallery_create_screen_can_be_rendered(): void
    {
        $response = $this->get('/gallery/create');

        $response->assertStatus(200);
        $response->assertSee('Upload Foto Baru');
    }

    public function test_can_upload_new_gallery_post(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('nature.jpg', 640, 480);

        $response = $this->post('/gallery', [
            'title' => 'Pemandangan Indah',
            'description' => 'Foto pemandangan alam pegunungan',
            'picture' => $file,
        ]);

        $response->assertRedirect('/gallery');
        $this->assertDatabaseHas('posts', [
            'title' => 'Pemandangan Indah',
            'description' => 'Foto pemandangan alam pegunungan',
        ]);

        $post = Post::where('title', 'Pemandangan Indah')->first();
        $this->assertNotNull($post->picture);
        Storage::disk('public')->assertExists('posts_image/' . $post->picture);
    }

    public function test_can_edit_and_update_gallery_post(): void
    {
        Storage::fake('public');

        $fileOld = UploadedFile::fake()->image('old.jpg');
        $fileOld->storeAs('posts_image', 'old_file.jpg', 'public');

        $post = Post::create([
            'title' => 'Judul Lama',
            'description' => 'Deskripsi Lama',
            'picture' => 'old_file.jpg',
        ]);

        $response = $this->get("/gallery/{$post->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Foto Galeri');

        $fileNew = UploadedFile::fake()->image('new.jpg');

        $updateResponse = $this->put("/gallery/{$post->id}", [
            'title' => 'Judul Baru',
            'description' => 'Deskripsi Baru',
            'picture' => $fileNew,
        ]);

        $updateResponse->assertRedirect('/gallery');
        $post->refresh();

        $this->assertEquals('Judul Baru', $post->title);
        $this->assertEquals('Deskripsi Baru', $post->description);
        Storage::disk('public')->assertMissing('posts_image/old_file.jpg');
        Storage::disk('public')->assertExists('posts_image/' . $post->picture);
    }

    public function test_can_delete_gallery_post(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('to_delete.jpg');
        $file->storeAs('posts_image', 'to_delete.jpg', 'public');

        $post = Post::create([
            'title' => 'Foto Dihapus',
            'description' => 'Akan segera dihapus',
            'picture' => 'to_delete.jpg',
        ]);

        $response = $this->delete("/gallery/{$post->id}");
        $response->assertRedirect('/gallery');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing('posts_image/to_delete.jpg');
    }
}
