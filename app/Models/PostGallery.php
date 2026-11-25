<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PostGallery extends Model
{
    use HasFactory;

    protected $table = 'post_galleries';

    protected $fillable = [
        'post_id',
        'nama_galeri',
        'galeri_seo',
        'foto',
        'keterangan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($gallery) {
            if (empty($gallery->galeri_seo) && ! empty($gallery->nama_galeri)) {
                $gallery->galeri_seo = Str::slug($gallery->nama_galeri);
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
