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

        // Use asset() pointing directly to the uploads folder, bypassing symlinks entirely
        return asset('uploads/' . $this->image);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
