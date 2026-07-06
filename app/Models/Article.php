<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'category',
        'user_id',
    ];

    /**
     * Get the full URL for the article's cover image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Gunakan rute berkas.serve yang lebih kebal terhadap masalah cache config di shared hosting
        return route('berkas.serve', ['path' => $this->image]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
