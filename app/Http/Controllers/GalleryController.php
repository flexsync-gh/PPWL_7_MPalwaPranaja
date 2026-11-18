<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999',
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid() . time();
            $smallFilename = "small_{$basename}.{$extension}";
            $mediumFilename = "medium_{$basename}.{$extension}";
            $largeFilename = "large_{$basename}.{$extension}";
            $filenameSimpan = "{$basename}.{$extension}";
            
            // Simpan ke disk public agar dapat diakses via asset('storage/posts_image/...')
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan, 'public');
        } else {
            $filenameSimpan = 'noimage.png';
        }

        $post = new Post();
        $post->picture = $filenameSimpan;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->save();

        return redirect('gallery')
            ->with('pesan', 'Berhasil menambahkan data baru')
            ->with('success', 'Berhasil menambahkan data baru');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);
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
            'picture' => 'image|nullable|max:1999',
        ]);

        $post->title = $request->input('title');
        $post->description = $request->input('description');

        if ($request->hasFile('picture')) {
            // Hapus gambar lama jika ada dan bukan noimage.png
            if ($post->picture && $post->picture !== 'noimage.png' && Storage::disk('public')->exists('posts_image/' . $post->picture)) {
                Storage::disk('public')->delete('posts_image/' . $post->picture);
            }

            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid() . time();
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
        if ($post->picture && $post->picture !== 'noimage.png' && Storage::disk('public')->exists('posts_image/' . $post->picture)) {
            Storage::disk('public')->delete('posts_image/' . $post->picture);
        }

        $post->delete();

        return redirect('gallery')
            ->with('pesan', 'Berhasil menghapus foto dari galeri')
            ->with('success', 'Berhasil menghapus foto dari galeri');
    }
}
