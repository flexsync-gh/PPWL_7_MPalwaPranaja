<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'post_seo',
        'description',
        'picture',
        'category',
    ];

    /**
     * Boot method to auto-generate SEO slug when creating or updating.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($post) {
            if (empty($post->post_seo) && ! empty($post->title)) {
                $slug = Str::slug($post->title);
                // Pastikan slug unik jika sudah ada
                $count = static::where('post_seo', 'LIKE', "{$slug}%")
                    ->where('id', '!=', $post->id ?? 0)
                    ->count();
                $post->post_seo = $count ? "{$slug}-{$count}" : $slug;
            }
        });
    }

    /**
     * Relasi ke galeri foto / infografis pendukung topik keuangan (Sesuai Bab X)
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PostGallery::class, 'post_id', 'id');
    }

    /**
     * Accessor for original_pict to match the practicum slide view.
     */
    public function getOriginalPictAttribute(): string
    {
        return $this->picture
            ? asset('storage/posts_image/'.$this->picture)
            : asset('storage/posts_image/noimage.png');
    }
}
