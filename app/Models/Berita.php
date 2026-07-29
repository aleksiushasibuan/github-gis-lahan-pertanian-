<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'isi',
        'gambar',
        'status',
        'views'
    ];

    // 🔥 AUTO GENERATE SLUG UNIK
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            $slug = Str::slug($berita->judul);

            $originalSlug = $slug;
            $count = 1;

            // cek kalau slug sudah ada
            while (self::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $berita->slug = $slug;
        });
    }
}